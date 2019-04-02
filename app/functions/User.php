<?php
/**
 * Created by PhpStorm.
 * User: bangweiwei
 * Date: 2019-03-28
 * Time: 14:28
 */

namespace App\Functions;


class User extends BaseFunction
{

    /*
     * 请求发送验证码接口并获取验证码，填入方块中
     */
    public function writeResetPwdCaptcha(){
        $code = self::getResetPwdCaptcha($this->datas['phone']);
        $this->doStep("class:row>>tag:input>placeholder:输入验证码>>>write:{$code}","must");
    }

    private function getResetPwdCaptcha($phone){
        $rs = $this->request(
            'https://dev.vanthink-core-api.vanthink.cn/master/api/user/resetPasswordPwdSendCaptcha',
            true,
            'post',
            ['phone'=>$phone]
        );
        $rs = json_decode($rs,true);
        return $rs['data'];
    }

}