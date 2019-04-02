<?php
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
/**
 * @author weibangwei
 * 自定义全局方法
 */
if (!function_exists('steps_path')) {
    function steps_path(){
        return __DIR__."/../app/steps";
    }
}