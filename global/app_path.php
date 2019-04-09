<?php
/**
 * @author bonwe
 * 自定义全局方法
 */
if (!function_exists('app_path')) {
    function app_path(){
        return __DIR__."/../app";
    }
}