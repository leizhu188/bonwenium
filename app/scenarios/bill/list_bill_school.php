<?php
/*
* 本校 - 题单列表操作
*/
return [
    [
        'must_step'    => "id:logo>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "tag:a>text:题库>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "class:head-bottom-nav>>tag:a>text:本校>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    //标签筛选
    [
        'must_step'    => "tag:span>text:高中课内教材>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "class:tag-wrapper>>tag:span>text:人教版>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "tag:input>placeholder:请输入内容>>>write:高一单词2",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "tag:span>text:人教版>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "tag:span>text:人教版>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "tag:span>text:高中课内教材>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "tag:input>placeholder:请选择>>>click",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:span>text:推荐人>>>click",
        'asserts'   => [],
    ],
    [
        'must_step'    => "tag:input>placeholder:请输入内容>>>write:韦老师",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "tag:button>>tag:span>text:搜索>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "tag:span>text:高中课内教材>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
    [
        'must_step'    => "tag:span>text:高中课内教材>>>click",
        'asserts'   => [],
        'check_msg' => null,
    ],
];