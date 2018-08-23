<?php
/**
 * @author rock
 * Date: 2018/1/4 下午1:39
 */

namespace App\controller;


use  \Gregwar\Captcha\CaptchaBuilder;
use  \Gregwar\Captcha\PhraseBuilder;
use  Framework\library\Session;


class Login extends Base
{
    static public $conf = array ();

    public function __construct()
    {
        parent::__construct();

        /**
         * 获取分类列表
         */
        $classifyList = $this->column();
        $this->assign('classifyList', $classifyList);

    }

    /**
     * 获取配置信息
     */
    public function getConf($modules, $file = "weixin")
    {
        $route = \Framework\library\conf::all('route');
        $conf = CALFBB . '/' . $route['DEFAULT_ADDONS'] . '/' . $modules . "/config/" . $file . ".conf";

        if(is_file($conf)) {
            if(isset(self::$conf[$file])) {
                return self::$conf[$file];
            } else {
                self::$conf[$file] = include $conf;
                return self::$conf[$file];
            }
        }
        return false;
    }

    /**
     * 登陆页面
     */
    public function index()
    {
        $sms = \framework\library\Conf::all('sms');
        $param['dir_name'] = 'login';
        $data = $this->post(url("api/modules/getModules"), $param);
        if($data->code == 1001) {
            $modules = $data->data->modules;
            $this->assign('modules', $modules);
        }
        $config['weixin'] = $this->getConf('login', 'weixin');
        $config['qq'] = $this->getConf('login', 'qq');

        $this->assign('config', $config);

        if(isset($sms['SMS_STATUS']) && $sms['SMS_STATUS'] == 'on') {
            $this->display('login/index_sms');
        } else {
            $this->display('login/index');
        }

    }

    /**
     * 注册页面
     */
    public function signup()
    {
        $sms = \framework\library\Conf::all('sms');
        $param['dir_name'] = 'login';
        $data = $this->post(url("api/modules/getModules"), $param);
        if($data->code == 1001) {
            $modules = $data->data->modules;
            $this->assign('modules', $modules);
        }
        $config['weixin'] = $this->getConf('login', 'weixin');
        $config['qq'] = $this->getConf('login', 'qq');

        $this->assign('config', $config);
        if(isset($sms['SMS_STATUS']) && $sms['SMS_STATUS']=="on") {
            $this->display('login/signup_sms');
        } else {
            $this->display('login/signup');
        }
    }

    public function loginOut()
    {
        $access_token = self::$session->get('access_token');

        self::$session->del($access_token);
        header("Location:" . url('app/login/index'));
    }

    /**
     * 短信登陆验证
     */
    //   public function login_sms()
    //   {

    // if($_POST['vercode']!='rock'){
    //           show_json(['code' => 2001, 'message' => '响应错误', 'data' => '验证码输入错误']);
    //       }

    //       $data = $this->post(url("api/user/login"), $_POST);
    //       if ($data->code == 1001) {
    //           /**
    //            * access_token处理
    //            */
    //           @$access_token = md5($this->randomkeys(6) + $data->data->uid);
    //           $session = new Session();
    //           $access_token = self::$session->set('access_token', $access_token);
    //           $userinfo = self::$session->set($access_token, (array)$data->data);
    //           $data->data = "登陆成功";
    //       }
    //       echo json_encode($data);

    //   }
    /**
     * 登陆验证
     */
    public function login()
    {
        if(self::$session->get('milkcaptcha') != $_POST['vercode']) {
            show_json(['code' => 2001, 'message' => '响应错误', 'data' => '验证码输入错误']);
        }

        $data = $this->post(url("api/user/login"), $_POST);
        if($data->code == 1001) {
            /**
             * access_token处理
             */
            @$access_token = md5($this->randomkeys(6) + $data->data->uid);
            $session = new Session();
            $access_token = self::$session->set('access_token', $access_token);
            $userinfo = self::$session->set($access_token, (array)$data->data);
            $data->data = "登陆成功";
        }
        echo json_encode($data);

    }

    /**
     *注册验证
     */

    public function siginin()
    {
        if($_POST['type'] == 'mobile') {
            if(self::$session->get('sms_code') != $_POST['vercode']) {
                show_json(['code' => 2001, 'message' => '响应错误', 'data' => '验证码输入错误']);
            }
        } else {
            if(self::$session->get('milkcaptcha') != $_POST['vercode']) {
                show_json(['code' => 2001, 'message' => '响应错误', 'data' => '验证码输入错误']);
            }
        }

        /**
         * 获取配置头像目录
         */
        $avatar = \framework\library\Conf::get('AVATAR', 'calfbbs');
        if(!$avatar) {
            $avatar = 'default';
        }
        $jpg = "boy" . rand(1, 10) . ".jpg";
        $_POST['avatar'] = "avatar/" . $avatar . "/" . $jpg;
        $type = $_POST['type'];
        $data = $this->post(url("api/user/adduser"), $_POST);
        if($data->code == 1001) {
            /**
             * access_token处理
             */
            if($type == 'email') {
                $user['email'] = $_POST['email'];
            }
            if($type == 'mobile') {
                $user['mobile'] = $_POST['mobile'];
            }
            $user['password'] = $_POST['password'];
            $user['type'] = $type;
            $data = $this->post(url("api/user/login"), $user);

            if($data->code == 1001) {
                @$access_token = md5($this->randomkeys(6) + $data->data->uid);
                $access_token = self::$session->set('access_token', $access_token);
                $userinfo = self::$session->set($access_token, (array)$data->data);
                $data->data = "注册成功";

            }
        }

        echo json_encode($data);

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function captcha()
    {
        //生成验证码图片的Builder对象，配置相应属性
        $phraseBuilder = new PhraseBuilder(5, '0123456789');

        $builder = new CaptchaBuilder(null, $phraseBuilder);

        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 45, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();
        $session = new Session();
        //把内容存入session
        $session->set('milkcaptcha', $phrase);

        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');

        $builder->output();
    }

    /**
     * 忘记密码手机
     *
     * @throws \Exception
     */
    public function forget_mobile()
    {
        if(http_method() == 'GET' && !isset($_POST['vercode'])) {
            return $this->display('login/forget_phone');
        }

        $sms = \framework\library\Conf::all('sms');

        if($sms['SMS_STATUS'] != 'on') {
            show_json(['code' => 2001, 'message' => '响应错误', 'data' => '手机找回密码功能未开启']);
        }

        if(self::$session->get('milkcaptcha') != $_POST['vercode']) {
            show_json(['code' => 2001, 'message' => '响应错误', 'data' => '验证码输入错误']);
        }

        if(self::$session->get('sms_code') != $_POST['sms_code']) {
            show_json(['code' => 2001, 'message' => '响应错误', 'data' => '手机验证码错误']);
        }

        $response = $this->post(url("api/user/forget_mobile"), $_POST);

        echo json_encode($response);
    }

    /**
     * 忘记密码 (邮箱)
     */
    public function forget()
    {

        $sms = \framework\library\Conf::all('sms');

        if(http_method() == 'GET' && !isset($_POST['vercode'])) {

            if($sms !== false && isset($sms['SMS_STATUS']) && $sms['SMS_STATUS'] === 'on') {
                $sms_status = 'on';
            } else {
                $sms_status = 'off';
            }
            $this->assign('sms_status', $sms_status);

            $type = (isset($_GET['type']) && $_GET['type'] == 'phone') ? true : false;

            $this->assign('type', $type);

            return $this->display('login/forget');
        }

        if(self::$session->get('milkcaptcha') != $_POST['vercode']) {
            show_json(['code' => 2001, 'message' => '响应错误', 'data' => '验证码输入错误']);
        }

        $response = $this->post(url("api/user/forget"), $_POST);
        echo json_encode($response);
    }

    /**
     * 重置密码
     *
     * @throws \Exception
     */
    public function resetpassword()
    {
        if(http_method() == 'GET' && !isset($_POST['vercode'])) {
            $data = $_GET;
            $this->assign('data', $data);
            return $this->display('login/resetpassword');
        }

        if(self::$session->get('milkcaptcha') != $_POST['vercode']) {
            show_json(['code' => 2001, 'message' => '响应错误', 'data' => '验证码输入错误']);
        }

        $response = $this->post(url("api/user/resetpassword"), $_POST);

        if($response->code == 1001) {
            $access_token = self::$session->get('access_token');
            self::$session->del($access_token);
        }

        echo json_encode($response);
    }

    /**
     * 手机验证码
     */
    public function getvercode()
    {
        $data = $this->post(url("api/sms/sendCode"), $_POST);
        if($data->code==1001){
            self::$session->set('sms_code',$data->data);
            $data->data=true;
        }
        echo json_encode($data);
    }

}
