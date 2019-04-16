<?php
/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-03-28
 * Time: 14:28
 */

namespace App\Functions;


use Bonwe\WebDriver\WebDriverBy;

class DemoFunction extends BaseFunction
{

    /*
     * 继承自入口Controller。自定义方法满足非常规操作
     */
    public function demo(){
        dd($this->datas);
    }

}