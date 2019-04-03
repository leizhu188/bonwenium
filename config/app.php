<?php
/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-03-19
 * Time: 13:59
 */
return[
    //入口
    'index_url' => 'https://www.baidu.com',

    //日志导出到 log_file | mysql
    'log_output' => 'log_file',

    //每一事件的超时限制
    'step_timeout' => 5,

    //每一事件的超时限制
    'until_appear_ready' => 0,
    'until_appear_timeout' => 5,

    //每一事件的超时限制
    'until_disappear_ready' => 1,
    'until_disappear_timeout' => 5,
];