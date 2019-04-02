<?php
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
/**
 * @author weibangwei
 * 自定义全局方法
 */
if (!function_exists('app_path')) {
    function app_path(){
        return __DIR__."/../app";
    }
}