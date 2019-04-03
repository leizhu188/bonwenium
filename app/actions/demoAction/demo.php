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
        'must_step'=>"id:kw>>>write:bonwenium",
    ],
    [
        'must_step'=>'id:su>>>click',
        'until'=>'id:su>>>disappear',
        'asserts'=>[
            'tag:a>text:图片>>>exist',
            'tag:a>text:影音>>>exist',
            'tag:a>text:包子>>>no_exist',
            'tag:a>text:地图>>>no_exist'
        ]
    ]
];