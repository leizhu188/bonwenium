<?php
/*
 * 重置密码
 */
return
    [
        [
            'should_step'    => "tag:a>text:忘记密码?>>>click",
            'asserts'   => [],
        ],
        [
            'must_step'    => "tag:input>placeholder:输入手机号>>>write:18810680772",
            'asserts'   => [
            ],
        ],
        ['function'=>'User@writeResetPwdCaptcha@{"phone":18810680772}'],
        [
            'must_step'    => "tag:input>value:下一步>>>click",
            'asserts'   => [
            ],
        ],
        [
            'must_step'    => "tag:input>placeholder:请输入密码>>>write:123456",
            'asserts'   => [],
        ],
        [
            'must_step'    => "tag:input>placeholder:请再输入一次>>>write:123456",
            'asserts'   => [],
        ],
        [
            'must_step'     => "tag:input>value:重置>>>click",
            'asserts'       => [],
            'check_msg'     => '重置密码成功'
        ]
    ];