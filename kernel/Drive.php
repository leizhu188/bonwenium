<?php
namespace Kernel;


use Bonwe\WebDriver\Remote\RemoteWebDriver;

class Drive
{
    private $driver;

    public function __construct(RemoteWebDriver $driver)
    {
        $this->driver = $driver;
    }

    public function handle()
    {
        $enterUrl = env('index_url');

        $stepDriver = $this->driver->get($enterUrl);

        return $stepDriver;
    }

}
