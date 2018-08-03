<?php
/**
 * @className：用户管理Services
 * @description：添加会员 , 修改会员资料 , 删除会员 , 获取会员详细信息 , 获取会员列表 , 用户提问 , 用户回帖 , 修改密码 ,登录用户
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */
namespace Addons\api\services\user;
use Addons\api\model\UserModel;
class UserServices  extends  UserModel
{
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
     *  验证email是否已经使用
     * @param $email
     */
    public function validateEamil($email)
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
    public function validateUsername($username)
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
}