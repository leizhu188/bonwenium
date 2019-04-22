<?php
namespace Kernel;
use Bonwe\WebDriver\Chrome\ChromeOptions;
use Bonwe\WebDriver\Exception\WebDriverException;
use Bonwe\WebDriver\Remote\DesiredCapabilities;
use Bonwe\WebDriver\Remote\RemoteWebDriver;
use Bonwe\WebDriver\Remote\WebDriverCapabilityType;

/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-03-19
 * Time: 13:55
 */

class CreateWebDriver{

    private $driver;

    public function __construct()
    {
        $browser = env('browser','chrome');
        $host           = config("web_driver.{$browser}.host");
        $timeOut        = config("web_driver.{$browser}.timeout");
        $capabilities = DesiredCapabilities::$browser();

        //chrome浏览器修改useragent
        if (env('USER_AGENT_RANDOM',true) && $browser == 'chrome'){
            $useragent = self::randomUserAgent();
            $options = new ChromeOptions();
            $options->addArguments(["user-agent={$useragent}"]);
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        }

        //使用代理ip
        if (env('PROXY_IP_RANDOM',false)){
            $ip = self::randomProxyIp();
            $capabilities->setCapability(WebDriverCapabilityType::PROXY,[
                'proxyType' => 'manual',
                'httpProxy' => $ip,
                'sslProxy' => $ip
            ]);
        }

        //如果当前已经有打开的浏览器
        $sessionIds = RemoteWebDriver::getAllSessions($host);
        foreach ($sessionIds as $sessionId){
            try{
                $this->driver = RemoteWebDriver::createBySessionID($sessionId['id'],$host);
                $this->driver->getTitle();
                break;
            }catch (WebDriverException $e){
                $this->driver = null;
                continue;
            }
        }

        if (!$this->driver){
            $this->driver = RemoteWebDriver::create($host, $capabilities, $timeOut);
        }
    }

    private function randomUserAgent(){
        $num = random_int(0,count($userAgents = config('setting.userAgents'))-1);
        return $userAgents[$num];
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