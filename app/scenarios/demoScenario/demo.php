<?php
/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-04-02
 * Time: 15:27
 * 情景操作样例：
 * 1.尝试 点击'搜索工具'
 * 2.停顿2秒
 * 3.必须 点击'收起工具'
 * 4。停顿500毫秒
 */
return[
    [
        'should_step' => 'tag:div>text:搜索工具>>>click',
    ],
    [
        'sleep' => 2,
    ],
    [
        'must_step' => 'tag:span>text:收起工具>>>click',
    ],
    [
        'asleep' => 500000,
    ]
];