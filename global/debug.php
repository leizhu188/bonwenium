<?php
/**
 * @author bonwe
 * 自定义全局方法
 */
if (!function_exists('debug')) {
    function debug($key,$default=null){

        if (empty($key)){
            $keyArr = ['test'];
        }else{
            $keyArr = explode('.',$key);
        }
        $fileName = __DIR__ . '/../app/debugs';
        foreach ($keyArr as $v){
            $fileName .= '/'.$v;
        }
        $fileName .= '.php';

        $return = require($fileName);

        return $return;
    }
}