<?php
/**
 * @author bonwe
 * 自定义全局方法
 */
if (!function_exists('env')) {
    function env($key,$default=null){

        $fileName = __DIR__ . '/../.env';

        $return = parse_ini_file($fileName);

        if (isset($return[$key])){
            return $return[$key];
        }

        return $default;
    }
}