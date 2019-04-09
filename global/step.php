<?php
/**
 * @author bonwe
 * 自定义全局方法
 */
if (!function_exists('step')) {
    function step($key,$default=null){

        $keyArr = explode('.',$key);
        $fileName = __DIR__ . '/../app/steps';
        foreach ($keyArr as $v){
            $fileName .= '/'.$v;
        }
        $fileName .= '.php';

        $return = require($fileName);

        return $return;
    }
}