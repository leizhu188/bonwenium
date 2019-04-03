<?php

namespace App;

use Bonwe\WebDriver\Exception\TimeOutException;
use Bonwe\WebDriver\Remote\RemoteWebDriver;
use Bonwe\WebDriver\WebDriverBy;
use Bonwe\WebDriver\WebDriverExpectedCondition;

class Controller
{
    public $driver;
    public function __construct(RemoteWebDriver $driver)
    {
        $this->driver = $driver;
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
            $steps = array_merge($steps,$module);
        }

        self::handleSteps($steps);
    }

    public function handleSteps($steps){
        foreach ($steps as $step){
            foreach ($step as $type=>$value){
                switch ($type){
                    case 'sleep':
                        sleep((int)$value);break;
                    case 'usleep':
                        usleep($value);break;
                    case 'must_step':
                        self::doStep($value,'must');break;
                    case 'should_step':
                        self::doStep($value,'should');break;
                    case 'until':
                        self::doUntil($value);break;
                    case 'asserts':
                        self::doAsserts($value);break;
                    case 'check_msg':
                        self::checkError($value);break;
                    case 'function':
                        self::doFunction($value);break;
                }
            }
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

    protected function doStep($step,$level = 'must'){
        $aSteps = self::stepStrToArr($step);

        $actionElement = $this->driver;
        try{

            foreach ($aSteps as $k=>$aStep){
                if (!is_array($actionElement)){
                    $actionElement = [$actionElement];
                }

                switch ($aStep['do']){
                    case '>'    : $actionElement = self::analysisElements_property($actionElement,$aStep['str']);break;
                    case '>>'   : $actionElement = self::analysisElements_next($actionElement,$aStep['str']);break;
                    case '>>>'  :
                        if ($level == 'should' && count($actionElement) == 0){
                            break;
                        }
                        $aStepDo = explode(':',$aStep['str']);

                        switch ($aStepDo[0]){
                            case 'write':
                                $actionElement[0]->clear();
                                $actionElement[0]->sendKeys($aStepDo[1]);break;
                            case 'append':
                                $actionElement[0]->sendKeys($aStepDo[1]);break;
                            case 'click':
                                $actionElement[0]->click();break;
                        }
                        break;
                }

                if (count($actionElement) == 0){
                    self::saveLog('step','no',$step,$aStep['str'],'not found');
                }
            }

        }catch (\Exception $e){
            self::saveLog('step','no',$step,$aStep['str'],'catch exception');
            return;
        }catch (\Error $e){
            self::saveLog('step','no',$step,$aStep['str'],'catch error');
            return;
        }
        self::saveLog('step','ok',$step,'','');
    }

    protected function stepStrToArr($step){
        $str = "";
        $aSteps = [];
        $aActionType = '>>';
        for ($i = 0; $i < strlen($step) ; $i++ ){
            if (isset($step[$i-1]) && $step[$i-1] != '>' && $step[$i] == '>'){
                $aSteps []= ['do'=>$aActionType,'str'=>$str];
                $aActionType = '';
                $str = "";
            }
            if ($step[$i] != '>'){
                $str .= $step[$i];
                continue;
            }
            $aActionType .= '>';
        }
        $aSteps []= ['do'=>$aActionType,'str'=>$str];
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
            if (empty($assert)){
                continue;
            }
            $aSteps = self::stepStrToArr($assert);
            $actionElement = $this->driver;
            try{
                foreach ($aSteps as $k=>$aStep) {
                    if (!is_array($actionElement)) {
                        $actionElement = [$actionElement];
                    }

                    switch ($aStep['do']) {
                        case '>'    :
                            $actionElement = self::analysisElements_property($actionElement, $aStep['str']);
                            break;
                        case '>>'   :
                            $actionElement = self::analysisElements_next($actionElement, $aStep['str']);
                            break;
                        case '>>>'  :
                            switch ($aStep['str']) {
                                case 'exist':
                                    if (count($actionElement) == 0){
                                        throw new \Exception('exist');
                                    }
                                    break;
                                case 'no_exist':
                                    if (count($actionElement) > 0){
                                        throw new \Exception('no_exist');
                                    }
                                    break;
                            }
                            break;
                    }
                }
            }catch (\Exception $e){
                self::saveLog('assert','no',$assert,$e->getMessage(),'throw exception');
                continue;
            }catch (\Error $e){
                self::saveLog('assert','no',$assert,$e->getMessage(),'throw wrong');
                continue;
            }

            self::saveLog('assert','ok',$assert,'','');
        }
    }

    protected function doUntil($until){
        if (empty($until)){
            return;
        }
        $aSteps = self::stepStrToArr($until);
        switch ($aSteps[count($aSteps)-1]['str']) {
            case 'appear':
                self::until_appear($aSteps[count($aSteps)-2]['str']);
                break;
            case 'disappear':
                self::until_disappear($aSteps[count($aSteps)-2]['str']);
                break;
        }

        self::saveLog('until','ok',$until,'','');
    }

    /*
     * 检测error_msg弹框
     */
    protected function checkError($rightMessage = null){
        try{
            $messageElement = $this->driver->findElement(WebDriverBy::className('el-message'));
        }catch (\Exception $e){
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

    public function until_appear($elementStr = 'tag:div'){
        sleep(config('app.until_appear_ready'));
        $elementArr = explode(':',$elementStr);
        $by = null;
        switch ($elementArr[0]){
            case 'tag' :
                $by = WebDriverBy::tagName($elementArr[1]);break;
            case 'class' :
                $by = WebDriverBy::className($elementArr[1]);break;
            case 'id' :
                $by = WebDriverBy::id($elementArr[1]);break;
        }

        try{
            $this->driver->wait(config('app.until_appear_timeout'))->until(
                WebDriverExpectedCondition::visibilityOfElementLocated(
                    $by
                )
            );
        }catch (TimeOutException $e){
            self::saveLog('until','no',$elementStr,'appear','disappear');
            return true;
        }
    }

    public function until_disappear($elementStr = 'tag:div'){
        sleep(config('app.until_disappear_ready'));
        $elementArr = explode(':',$elementStr);
        $by = null;
        switch ($elementArr[0]){
            case 'tag' :
                $by = WebDriverBy::tagName($elementArr[1]);break;
            case 'class' :
                $by = WebDriverBy::className($elementArr[1]);break;
            case 'id' :
                $by = WebDriverBy::id($elementArr[1]);break;
        }
        try{
            $this->driver->wait(config('app.until_disappear_timeout'))->until(
                WebDriverExpectedCondition::invisibilityOfElementLocated(
                    $by
                )
            );
        }catch (TimeOutException $e){
            self::saveLog('until','no',$elementStr,'disappear','appear');
            return true;
        }
    }

    public function saveLog($stepType,$status,$stepStr,$expect,$result){
        switch (config('app.log_output')){
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
