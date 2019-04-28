<?php

namespace Kernel;

use Bonwe\WebDriver\Remote\RemoteWebDriver;
use Bonwe\WebDriver\WebDriverBy;
use Predis\Client;

class Controller
{
    public $driver;
    private $redis;
    private $mysql;
    public function __construct(RemoteWebDriver $driver)
    {
        $this->driver = $driver;
    }

    public function redis(){
        if (!$this->redis){
            $this->redis = new Client([
                'scheme' => 'tcp',
                'host'   => env('REDIS_HOST','127.0.0.1'),
                'port'   => env('REDIS_PORT',6379),
            ]);
        }
        return $this->redis;
    }

    public function mysql(){
        if (!$this->mysql){
            $this->mysql = new Mysql();
        }
        return $this->mysql;
    }

    /**
     * step 结构：
     * [
     * 执行不成功则不通过
     *  'must_step'    => "class:bodyer>>tag:input>num:1>>>write:123456",
     * 执行不成功也算通过
     *  'should_step'    => "class:bodyer>>tag:input>num:1>>>write:123456",
     * 断言
     *  'asserts'   => [
     *  ],
     * 检查错误提示框（值为'操作成功'时算通过）
     *  'check_msg' => '操作成功',
     * 休眠
     * 'sleep'=>3,
     * 'usleep'=>500000,
     * ],
     * ]
     */
    public function handle($stepName)
    {
        $steps = [];
        foreach (step($stepName) as $module){
            $steps []= $module;
        }

        self::handleSteps($steps);
    }

    /*
     * 支持无限层级混级 数组遍历 步骤执行
     */
    public function handleSteps($steps){
        if (!is_array($steps)){
            return;
        }
        if (!isset($steps['type'])){
            foreach ($steps as $step){
                self::handleSteps($step);
            }
            return;
        }

        switch ($steps['type']){
            case 'sleep':
                sleep((int)$steps['value']);break;
            case 'usleep':
                usleep((int)$steps['value']);break;
            case 'scroll':
                self::doScroll($steps['value']);break;
            case 'step':
                self::doStep($steps);break;
            case 'until':
                self::doUntils([$steps]);break;
            case 'untils':
                self::doUntils($steps['list']);break;
            case 'assert':
                self::doAsserts([$steps]);break;
            case 'asserts':
                self::doAsserts($steps['list']);break;
            case 'check_msg':
                self::checkError($steps);break;
            case 'function':
                self::doFunction($steps['value']);break;
        }
    }

    /**
     * @param $step
     * 无参数： 例 User@test
     * 有参数，仅能传一个参数，用json格式： 例 User@test@{'phone'=>18888888888}
     */
    protected function doFunction($step){
        $arr = explode('@',$step);
        $class  = config('setting.function_namespace').'\\'.$arr[0];
        $method = $arr[1];
        $a = new $class($this->driver);
        if (isset($arr[2])){
            $a->setDatas(json_decode($arr[2],true));
        }
        $a->$method();
    }

    const PATH_TYPE = ['bw-path','x-path'];
    /*
     * 根据字符串（bw-path/x-path）获取元素对象
     */
    protected function getElementByStr($str){
        if (count($arr = explode(':',$str)) < 2 || !in_array($arr[0],self::PATH_TYPE)){
            return null;
        }

        $path = substr($str,strlen($arr[0])+1);

        $func_name = 'getElementByStr_'.explode('-',$arr[0])[0];
        return self::$func_name($path);
    }

    protected function getElementByStr_bw($path){
        if (empty($path)){
            return [];
        }

        $aSteps = self::stepStrToArr($path);
        $actionElement = $this->driver;
        foreach ($aSteps as $k=>$aStep) {
            if (!is_array($actionElement)) {
                $actionElement = [$actionElement];
            }
            switch ($aStep['do']) {
                case '>'    :
                    try{
                        $actionElement = self::analysisElements_property($actionElement, $aStep['str']);
                    }catch (\Exception $e){
                        monolog($e->getMessage(),'bonwenium.log');
                        $actionElement = [];
                    }
                    break;
                case '>>'   :
                    try{
                        $actionElement = self::analysisElements_next($actionElement, $aStep['str']);
                    }catch (\Exception $e){
                        monolog($e->getMessage(),'bonwenium.log');
                        $actionElement = [];
                    }
                    break;
                case '>>>'   :
                    break;
                default :
                    $actionElement = [];
            }
        }

        return $actionElement;
    }

    protected function getElementByStr_x($path){
        if (empty($path)){
            return [];
        }

        try{
            return [$this->driver->findElement(WebDriverBy::xpath($path))];
        }catch (\Exception $e){
            monolog($e->getMessage(),'bonwenium.log');
            return [];
        }
    }


    protected function  doStep($step){
        $level = $step['level'] ?? 'must';

        $actionElement = self::getElementByStr($step['path']);

        if ($level == 'should' && count($actionElement) == 0){
            return;
        }

        $repeatCount = 0;
        do{
            try{
                $aStepDo = explode(':',$step['value']);
                switch ($aStepDo[0]){
                    case 'write':
                        $actionElement[0]->clear();
                        $actionElement[0]->sendKeys($aStepDo[1],env('browser','safari'));break;
                    case 'append':
                        $actionElement[0]->sendKeys($aStepDo[1],env('browser','safari'));break;
                    case 'click':
                        if (isset($aStepDo[1])){
                            switch ($aStepDo[1]){
                                case 'move':
                                    $this->driver->getMouse()->mouseMove($actionElement[0]->getCoordinates());
                                    break;
                                case 'down':
                                    $this->driver->getMouse()->mouseDown($actionElement[0]->getCoordinates());
                                    break;
                                case 'up':
                                    $this->driver->getMouse()->mouseUp($actionElement[0]->getCoordinates());
                                    break;
                            }
                        }else{
                            $actionElement[0]->click();break;
                        }
                }
                $repeatCount = config('app.step_repeat_times');
            }catch (\Exception $e){
                ++$repeatCount;
                monolog($e->getMessage(),'bonwenium.log');
                self::saveLog($step['type'],'no',$step['path'],$step['value'],"catch exception try {$repeatCount} times");
                usleep(500000);
                continue;
            }catch (\Error $e){
                ++$repeatCount;
                monolog($e->getMessage(),'bonwenium.log');
                self::saveLog('step','no',$step['path'],$step['value'],"catch error try {$repeatCount} times");
                usleep(500000);
                continue;
            }
            self::saveLog('step','ok',$step['path'],$step['value'],'');
        }while($repeatCount < config('app.step_repeat_times'));


    }

    protected function stepStrToArr($step){
        $aSteps = [];
        while ($step){
            $aActionType = "";
            while ($step[0] == '>'){
                $aActionType .= '>';
                $step = substr($step,1);
                if (!$step){
                    break;
                }
            }
            $str = "";
            while ($step[0] != '>'){
                $str .= $step[0];
                $step = substr($step,1);
                if (!$step){
                    break;
                }
            }
            $aSteps []= ['do'=>$aActionType,'str'=>$str];
        }

        return $aSteps;
    }


    /*
     * 分析并执行 后代元素查找
     * 例： >tag:a
     */
    protected function analysisElements_next($elements,$str){
        if (empty($str)){
            return $elements;
        }

        $k = explode(':',$str)[0];
        $v = explode(':',$str)[1];
        $return = [];
        foreach ($elements as $element){
            switch ($k){
                case 'id'       : $by = WebDriverBy::id($v);break;
                case 'class'    : $by = WebDriverBy::className($v);break;
                case 'tag'      : $by = WebDriverBy::tagName($v);break;
            }

            $actionElements = $element->findElements($by);
            if (!empty($actionElements)){
                $return = array_merge($return,$actionElements);
            }
        }

        return $return;
    }

    /*
     * 分析并执行 元素内查找
     * 例： >text:题库
     */
    protected function analysisElements_property($elements,$str){
        $strArr = explode(':',$str);
        $return = [];
        foreach ($elements as $num => $element){
            switch ($strArr[0]){
                case 'tag'  : $search = $element->getTagName();break;
                case 'id'   : $search = $element->getID();break;
                case 'text' : $search = trim($element->getText());break;
                case 'num'  : $search = $num;break;
                case 'css'  : $search = $element->getCSSValue($strArr[1]);break;
                default:
                    $search = $element->getAttribute($strArr[0]);
            }
            if ($search == $strArr[count($strArr)-1]){
                $return []= $element;
            }
        }
        return $return;
    }

    protected function doAsserts($asserts){
        foreach ($asserts as $assert){
            self::doAssert($assert);
        }
    }

    protected function doAssert($assert){
        try {
            $actionElement = self::getElementByStr($assert['path']);
            switch ($assert['value']) {
                case 'exist':
                    if (count($actionElement) == 0) {
                        throw new \Exception('exist');
                    }
                    break;
                case 'no_exist':
                    if (count($actionElement) > 0) {
                        throw new \Exception('no_exist');
                    }
                    break;
            }
        } catch (\Exception $e) {
            monolog($e->getMessage(),'bonwenium.log');
            self::saveLog('assert', 'no', $assert['path'], $e->getMessage(), 'throw exception');
            return;
        } catch (\Error $e) {
            monolog($e->getMessage(),'bonwenium.log');
            self::saveLog('assert', 'no', $assert['path'], $e->getMessage(), 'throw wrong');
            return;
        }
        self::saveLog('assert','ok',$assert['path'],$assert['value'],'');
    }

    /*
     * 移动滚动条
     */
    protected function doScroll($scroll){
        $this->driver->executeScript("window.scrollTo({$scroll});",[]);
    }

    /*
     * 等待元素 出现 | 消失
     */
    protected function doUntils($untils){
        usleep(config('app.until_ready',0));

        foreach ($untils as $until){
            self::doUntil($until);
        }
    }

    /*
     * 等待元素 出现 | 消失
     */
    protected function doUntil($until){
        $beginTime = time();
        $isContinue = true;
        do{
            $actionElement = self::getElementByStr($until['path']);

            switch ($until['value']) {
                case 'appear':
                    if (count($actionElement) > 0){
                        $isContinue = false;
                    }
                    break;
                case 'disappear':
                    if (count($actionElement) == 0){
                        $isContinue = false;
                    }
                    break;
            }

            usleep(100000);
        }while($isContinue && (time()-$beginTime < config('app.until_timeout')));
        if ($isContinue){
            self::saveLog('until','no',$until['path'],$until['value'],'timeout');
        }else{
            self::saveLog('until','ok',$until['path'],$until['value'],(time()-$beginTime).'s');
        }
    }

    /*
     * 检测error_msg弹框
     */
    protected function checkError($rightMessage = null){
        try{
            $messageElement = $this->driver->findElement(WebDriverBy::className('el-message'));
        }catch (\Exception $e){
            monolog($e->getMessage(),'bonwenium.log');
            if (empty($rightMessage)){
                self::saveLog('check_msg','ok',$rightMessage,'','');
                return true;
            }else{
                self::saveLog('check_msg','no',$rightMessage,$rightMessage,'');
                return false;
            }
        }

        if (empty($messageElement)){
            self::saveLog('check_msg','ok',$rightMessage,'','');
            return true;
        }

        $message = $messageElement->findElement(WebDriverBy::tagName('p'))->getText();
        $message = trim(trim($message),'<!---->');

        if ($rightMessage && $message == $rightMessage){
            self::saveLog('check_msg','ok',$rightMessage,'','');
            return true;
        }

        self::saveLog('check_msg','no',$rightMessage,$rightMessage,$message);
        return false;
    }

    public function saveLog($stepType,$status,$stepStr,$expect,$result){
        switch (env('log_output')){
            case 'log_file' :
                monolog(
                    str_pad($stepType,10," ")
                    .str_pad($status,5," ")
                    .'expect:'.str_pad($expect,20," ")
                    .'result:'.str_pad($result,20," ")
                    .$stepStr
                );
                break;
            case 'mysql':
                break;
        }
    }
}
