<?php
/**
 * @author bonwe
 * 自定义全局方法
 */
if (!function_exists('config')) {
    function config($key,$default=null){

        $keyArr = explode('.',$key);
        $fileName = __DIR__ . '/../config/' .$keyArr[0].'.php';

        $return = require($fileName);

        for ( $i = 1 ; $i<count($keyArr) ; $i++ ){
            $return = $return[$keyArr[$i]];
        }

        return $return;
    }
}