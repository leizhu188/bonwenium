<?php
namespace Kernel;
use Bonwe\WebDriver\Chrome\ChromeOptions;
use Bonwe\WebDriver\Remote\DesiredCapabilities;
use Bonwe\WebDriver\Remote\RemoteWebDriver;
use Bonwe\WebDriver\Remote\WebDriverCapabilityType;
use Predis\Client;
use Predis\Connection\ConnectionException;

/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-03-19
 * Time: 13:55
 */

class CreateWebDriver{

    private $driver;
    private $host;
    private $browserName;
    private $timeOut;
    CONST REDIS_SESSION_ID = "driver_session_id";

    public function __construct()
    {
        $browser            = env('browser','chrome');
        $this->browserName  = $browser;
        $this->host         = config("web_driver.{$browser}.host");
        $this->timeOut      = config("web_driver.{$browser}.timeout");
        $capabilities       = DesiredCapabilities::$browser();



        self::setOptions($capabilities);

        //使用代理ip
        self::setIpProxy($capabilities);

        //如果当前已经有打开的浏览器
        $sessionRs = self::createDriverBySessionId();

        if (!$sessionRs){
            self::createNewDriver($capabilities);
        }

    }

    private function randomUserAgent(){
        $num = random_int(0,count($userAgents = config('setting.userAgents'))-1);
        return $userAgents[$num];
    }

    /*
     * 设置useragent,headless
     */
    private function setOptions(DesiredCapabilities $capabilities){
        if ($this->browserName == 'chrome'){
            $options = new ChromeOptions();
            //chrome浏览器修改useragent
            if (env('USER_AGENT_RANDOM',true)){
                $useragent = self::randomUserAgent();
                $options->addArguments(["user-agent={$useragent}"]);
            }
            //chrome浏览器 headless
            if (env('BROWSER_HEADLESS',true)){
                $options->addArguments(["--headless"]);
            }
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        }
    }

    /*
     * 设置ip代理
     */
    private function setIpProxy(DesiredCapabilities $capabilities){
        if (env('PROXY_IP_RANDOM',false)){
            $ip = self::randomProxyIp();
            $capabilities->setCapability(WebDriverCapabilityType::PROXY,[
                'proxyType' => 'manual',
                'httpProxy' => $ip,
                'sslProxy' => $ip
            ]);
        }
    }

    /*
     * 获取session_id并驱动create
     * redis获取session->无效后 驱动器session管理获取session
     * 如果是firefox，session_id从redis取
     */
    private function createDriverBySessionId(){
        //redis服务开启时：从redis取session驱动driver
        if ($redis = $this->redis()){
            $sessionId = $redis->get(self::REDIS_SESSION_ID);
            if ($sessionId){
                $this->driver = RemoteWebDriver::createBySessionID($sessionId,$this->host);
            }
            if ($rs = self::pingDriver()){
                return true;
            }
        }

        //firefox只能从redis取session驱动driver
        if ($this->browserName == 'firefox'){
            return false;
        }

        //redis的session无效时，从驱动器session管理器获取session尝试驱动
        $sessionIds = RemoteWebDriver::getAllSessions($this->host);
        foreach ($sessionIds as $sessionId){
            $this->driver = RemoteWebDriver::createBySessionID($sessionId['id'],$this->host);
            if (self::pingDriver()){
                return true;
            }
        }

        return false;
    }

    /*
     * 测试driver是否连上，没连上清除session_id,driver对象置空
     */
    private function pingDriver(){
        if (empty($this->driver)){
            return false;
        }

        try{
            $title = $this->driver->getTitle();
            if (!is_string($title)){
                throw new \Exception();
            }
        }catch (\Exception $e){
            if ($redis = self::redis()){
                $redis->del([self::REDIS_SESSION_ID]);
            }
            $this->driver = null;
            return false;
        }

        return true;
    }

    /*
     * 创建driver
     * 如果redis存在，将session存入redis
     */
    private function createNewDriver(DesiredCapabilities $capabilities){
        $this->driver = RemoteWebDriver::create($this->host, $capabilities, $this->timeOut);
        if ($redis = self::redis()){
            $sessionId = $this->driver->getSessionID();
            if (!$sessionId){
                $sessionId = $this->driver->getCapabilities()->getCapability('sessionId');
            }

            $redis->set(self::REDIS_SESSION_ID,$sessionId);
        }

        //启动后，根据sessionId再加载一次（兼容部分firefox启动抓不到元素问题）
        self::createDriverBySessionId();
    }

    public function redis(){
        $redis = new Client([
            'scheme' => 'tcp',
            'host'   => env('REDIS_HOST','127.0.0.1'),
            'port'   => env('REDIS_PORT',6379),
        ]);

        try{
            $ping = $redis->ping()->getPayload();
            if ($ping != "PONG"){
                return null;
            }
        }catch (ConnectionException $e){
            return null;
        }

        return $redis;
    }

    private function randomProxyIp(){
        $num = random_int(0,count($ips = config('setting.proxy_ips'))-1);
        return $ips[$num];
    }

    /**
     * @return \Bonwe\WebDriver\Remote\RemoteWebDriver
     */
    public function drive()
    {
        (new Drive($this->driver))->handle();

        return $this->driver;
    }

    /**
     * @return \Bonwe\WebDriver\Remote\RemoteWebDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

}