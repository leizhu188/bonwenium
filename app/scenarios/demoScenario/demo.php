<?php
/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-04-02
 * Time: 15:27
 * 情景操作：
 * 1.点击搜索工具
 */
return[
    [
        'should_step' => 'tag:div>text:搜索工具>>>click',
    ],
    [
        'sleep' => 2,
    ],
    [
        'should_step' => 'tag:span>text:收起工具>>>click',
    ],
    [
        'asleep' => 500000,
    ]
];