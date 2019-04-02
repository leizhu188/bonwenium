<?php
/*
 * 流程线：
 * 一次测试流程的分发口
 */
return [
    app('actions.demoAction.demo'),
    app('scenarios.demoScenario.demo'),
    [
        [
            'should_step'=>'tag:a>text:资讯>>>click',
            'sleep'=>2,
            'must_step'=>'tag:a>text:网页>>>click'
        ]
    ],
    [
        ['function'=>'DemoFunction@demo']
    ],
];