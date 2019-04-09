<?php
/**
 * @author bonwe
 * 自定义全局方法
 */
if (!function_exists('steps_path')) {
    function steps_path(){
        return __DIR__."/../app/steps";
    }
}