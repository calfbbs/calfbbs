<?php
/**
 * @className：用户管理验证文件
 * @description：对用户接口验证的文件
 * @author:calfbbs技术团队
 * Date: 2017/11/5
 * Time: 早上01:44
 */

namespace Addons\api\validate;

use framework\library\Validator;
use \Addons\api\validate\BaseValidate;

class UserValidate extends BaseValidate {


    /**
     * 验证添加用户资料
     * @param array $params 参数
     * @return mixed 通过验证参数
     */
    public function addUserValidate(array $params = [])
    {
        $validator = new Validator($params);

        // if (!isset($params['type'])) {//微信qq登录不需要email,mobile,密码
        //     if (isset($params['email'])) {
        //         $validator
        //             ->required('email不能为空')
        //             ->email('不是一个正确的email邮箱')
        //             ->validate('email');
        //     }
        //     if (isset($params['password'])) {
        //         $validator->required('密码不能为空')
        //             ->matches('repass', 'password', '两次密码输入不一致')
        //             ->validate('password');
        //     }
        // }else{

            $validator
                ->required('该参数值不能为空')
                ->validate('type');

            if($params['type']=='email'){            
                $validator
                    ->required('email不能为空')
                    ->email('不是一个正确的email邮箱')
                    ->validate('email');
            
            }
            if($params['type']=='mobile'){   
                $validator
                    ->required('该参数值不能为空')
                    ->validate('mobile');                
            
            }
            if(($params['type']=='weixin') || ($params['type']=='qq')){
                if (isset($params['email'])) {
                    $validator
                        ->required('email不能为空')
                        ->email('不是一个正确的email邮箱')
                        ->validate('email');
                }                
            }

            if (isset($params['password'])) {
                $validator->required('密码不能为空')
                        ->matches('repass', 'password', '两次密码输入不一致')
                        ->validate('password');
            }
        //}

        $validator
            ->required('用户昵称不能为空')
            //            ->between(3,9,false,'用户昵称必须3到9个字符')
            ->validate('username');

        

        if (isset($params['sex'])) {
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->between(0, 3, false, '性别只能为1和2')
                ->validate('sex');
        }

        if (isset($params['username'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('username');
        }

        if (isset($params['province'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('province');
        }

        if (isset($params['city'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('city');
        }

        if (isset($params['area'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('area');
        }

        if (isset($params['signature'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('signature');
        }

        if (isset($params['avatar'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('avatar');
        }

        if (isset($params['status'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('status');
        }

        return $this->returnValidate($validator);
    }

    /**
     * 验证更新用户资料
     * @param array $params
     * @return mixed
     */
    public function updateUserValidate(array $params = [])
    {
        $validator = $validator = new Validator($params);

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');

        if (isset($params['email']) && !empty($params['email'])) {
            $validator
                ->email('不是一个正确的email邮箱')
                ->validate('email');
        }


        if (isset($params['sex']) && !empty($params['sex'])) {
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->between(0, 3, false, '性别只能为1和2')
                ->validate('sex');
        }

        if (isset($params['username']) && !empty($params['username'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('username');
        }

        if (isset($params['province']) && !empty($params['province'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('province');
        }

        if (isset($params['city']) && !empty($params['city'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('city');
        }

        if (isset($params['area']) && !empty($params['area'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('area');
        }

        if (isset($params['signature']) && !empty($params['signature'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('signature');
        }

        if (isset($params['avatar']) && !empty($params['avatar'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('avatar');
        }

        if (isset($params['status']) && !empty($params['status'])) {
            $validator
                ->required('该参数值不能为空')
                ->validate('status');
        }


        return $this->returnValidate($validator);
    }


    /**
     * 验证更新用户资料
     * @param array $params
     * @return mixed
     */
    public function delUserValidate(array $params = [])
    {
        $validator = $validator = new Validator($params);

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');

        return $this->returnValidate($validator);
    }

    /**
     * 根据用户id资料查询
     * @param array $params
     * @return mixed
     */
    public function findUser(array $params)
    {
        $validator = $validator = new Validator($params);

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');

        return $this->returnValidate($validator);
    }

    /**
     * 用户资料查询
     * @param array $params
     * @return mixed
     */
    public function selectUser(array $params)
    {
        $validator = new Validator($params);

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->between(9, 101, false, '只能传10-100之间的数')
            ->validate('page_size');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('current_page');

        if (isset($params['email'])) {
            $validator
                ->email('不是一个正确的email邮箱')
                ->validate('email');
        }

        $validator->validate('username');

        return $this->returnValidate($validator);
    }

    /**
     * 搜索用户资料查询
     * @param array $params
     * @return mixed
     */
    public function searchUser(array $params)
    {
        $validator = new Validator($params);

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->between(9, 101, false, '只能传10-100之间的数')
            ->validate('page_size');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('current_page');

        // if (isset($params['email'])) {
        //     $validator
        //         ->email('不是一个正确的email邮箱')
        //         ->validate('email');
        // }

        $validator->validate('username');
        $validator->validate('email');

        return $this->returnValidate($validator);
    }

    /**
     * 用户中心提问验证
     * @param array $params 参数
     * @return mixed 通过验证参数
     */
    public function selectPostValidate(array $params = [])
    {
        $validator = $validator = new Validator($params);

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->between(9, 101, false, '只能传10-100之间的数')
            ->validate('page_size');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('current_page');

        $validator
            ->required('uid不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');

        return $this->returnValidate($validator);
    }


    /**
     * 用户中心问答验证
     * @param array $params 参数
     * @return mixed 通过验证参数
     */
    public function selectReplieValidate(array $params = [])
    {
        $validator = $validator = new Validator($params);

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->between(9, 101, false, '只能传10-100之间的数')
            ->validate('page_size');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('current_page');

        $validator
            ->required('uid不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');

        return $this->returnValidate($validator);
    }

    /**
     * 管理员修改用户密码
     * @param array $params
     * @return mixed
     */
    public function adminModifyPassword(array $params)
    {
        $validator = $validator = new Validator($params);

        $validator
            ->required('uid不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');

        $validator->required('新密码不能为空')
            ->validate('password');


        return $this->returnValidate($validator);
    }


    /**
     * 修改密码
     * @param array $params 参数
     * @return mixed 通过验证参数
     */
    public function modifypassword(array $params = [])
    {
        $validator = $validator = new Validator($params);

        $validator
            ->required('uid不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');

        $validator->required('旧密码不能为空')
            ->validate('old_password');

        $validator->required('确认密码不能为空')
            ->validate('repass');

        $validator->required('新密码不能为空')
            ->matches('repass', 'password', '两次密码输入不一致')
            ->validate('password');


        return $this->returnValidate($validator);
    }

    public function login(array $params = [])
    {
        $validator = new Validator($params);

        $validator
            ->required('登录类型不能为空')
            ->validate('type');
        if($params['type']=='username'){
            $validator
                ->required('username不能为空')
                ->validate('username');
        }

        if($params['type']=='email'){        
            $validator
                ->email('不是一个正确的email邮箱')
                ->validate('email');
        }
        if($params['type']=='mobile'){        
            $validator
                ->required('mobile不能为空')
                ->validate('mobile');
        }

        if(isset($params['type'])&&($params['type']=='register')){
            $validator
                ->required('uid不能为空')
                ->integer('该参数值必须是一个整型integer')
                ->validate('uid');
        }else{
            $validator->required('密码不能为空')
                ->validate('password');
        
        }
        

        return $this->returnValidate($validator);
    }


    /**
     * 找回密码
     * @param array $params
     * @return mixed
     */
    public function forget(array $params)
    {
        $validator = $validator = new Validator($params);

        $validator
            ->email('不是一个正确的email邮箱')
            ->validate('email');

        return $this->returnValidate($validator);
    }

    /**
     * 邮箱找回密码
     * @param array $params
     * @return mixed
     */
    public function resetpassword(array $params)
    {
        $validator = $validator = new Validator($params);

        $validator
            ->required('token不能为空')
            ->validate('token');

        $validator
            ->email('不是一个正确的email邮箱')
            ->validate('email');

        $validator->validate('mobile');

        $validator->required('密码不能为空')
            ->matches('repass', 'password', '两次密码输入不一致')
            ->validate('password');

        return $this->returnValidate($validator);
    }

    /**
     * 手机号找回密码
     * @param array $params
     * @return mixed
     */
    public function phoneResetPassword(array $params)
    {
        $validator = $validator = new Validator($params);

        $validator
            ->required('手机号不能为空')
            ->validate('mobile');

        return $this->returnValidate($validator);
    }
}

