<?php
/**
 * @className：用户TOKEN操作类
 * @description：
 * @author:calfbbs技术团队
 * Date: 2017/11/16
 * Time: 下午9:23
 */

namespace Addons\api\controller;
use Addons\api\services\user\TokenServices;
use Addons\api\validate\TokenValidate;
use Addons\api\services\user\UserServices;
use Addons\api\model\UserModel;
class Token extends TokenServices
{
    public $validate;
    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();
        $this->validate = new TokenValidate();
    }

    /** 通过账号密码获取用户Token
     * @return mixed
     */
    public function getUserToken(){

        $UserServices=new UserServices();
        $UserModel=new UserModel();
        if(!isset($this->post['type'])){
            $this->post['type']="";
        }
        $params = $this->validate->getUserTokenValidate($this->post);

        /*根据类型判断**/
        if ($params['type'] == 'email') {
            if (empty($params['email'])) {
                return $this->returnMessage(2001, '响应错误', 'email不能为空');
            }

            $user = $UserModel->getUser(['email' => $params['email']]);
            if ( !$user) {
                return $this->returnMessage(2001, '响应错误', '邮箱不存在');
            }

        } else if ($params['type'] == 'username') {
            if (empty($params['username'])) {
                return $this->returnMessage(2001, '响应错误', '用户名不能为空');
            }

            $user = $UserModel->getUser(['username' => $params['username']]);
            if ( !$user) {
                return $this->returnMessage(2001, '响应失败', '用户不存在');
            }
        } else if ($params['type'] == 'mobile') {
            if (empty($params['mobile'])) {
                return $this->returnMessage(2001, '响应错误', '手机不能为空');
            }

            $user = $UserModel->getUser(['mobile' => $params['mobile']]);
            if ( !$user) {
                return $this->returnMessage(2001, '响应失败', '手机不存在');
            }
        } else if ($params['type'] == 'register') {
            if (empty($params['uid'])) {
                return $this->returnMessage(2001, '响应错误', 'uid不能为空');
            }

            $user = $UserModel->getUser(['uid' => $params['uid']]);
            if ( !$user) {
                return $this->returnMessage(2001, '响应失败', 'uid不存在');
            }
        }else {
            return $this->returnMessage(2001, '响应失败', '登录类型有误');
        }

        /**验证密码是否正确*/
        $password = $UserServices->validatePassword($user, $params['password']);
        if ($password == false) {
            return $this->returnMessage(2001, '响应失败', '密码不正确');
        }
        if (isset($user['password'])){
            unset($user['password']);
        }


        if (isset($user['token'])){
            unset($user['token']);
        }

        if ($user) {
            /**
             * 调用TokenServers,创建一个新的Token
             */
            $token=$this->createToken($user);
            return $this->returnMessage(1001, '响应成功', $token);
        }
        return $this->returnMessage(2001, '响应错误', "获取token失败");
    }





    /**
     * 删除用户Token
     */

//    public function deleteUserToken(){
//        /**
//         * get 字段参数验证是否符合条件
//         */
//        $validateResult=$this->validate->deleteUserTokenValidate($this->post);
//
//        /**
//         * 判断验证是否有报错信息
//         */
//
//        if(@$validateResult->code==2001){
//            return $validateResult;
//        }
//
//        $user=$this->decode($validateResult['key'],$_SERVER['HTTP_HOST']);
//
//    }
}