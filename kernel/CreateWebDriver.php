<?php
namespace Kernel;
use Bonwe\WebDriver\Chrome\ChromeOptions;
use Bonwe\WebDriver\Remote\DesiredCapabilities;
use Bonwe\WebDriver\Remote\RemoteWebDriver;

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

        $this->driver = RemoteWebDriver::create($host, $capabilities, $timeOut);
    }

    private function randomUserAgent(){
        $num = random_int(0,count($userAgents = config('setting.userAgents'))-1);
        return $userAgents[$num];
    }

    /**
     * @return \Bonwe\WebDriver\Remote\RemoteWebDriver
     */
    public function drive()
    {
        (new Drive($this->driver))->handle();

        return $this->driver;
    }

}