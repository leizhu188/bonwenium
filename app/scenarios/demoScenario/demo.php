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
        'path'=>'bw-path:>>id:kw',
        'value'=>'write:bonwenium',
        'level'=>'must',
    ],
    [
        'type'=>'until',
        'path'=>'x-path://*[@id="ent_sug"]',
        'value'=>'appear',
    ],
    [
        'type'=>'untils',
        'list' =>[
            [
                'path'=>'x-path://*[@id="ent_sug"]',
                'value'=>'disappear',
            ],
            [
                'path'=>'x-path://*[@id="ent_sug"]',
                'value'=>'appear',
            ]
        ]
    ],
    [
        'type'=>'assert',
        'path'=>'x-path://*[@id="s_tab"]/div/a[text()="贴吧"]',
        'value'=>'exist'
    ],
    [
        'type'=>'asserts',
        'list'=>[
            [
                'path'=>'x-path://*[@id="s_tab"]/div/a[text()="图片"]',
                'value'=>'exist'
            ],
            [
                'path'=>'x-path://*[@id="s_tab"]/div/a[text()="影音"]',
                'value'=>'exist'
            ],
            [
                'path'=>'x-path://*[@id="s_tab"]/div/a[text()="包子"]',
                'value'=>'no_exist'
            ],
            [
                'path'=>'x-path://*[@id="s_tab"]/div/a[text()="地图"]',
                'value'=>'no_exist'
            ],
        ]
    ],
    [
        'type'=>'step',
        'path'=>'x-path://*[@id="su"]',
        'value'=>'click',
        'level'=>'must',
    ],
];