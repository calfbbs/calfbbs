<?php
/**
 * Sms.php
 * 短信发送API接口
 * Created by PhpStorm.
 * Create On  2018/3/3111:22
 * Create by cyj
 */

namespace Addons\api\controller;



use Addons\api\model\BaseModel;
use Addons\api\services\sms\SignatureHelper;
use framework\library\Conf;
use Framework\library\Session;

class Sms extends BaseModel
{

    public function __construct()
    {
        $this->vaildateAppToken();
    }

    /**
     * 生成验证码
     * @param $length int 验证码长度,默认为6个中文字符
     * @return mixed
     * */
    private function getCaptcha($length = 6){
        $captcha = "";
        $str = "0123456789";
        $max = strlen($str)-1 ;
        for($i = 0 ; $i<$length ;$i ++){
            $captcha .= $str[rand(0,$max)];
        }
       // $session = new Session();
       // $session->set('sms_code',$captcha);
        return $captcha ;
    }
    /**
     * 发送短信验证
     * */
    public function sendCode(){

        $phone = isset($_POST['mobile']) ? $_POST['mobile'] : '';  //设置手机号码

        if(!preg_match('/^1[345789]\d{9}$/',$phone)){
            return $this->returnMessage(2001,'手机号码错误',[]);
        }

        $arr = Conf::all('sms'); //读取配置文件
        $params = array();
        $accessKeyId = $arr['accessKeyId'];
        $accessKeySecret = $arr['accessKeySecret'];
        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $phone;
        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = $arr['SignName'];

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $arr['templateCode'];

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $code = $this->getCaptcha();
        $params['TemplateParam'] = Array (
            "code" => $code,
        );

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        //$params['SmsUpExtendCode'] = "1234567";
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        $res = array_merge($params,array("RegionId" => "cn-hangzhou","Action" => "SendSms","Version" => "2017-05-25",));
        $helper = new SignatureHelper();
        try{
            $content = $helper->request($accessKeyId,$accessKeySecret,"dysmsapi.aliyuncs.com",$res);
            if($content->Code == 'OK'){
                //发送成功
                return $this->returnMessage(1001,'短信发送成功',$code);
            }else{
                $message=@$res->Message ?  @$res->Message  : "短信发送失败";
                return $this->returnMessage(2001,$message,false);
            }
        }catch (\Exception $e){
            return $this->returnMessage(2001,'短信发送失败',false);
        }
    }
}