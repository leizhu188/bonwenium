<?php
/*
 * 登录
 */
return
    [
        [
            'must_step'    => "class:bodyer>>tag:input>num:0>>>write:18810680772",
            'asserts'   => [],
        ],
        [
            'must_step'    => "class:bodyer>>tag:input>num:1>>>write:123456",
            'asserts'   => [
                'exists'=>[],
                'no_exists'=>[],
            ],
        ],
        [
            'must_step'    => "class:bodyer>>tag:button>>>click",
            'asserts'   => [],
            'check_msg' => null
        ],
        [
            'must_step'    => "class:select>>tag:span>text:在编教师>>>click",
            'asserts'   => [],
        ]
    ];