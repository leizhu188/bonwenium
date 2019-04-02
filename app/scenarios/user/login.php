<?php
/*
 * 登录 -- 场景
 */
return [
    //手机号格式
    [
        'must_step'    => "tag:button>text:登录>>>click",
        'asserts'   => [],
        'check_msg' => "手机号码不可为空",
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入手机号>>>write:aaa",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入密码>>>write:123456",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:button>text:登录>>>click",
        'asserts'   => [],
        'check_msg' => "手机号码不符合规范",
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入手机号>>>write:1881068077",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入密码>>>write:123456",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:button>text:登录>>>click",
        'asserts'   => [],
        'check_msg' => "手机号码不符合规范",
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入手机号>>>write:18810689898",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入密码>>>write:123456",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:button>text:登录>>>click",
        'asserts'   => [],
        'check_msg' => "手机号未注册，请先注册",
    ],
    //密码格式
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入手机号>>>write:18810680772",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入密码>>>write:!!",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:button>text:登录>>>click",
        'asserts'   => [],
        'check_msg' => "密码应为3~20位非空字符",
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入手机号>>>write:18810680772",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入密码>>>write:aaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:button>text:登录>>>click",
        'asserts'   => [],
        'check_msg' => "密码应为3~20位非空字符",
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入手机号>>>write:18810680772",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入密码>>>write:123123123",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:button>text:登录>>>click",
        'asserts'   => [],
        'check_msg' => "手机号或密码错误",
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入手机号>>>write:18810680772",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:div>class:row>>tag:input>placeholder:请输入密码>>>write:123456",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:button>text:登录>>>click",
        'asserts'   => [],
    ],
];