<?php
namespace Kernel;
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

        $this->driver = RemoteWebDriver::create($host, $capabilities, $timeOut);
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