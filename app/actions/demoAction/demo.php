<?php
/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-04-02
 * Time: 15:08
 * 标准操作样例 ：
 * 1.必须 搜索框输入内容
 * 2.必须 点击搜索
 */
return[
    [
        'type'=>'step',
        'path'=>'x-path://*[@id="s_tab"]/div/a[9]',
        'value'=>'click',
        'level'=>'must',
    ],
    [
        'type'=>'step',
        'path'=>'x-path://*[@id="s_fm"]/a/img',
        'value'=>'click',
        'level'=>'must',
    ],
];