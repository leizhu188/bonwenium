<?php
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
/**
 * @author bonwe
 * 自定义全局方法
 */
if (!function_exists('monolog')) {
    function monolog($logMsg,$filename = null,$levelStr = 'error'){
        if (empty($filename)){
            $filename = date('y_m_d').'.log';
        }

        switch ($levelStr){
            case 'debug'    : $level = Logger::DEBUG;break;
            case 'info'     : $level = Logger::INFO;break;
            case 'warning'  : $level = Logger::WARNING;break;
            case 'error'    : $level = Logger::ERROR;break;
            default         : $level = Logger::ERROR;break;
        }

        $log = new Logger($filename);
        $log->pushHandler(new StreamHandler(__DIR__ . '/../storage/logs/' .$filename,$level));
        $log->$levelStr($logMsg);
    }
}