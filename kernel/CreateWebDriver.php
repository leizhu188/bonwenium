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
        $host           = config('web_driver.host');
        $timeOut        = config('web_driver.timeout');
        $desiredType    = config('web_driver.desired_type');
        switch ($desiredType){
            case 'safari' :
                $capabilities = DesiredCapabilities::safari();
                break;
            case 'chrome' :
                $capabilities = DesiredCapabilities::chrome();
                break;
        }

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