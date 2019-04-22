<?php
/**
 * @author bonwe
 * 自定义全局方法
 */
if (!function_exists('debugs_path')) {
    function debugs_path(){
        return __DIR__."/../app/debugs";
    }
}