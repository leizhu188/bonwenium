<?php
/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-03-27
 * Time: 10:45
 */

namespace Kernel;


use App\Controller;

class Selenium
{
    private $argv = [];
    public function __construct($argv = null)
    {
        if ($argv === null){
            self::setCommendArgv();
        }
    }

    private function setCommendArgv(){
        $args = $_SERVER['argv'];
        if (count($args) > 1){
            unset($args[0]);
        }
        $this->argv = array_values($args);
    }

    public function handle(){
        if (count($this->argv) == 0){
            return;
        }

        if ($this->argv[0] == 'list'){
            self::echoList();
            return;
        }

        foreach ($this->argv as $step){
            if (!self::checkStep($step)){
                return;
            }
        }

        $driver = (new CreateWebDriver())->drive();
        foreach ($this->argv as $step){
            self::handleStep($driver,$step);
        }

        CloseWebDriver::doClose($driver);
    }

    private function echoList(){
        foreach (self::getList() as $value){
            echo $value."\n";
        }
    }

    private function getList(){
        $files = scandir(steps_path());
        $return = [];
        foreach ($files as $file){
            $arr = explode('.php',$file);
            if (count($arr) == 2 && empty($arr[1])){
                $return []= "php selenium {$arr[0]}";
            }
        }
        return $return;
    }

    private function checkStep($stepName){
        $files = scandir(steps_path());
        if (!in_array($stepName.'.php',$files)){
            echo "command not found \n";
            return false;
        }
        return true;
    }

    private function handleStep($driver,$stepName){
        (new Controller($driver))->handle($stepName);
    }

}