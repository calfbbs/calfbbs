<?php
/**
 * @className   ：邮件服务
 * @description ：重构PHPMailer类
 * @author      : calfbbs技术团队
 * Date         : 2018年3月18日 23:02:03
 */

namespace Addons\api\services\mail;

use PHPMailer;
use framework\library\Conf;

class MailService extends PHPMailer
{
    /**
     * @var string 收件箱
     */
    public $recipient;
    protected $mailConf;

    public function __construct($exceptions = true)
    {
        parent::__construct($exceptions);

        $this->mailConf = Conf::G('email');

        /**
         * 邮件服务帐号
         */
        $this->Username = $this->mailConf['email_url'];
        $this->Password = $this->mailConf['email_pass'];

    }

    /**
     * @function 邮件发送
     * @author   Felix <Fzhengpei@gmail.com>
     */
    public function sendMail()
    {
        // debug : 2
        $this->SMTPDebug = 0;

        $this->isSMTP();

        /**
         * QQ ：smtp.qq.com
         * 163：smtp.163.com
         */
        $this->Host = $this->mailConf['email_server'];

        // Enable SMTP authentication
        $this->SMTPAuth = true;

        // 设置发件人邮箱地址
        $this->From = $this->mailConf['email_url'];

        // Enable TLS encryption, `ssl` also accepted
        $this->SMTPSecure = 'ssl';

        // TCP端口 465、25、587
        $this->Port = $this->mailConf['email_port'];

        //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
        //$this->Hostname = 'http://www.calfbbs.com';

        //编码 可选UTF-8、GB2312
        $this->CharSet = 'UTF-8';

        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $this->FromName = 'Mailer';

        // 显示发件人信息
        $this->setFrom($this->Username, 'CalfBBS');

        //收件人
        $this->addAddress($this->recipient);

        $this->send();
    }


}