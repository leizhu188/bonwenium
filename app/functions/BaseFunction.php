<?php
/**
 * Created by PhpStorm.
 * User: bonwe
 * Date: 2019-03-28
 * Time: 15:16
 */

namespace App\functions;


use Kernel\Controller;

class BaseFunction extends Controller
{
    public $datas = [];

    public function setDatas($datas){
        $this->datas = $datas;
    }

    // curl 请求接口
    public function request($url,$https=true,$method='get',$data=null) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        if ($https == true){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        }
        if ($method == 'post'){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

}