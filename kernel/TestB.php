<?php
/**
 * Created by PhpStorm.
 * User: bangweiwei
 * Date: 2019-03-28
 * Time: 11:05
 */

namespace Kernel;


class TestB
{
    private $testA;
    public function __construct(TestA $testA)
    {
        $this->testA = $testA;
    }

    public function echo(){
        var_dump($this->testA->echo());
    }

}