<?php
/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-03-27
 * Time: 10:45
 */

namespace Kernel;


use App\Controller;

class Bonwenium
{
    private $argv = [];
    private $isCLI;
    private $spaceStr;
    public function __construct($argv = null)
    {
        if ($argv === null){
            self::setCommendArgv();
        }

        $this->isCLI = php_sapi_name() == 'cli';
        $this->spaceStr = $this->isCLI ? "\n" : "<br/>";
    }

    private function setCommendArgv(){
        $args = $_SERVER['argv'];
        if (count($args) > 1){
            unset($args[0]);
        }
        $this->argv = array_values($args);
    }

    public function handle(){
        if ($this->isCLI){
            self::handle_cli();
        }else{
            self::handle_cgi();
        }
    }

    public function handle_cli(){
        if (count($this->argv) == 0){
            return;
        }

        if ($this->argv[0] == 'list'){
            self::echoList();
            return;
        }

        $argvs = array_filter($this->argv);
        foreach ($argvs as $step){
            if (!self::checkStep($step)){
                return;
            }
        }

        self::echoDriverBegin();
        $driver = (new CreateWebDriver())->drive();
        foreach ($argvs as $step){
            self::handleStep($driver,$step);
        }

        CloseWebDriver::doClose($driver);
        self::echoDriverClose();
    }

    public function handle_cgi(){
        if ($_SERVER['REQUEST_URI'] == "/"){
            echo "please send 'list' or any step name as uri!";
            return;
        }

        if ($_SERVER['REQUEST_URI'] == '/list'){
            self::echoList();
        }

        $argvs = array_filter(explode('/',$_SERVER['REQUEST_URI']));
        foreach ($argvs as $step){
            if (!self::checkStep($step)){
                return;
            }
        }

        self::echoDriverBegin();
        $driver = (new CreateWebDriver())->drive();
        foreach ($argvs as $step){
            self::handleStep($driver,$step);
        }

        CloseWebDriver::doClose($driver);
        self::echoDriverClose();
    }

    private function echoList(){
        if ($this->isCLI){
            echo "Available commands:{$this->spaceStr}{$this->spaceStr}";
        }else{
            echo "Available uris:{$this->spaceStr}{$this->spaceStr}";
        }
        foreach (self::getList() as $value){
            echo $value.$this->spaceStr;
        }
        die();
    }

    private function echoDriverBegin(){
        echo "driver starting ...{$this->spaceStr}";
    }

    private function echoDriverClose(){
        echo "driver closed .{$this->spaceStr}";
    }

    private function getList(){
        $files = scandir(steps_path());
        $return = [];
        foreach ($files as $file){
            $arr = explode('.php',$file);
            if (count($arr) == 2 && empty($arr[1])){
                $return []= $this->isCLI ? "php bonwenium {$arr[0]}" : "/{$arr[0]}";
            }
        }
        return $return;
    }

    private function checkStep($stepName){
        $files = scandir(steps_path());
        if (!in_array($stepName.'.php',$files)){
            echo "command not found {$this->spaceStr}";
            return false;
        }
        return true;
    }

    private function handleStep($driver,$stepName){
        echo "steping  {$stepName} ...{$this->spaceStr}";
        (new Controller($driver))->handle($stepName);
    }

}