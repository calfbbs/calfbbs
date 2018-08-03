<?php
/**
 * @className：用户USER_TOKEN验证文件
 * @description：获取token验证的文件
 * @author:calfbbs技术团队
 * Date: 2018/4/5
 * Time: 早上01:44
 */
namespace Addons\api\validate;

use framework\library\Validator;
use \Addons\api\validate\BaseValidate;

class TokenValidate extends  BaseValidate
{
    public $validator;


    public function getUserTokenValidate(array $params = [])
    {

        $validator = $validator = new Validator($params);
        $validator->requestMethod("POST");
        $validator
            ->required('登录类型不能为空')
            ->validate('type');
        if($params['type']=='username'){
            $validator
                ->required('username不能为空')
                ->validate('username');
        }
        if($params['type']=='mobile'){
            $validator
                ->required('mobile不能为空')
                ->validate('mobile');
        }
        if($params['type']=='register'){
            $validator
                ->required('uid不能为空')
                ->integer('该参数值必须是一个整型integer')
                ->validate('uid');
        }

        if ($params['type']) {
            $validator
                ->email('不是一个正确的email邮箱')
                ->validate('email');
        }

        $validator->required('密码不能为空')
            ->validate('password');

        return $this->returnValidate($validator);
    }

    public function deleteUserTokenValidate(array $params = []){
        $validator = $validator = new Validator($params);

        $validator
            ->required('key不能为空')
            ->validate('key');

        return $this->returnValidate($validator);
    }
}