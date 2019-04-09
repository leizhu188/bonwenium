<?php
/**
 * @author bonwe
 * 自定义全局方法
 */
if (!function_exists('app')) {
    function app($key,$default=null){

        $keyArr = explode('.',$key);
        $fileName = __DIR__ . '/../app';
        foreach ($keyArr as $v){
            $fileName .= '/'.$v;
        }
        $fileName .= '.php';

        $return = require($fileName);

        return $return;
    }
}