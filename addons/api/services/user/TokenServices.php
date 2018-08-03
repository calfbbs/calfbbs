<?php
/**
 * @className：用户TOKEN Services类
 * @description：
 * @author:calfbbs技术团队
 * Date: 2017/11/16
 * Time: 下午9:23
 */

namespace Addons\api\services\user;
use \Firebase\JWT\JWT;
use \Framework\library\Session;
use Addons\api\model\TokenModel;
class TokenServices extends TokenModel
{
    /** 创建一个Token
     * @param $user 用户信息
     *
     * @return string
     */
    public function createToken($user){
        $session=new Session();
        $token['uid']=$user['uid'];
        $token['username']=$user['username'];
        $token['status']=$user['status'];
        $token['expiredate']=time()+7200;
        $token['random']=random(6);
        $key=$_SERVER['HTTP_HOST'];
        $encode=$this->encode($token,$key);
        /**
         * 用于验证用户传过来的Token是否是最新的
         */
        $session->set("user_token_".$user['uid'],md5($encode));
        return $encode;
    }


    /** 验证Token 　是否合法
     * @param $token
     *
     * @return bool
     */
    public function VerifyToken($token){
        $session=new Session();
        $key=$_SERVER['HTTP_HOST'];
        $user=(array)$this->decode($token,$key);
        $encode=$session->get("user_token_".$user['uid']);
        /**
         * 验证Token是否是最新token
         */
        if($encode && md5($token) != $encode){
            return $this->returnMessage(2001, '响应失败', '非法token');
        }

        /**
         * 验证Token是否过期
         */
        if((int)$user['expiredate'] < time()){
            return $this->returnMessage(2001, '响应失败', 'token已经过期,请重新申请');
        }

        $session->set("user_token_".$user['uid'],md5($token));
        return $user;
    }


    /**
     * Token加密
     */
    public function encode($token, $key){
        $jwt = JWT::encode($token, $key);
        return $jwt;
    }


    /**
     * Token 解密
     */
    public function decode($jwt, $key){
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        return (array)$decoded;
    }
}