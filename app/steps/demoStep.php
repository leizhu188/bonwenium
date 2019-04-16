<?php
/*
 * 流程线：
 * 一次测试流程的分发口
 */
return [
    //输入搜索内容 -> 搜索
    app('scenarios.demoScenario.demo'),
    //点击 更多 -> 返回首页
    app('actions.demoAction.demo'),
    //点击新闻
    [
        'type'=>'step',
        'path'=>'x-path://*[@id="u1"]/a[1]',
        'value'=>'click',
    ],
    //unction 自定义操作
    [
        'type'=>'function',
        'value'=>'DemoFunction@demo@{"id":1,"name":"bonwe"}',
    ],
];