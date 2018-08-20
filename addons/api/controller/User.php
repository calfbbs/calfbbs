<?php
/**
 * @className：用户管理接口
 * @description：添加会员 , 修改会员资料 , 删除会员 , 获取会员详细信息 , 获取会员列表 , 用户提问 , 用户回帖 , 修改密码 ,登录用户
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\api\controller;

use Addons\api\model\UserModel;
use Addons\api\validate\UserValidate;
use Addons\api\services\user\TokenServices;
class User extends UserModel {

    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();

    }

    /**
     * 添加会员
     * @param varchar $email 邮箱
     * @param string $username 用户昵称
     * @param string $password 密码
     * @param string $repass 确认密码
     * @return array $data   响应数据
     */
    public function addUser()
    {
        $validate = new UserValidate();

        $datas = $validate->addUserValidate($this->post);
        /**
         * 判断验证是否有报错信息
         */
        
        if (@$datas->code == 2001) {
            return $datas;
        }

        /**验证email是否唯一**/
        if (isset($datas['email'])) {
            $user = $this->getUser(['email' => $datas['email']]);

            if ($user && @$user['uid'] != @$datas['uid']) {
                return $this->returnMessage(2001, '响应失败', [$datas['email'] => "，该email已被使用"]);
            }

        }

        /**验证mobile是否唯一**/
        if (isset($datas['mobile'])) {
            $user = $this->getUser(['mobile' => $datas['mobile']]);

            if ($user && @$user['uid'] != @$datas['uid']) {
                return $this->returnMessage(2001, '响应失败', [$datas['mobile'] => "，该mobile已被使用"]);
            }

        }

        //验证用户名是否存在
        if (isset($datas['username'])) {
            $user = $this->getUser(['username' => $datas['username']]);

            if ($user && @$user['uid'] != @$datas['uid']) {
                return $this->returnMessage(2001, '响应失败', [$datas['username'] => "，该用户名已被使用"]);
            }
        }

        // /**验证email是否唯一**/
        // $email=$this->validateEamil($datas['email']);
        // if($email){
        //     $this->returnMessage(2001,'响应错误',[$datas['email']=>'email已被使用']);
        // }


        // //验证用户名是否唯一
        // $username=$this->validateUsername($datas['username']);
        // if($username){
        //     $this->returnMessage(2001,'响应错误',[$datas['username']=>'用户名已被使用']);
        // }


        if(!isset($datas['type'])){//微信qq注册跳过不需要密码

            /**token**/
            $datas['token'] = $this->randomkeys(6);

            /**处理密码**/
            $datas['password'] = md5($datas['token'] . $datas['password']);

        }else{//微信qq注册跳过不需要密码            

            if(isset($datas['password'])){
                /**token**/
                $datas['token'] = $this->randomkeys(6);

                /**处理密码**/
                $datas['password'] = md5($datas['token'] . $datas['password']);
            }
        }
        unset($datas['type']);
	   $datas['create_time'] = time();


	   $result = $this->create($datas);

        if ($result) {
            return $this->returnMessage(1001, '创建用户成功', $result);
        }
        return $this->returnMessage(2001, '响应错误', $result);

    }

    /**
     * 修改会员资料
     * @param int $uid 用户uid
     * @param varchar $email 邮箱
     * @param string $username 用户昵称
     * @param int $sex 性别
     * @param string $city 区县
     * @param string $area 地区
     * @param text $signature 个性签名
     * @param string $avatar 头像
     * @return array $data   响应数据
     */
    public function updateUser()
    {
        $validate = new UserValidate();
        $datas = $validate->updateUserValidate($this->post);
        /**
         * 判断验证是否有报错信息
         */
        if (@$datas->code == 2001) {
            return $datas;
        }
        /**验证用户是否存在**/
        $user = $this->userHas($datas['uid']);
        if ($user == false) {
            return $this->returnMessage(2001, '响应错误', [$datas['uid'] => '，该用户数据库不存在这条记录']);
        }

        /**验证email是否唯一**/
        if (isset($datas['email'])) {
            $user = $this->getUser(['email' => $datas['email']]);

            if ($user && $user['uid'] != $datas['uid']) {
                return $this->returnMessage(2001, '响应失败', [$datas['email'] => "，该email已被使用"]);
            }

        }

        //验证用户名是否存在
        if (isset($datas['username'])) {
            $user = $this->getUser(['username' => $datas['username']]);

            if ($user && $user['uid'] != $datas['uid']) {
                return $this->returnMessage(2001, '响应失败', [$datas['username'] => "，该用户名已被使用"]);
            }
        }

        $result = $this->update($datas, ['uid' => $datas['uid']]);

        if ($result === false) {
            return $this->returnMessage(2001, '响应错误', false);
        }

        return $this->returnMessage(1001, '响应成功', true);
    }

    /**
     * 删除会员
     * @param int $id 用户uid
     * @return array $data   响应数据
     */
    public function deleteUser()
    {
        $validate = new UserValidate();

        $datas = $validate->delUserValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if (@$datas->code == 2001) {
            return $datas;
        }
        /**验证用户是否存在**/
        $user = $this->userHas($datas['uid']);
        if ($user == false) {
            return $this->returnMessage(2001, '响应错误', [$datas['uid'] => '，该用户数据库不存在这条记录']);
        }
        $result = $this->delete(['uid' => $datas['uid']]);

        if ($result) {
            return $this->returnMessage(1001, '响应成功', $result);
        }
        return $this->returnMessage(2001, '响应错误', $result);
    }

    /**
     * 根据用户uid查询用户的详细资料
     * @param int $uid 用户uid
     * @return array $data   响应数据
     */
    public function getUserInfo()
    {
        $validate = new UserValidate();

        $datas = $validate->findUser($this->get);

        /**
         * 判断验证是否有报错信息
         */
        if (@$datas->code == 2001) {
            return $datas;
        }
        /**验证用户是否存在**/
        $user = $this->userHas($datas['uid']);
        if ($user == false) {
            return $this->returnMessage(2001, '响应错误', '该用户数据库不存在这条记录');
        }
        $result = $this->getUser(['uid' => $datas['uid']]);

        if (isset($result['password']))
            unset($result['password']);

        if (isset($result['token']))
            unset($result['token']);

        if ($result) {
            return $this->returnMessage(1001, '响应成功', $result);
        }
        return $this->returnMessage(2001, '响应错误', $result);
    }


    /**
     * 获取会员列表
     * @param string $email 邮箱
     * @param string $username 昵称
     * @param int $page_size 每页显示数量
     * @param int $current_page 页码
     */
    public function getUsersList()
    {
        $validate = new UserValidate();

        $datas = $validate->selectUser($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if (@$datas->code == 2001) {
            return $datas;
        }
        $result = $this->getUsers($datas);

        if ($result['list']) {
            return $this->returnMessage(1001, '响应成功', $result);
        }
        return $this->returnMessage(2001, '响应错误', $result);
    }

    /**
     * 获取搜索会员列表
     * @param string $email 邮箱
     * @param string $username 昵称
     * @param int $page_size 每页显示数量
     * @param int $current_page 页码
     */
    public function getSearchList()
    {
        $validate = new UserValidate();

        $datas = $validate->searchUser($this->get);

        /**
         * 判断验证是否有报错信息
         */
        if (@$datas->code == 2001) {
            return $datas;
        }
        $result = $this->getSearchUsers($datas);

        if ($result['list']) {
            return $this->returnMessage(1001, '响应成功', $result);
        }
        return $this->returnMessage(2001, '响应错误', $result);
    }

    /**
     * 获取用户最近的提问
     * @param string $uid 用户uid
     * @param int $page_size 每页显示数量
     * @param int $current_page 页码
     */
    public function getQuestions()
    {
        $validate = new UserValidate();

        $datas = $validate->selectPostValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if (@$datas->code == 2001) {
            return $datas;
        }
        /**验证用户是否存在**/
        $user = $this->userHas($datas['uid']);
        if ($user == false) {
            return $this->returnMessage(2001, '响应错误', '该用户数据库不存在这条记录');
        }
        $posts = $this->questions($datas);

        if ($posts['list']) {
            return $this->returnMessage(1001, '响应成功', $posts);
        }
        return $this->returnMessage(2001, '响应错误', $posts);
    }


    /**
     * 获取用户最近的回答
     * @param string $uid 用户uid
     * @param int $page_size 每页显示数量
     * @param int $current_page 页码
     */
    public function getAnswers()
    {

        $validate = new UserValidate();

        $datas = $validate->selectReplieValidate($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if (@$datas->code == 2001) {
            return $datas;
        }
        /**验证用户是否存在**/
        $user = $this->userHas($datas['uid']);
        if ($user == false) {
            return $this->returnMessage(2001, '响应错误', '该用户数据库不存在这条记录');
        }

        $replies = $this->answers($datas);

        if ($replies['list']) {
            return $this->returnMessage(1001, '响应成功', $replies);
        }
        return $this->returnMessage(2001, '响应错误', $replies);

    }

    /**
     * 判断某个用户是否存在
     * @param $uid 用户uid
     * @return array
     */
    public function userHas($uid)
    {
        /**验证用户是否存在**/
        $user = $this->getUser(['uid' => $uid]);

        if ( !$user) {
            return false;
        }

        return $user;
    }

    /**
     * 管理员修改密码
     * @param $uid 用户uid
     * @param $password 新密码
     * @param $repass 确认密码
     * @return json响应数据
     */
    public function adminModifyPassword()
    {
        $validate = new UserValidate();

        $datas = $validate->adminModifyPassword($this->post);

        /**
         * 判断验证是否有报错信息
         */

        if (@$datas->code == 2001) {
            return $datas;
        }

        /**验证用户是否存在**/
        $user = $this->userHas($datas['uid']);
        if ($user == false) {
            return $this->returnMessage(2001, '响应错误', '该用户数据库不存在这条记录');
        }


        /**token**/
        $params['token'] = $this->randomkeys(6);

        /**处理密码**/
        $params['password'] = md5($params['token'] . $datas['password']);

        $result = $this->update($params, ['uid' => $datas['uid']]);

        if ($result) {
            return $this->returnMessage(1001, '响应成功', $result);
        }
        return $this->returnMessage(2001, '响应错误', $result);
    }

    /**
     * 修改密码
     * @param $uid 用户uid
     * @param $old_password 旧密码
     * @param $password 新密码
     * @param $repass 确认密码
     * @return json响应数据
     */
    public function modifyPassword()
    {
        $validate = new UserValidate();

        $datas = $validate->modifypassword($this->post);
        /**
         * 判断验证是否有报错信息
         */
        if (@$datas->code == 2001) {
            return $datas;
        }
        if ($datas['old_password'] == $datas['password']) {
            return $this->returnMessage(2001, '响应错误', '新密码跟旧密码不能一样');
        }

        /**验证用户是否存在**/
        $user = $this->userHas($datas['uid']);
        if ($user == false) {
            return $this->returnMessage(2001, '响应错误', '该用户数据库不存在这条记录');
        }

        /**验证密码是否正确*/
        $password = $this->validatePassword($user, $datas['old_password']);
        if ($password == false) {
            return $this->returnMessage(2001, '响应失败', '原密码不正确');
        }

        /**token**/
        $params['token'] = $this->randomkeys(6);

        /**处理密码**/
        $params['password'] = md5($params['token'] . $datas['password']);

        $result = $this->update($params, ['uid' => $datas['uid']]);

        if ($result === false) {
            return $this->returnMessage(2001, '响应错误', '修改失败');
        }
        return $this->returnMessage(1001, '响应成功', '修改成功');
    }

    /**
     * 登录用户
     * @param $type 账号类型 ( email / username)
     * @param $email 邮箱
     * @param $username 用户名
     * @param $password 密码
     * @return json响应数据
     */
    public function login()
    {
        $validate = new UserValidate();

        $params = $validate->login($this->post);

        /**
         * 判断验证是否有报错信息
         */

        if (@$params->code == 2001) {
            return $params;
        }

        /*根据类型判断**/
        if ($params['type'] == 'email') {
            if (empty($params['email'])) {
                return $this->returnMessage(2001, '响应错误', 'email不能为空');
            }

            $user = $this->getUser(['email' => $params['email']]);
            if ( !$user) {
                return $this->returnMessage(2001, '响应错误', '邮箱不存在');
            }
        } else if ($params['type'] == 'mobile') {
            if (empty($params['mobile'])) {
                return $this->returnMessage(2001, '响应错误', '手机不能为空');
            }

            $user = $this->getUser(['mobile' => $params['mobile']]);
            if ( !$user) {
                return $this->returnMessage(2001, '响应失败', '手机不存在');
            }

        } else if ($params['type'] == 'username') {
            if (empty($params['username'])) {
                return $this->returnMessage(2001, '响应错误', '用户名不能为空');
            }

            $user = $this->getUser(['username' => $params['username']]);
            if ( !$user) {
                return $this->returnMessage(2001, '响应失败', '用户不存在');
            }
        } else if ($params['type'] == 'register') {
            if (empty($params['uid'])) {
                return $this->returnMessage(2001, '响应错误', 'uid不能为空');
            }

            $user = $this->getUser(['uid' => $params['uid']]);
            if ( !$user) {
                return $this->returnMessage(2001, '响应失败', 'uid不存在');
            }else{
                return $this->returnMessage(1001,'响应成功',$user);
            }
            
        } else {
            return $this->returnMessage(2001, '响应失败', '登录类型有误');
        }

        /**验证密码是否正确*/
        $password = $this->validatePassword($user, $params['password']);
        if ($password == false) {
            return $this->returnMessage(2001, '响应失败', '密码不正确');
        }
        if (isset($user['password']))
            unset($user['password']);

        if (isset($user['token']))
            unset($user['token']);

        if ($user) {
            /**
             * 调用TokenServers,创建一个新的Token
             */
            $tokenServices=new TokenServices();
            $token=$tokenServices->createToken($user);
            $user['token']=$token;
            return $this->returnMessage(1001, '响应成功', $user);
        }
        return $this->returnMessage(2001, '响应错误', []);
    }

    /**
     *  验证email是否已经使用
     * @param $email
     */
    private function validateEamil($email)
    {
        $user = $this->getUser(['email' => $email]);
        if ($user) {
            return true;
        }
        return false;
    }

    /**
     * 验证用户名是否已经使用
     * @param $username
     */
    private function validateUsername($username)
    {
        $user = $this->getUser(['username' => $username]);
        if ($user) {
            return true;
        }
        return false;
    }

    /**
     *  验证密码是否正确
     * @param $user
     * @param $password
     * @return bool
     */
    public function validatePassword($user, $password)
    {
        //处理密码
        $new_password = md5($user['token'] . $password);

        if ($new_password != $user['password']) {
            return false;
        }
        return true;
    }

    /**
     * 手机找回密码
     * @return mixed
     */
    public function forget_mobile()
    {
        $validate = new UserValidate();

        $params = $validate->phoneResetPassword($this->post);

        /**
         * 判断验证是否有报错信息
         */

        if (@$params->code == 2001) {
            return $params;
        }

        //Todo 第一步,查询数据库这个用户是否存在.

        $user = $this->getUser(array('mobile' => $params['mobile']));

        if (empty($user)) {
            return $this->returnMessage(2001, '响应失败', '用户不存在');
        }

        //Todo 第二步: 根据用户uid+用户名+密码生成 "唯一" token 和过期time.

        $token = md5($user['uid'] . $user['username'] . $user['password']);
        $expire_time = time() + 1800;
        $string = base64_encode($token . '-' . $expire_time);

        $url = url('app/login/resetpassword', array('mobile' => $user['mobile'], 'token' => $string), false);

        return $this->returnMessage(1001, '响应成功', $url);
    }


    /**
     * 找回密码
     * @param $email 邮箱
     * @return json响应数据
     */
    public function forget()
    {
        $validate = new UserValidate();

        $params = $validate->forget($this->post);

        /**
         * 判断验证是否有报错信息
         */

        if (@$params->code == 2001) {
            return $params;
        }

        //Todo 第一步,查询数据库这个用户是否存在.

        $user = $this->getUser(array('email' => $params['email']));

        if (empty($user)) {
            return $this->returnMessage(2001, '响应失败', '用户不存在');
        }

        //Todo 第二步: 根据用户uid+用户名+密码生成 "唯一" token 和过期time.

        $token = md5($user['uid'] . $user['username'] . $user['password']);
        $expire_time = time() + 1800;
        $string = base64_encode($token . '-' . $expire_time);

        //Todo 第三步: 生成发送重置的邮件链接.
        //Todo 1. 生成需要发送的参数

        $url = url('app/login/resetpassword', array('email' => $user['email'], 'token' => $string), false);
        $data['email'] = $user['email'];
        $data['content'] = <<<EOT
        你好，{$user['username']}同学，请在30分钟内重置您的密码：<a href="{$url}" style="background-color: #009E94; color: #fff; display: inline-block; height: 32px; line-height: 32px; margin: 0 15px 0 0; padding: 0 15px; text-decoration: none;" target="_blank">立即重置密码</a>
EOT;
        $data['subject'] = '找回密码';

        //Todo 2.
        $api = new Api();
        $response = $api->post(url("api/mail/send"), $data);

        //Todo 第四步: 判断是否发送成功.

        if ($response->code != 1001) {
            return $this->returnMessage(2001, '响应失败', '发送重置密码邮件失败');
        }

        return $this->returnMessage(1001, '响应成功', '发送重置密码邮件成功');
    }


    /**
     * 重置密码处理逻辑
     * @param $token 根据账号生成的token
     * @param $email 邮箱
     * @param $password 新密码
     * @param $repass 确认密码
     * @return json响应数据
     */
    public function resetpassword()
    {
        $validate = new UserValidate();

        $params = $validate->resetpassword($this->post);

        /**
         * 判断验证是否有报错信息
         */

        if (@$params->code == 2001) {
            return $params;
        }

        //Todo 第一步: 验证email / mobile 是否存在.
        if(!empty($params['mobile'])) {
            $user = $this->getUser(array('mobile' => $params['mobile']));
        }else{
            $user = $this->getUser(array('email' => $params['email']));
        }

        if ( !$user) {
            return $this->returnMessage(2001, '响应失败', '用户不存在');
        }

        //Todo 第二步 解密 "base64" 得到 token 和 过期时间.
        $string = base64_decode($params['token']);
        $string = explode('-', $string);
        $token = $string[0];
        $expire_time = $string[1];

        //Todo 第三步 验证token正确.
        $md5 = md5($user['uid'] . $user['username'] . $user['password']);

        if ($token != $md5 || time() >= $expire_time) {
            return $this->returnMessage(2001, '响应失败', 'token 失效');
        }

        //Todo 重置密码.
        /**token**/
        $_data['token'] = $this->randomkeys(6);

        /**处理密码**/
        $_data['password'] = md5($_data['token'] . $params['password']);
        $response = $this->update($_data, array('uid' => $user['uid']));

        if ( !$response) {
            return $this->returnMessage(2001, '响应失败', '找回密码失败');
        }

        return $this->returnMessage(1001, '响应成功', '找回密码成功');
    }


    /**
     * 生成6位字母+数字随机数
     * @param $length
     * @return null|string
     */
    function randomkeys($length)
    {
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = null;
        for ( $i = 0; $i < $length; $i++ ) {
            $key .= $pattern{mt_rand(0, 35)};    //生成php随机数
        }
        return $key;
    }
}
