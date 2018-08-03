<?php
/**
 * @className   ：邮件接口
 * @description ：支持主流163、qq邮箱，注意发送内容需合规
 * @author      : calfbbs技术团队
 * Date         : 2018年3月18日 22:30:54
 */

namespace Addons\api\controller;

use Addons\api\model\BaseModel;
use Addons\api\services\mail\MailService;
use Addons\api\validate\SendMailValidate;

class Mail extends BaseModel
{
    /**
     * 失败状态码
     */
    const ERROR_CODE = 2001;

    /**
     * 成功状态码
     */
    const SUCCESS_CODE = 1001;

    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();
    }

    public function send()
    {
        $validate       = new SendMailValidate($this->post);
        $validateResult = $validate->sendValidate();

        try {

            $mail = new MailService();

            /**
             * 待发送邮件
             */
            $mail->recipient = $validateResult['email'];

            /**
             * 内容
             */
            $mail->isHTML(true);
            $mail->Subject = $validateResult['subject'];
            $mail->Body    = $validateResult['content'];
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->sendMail();
            return $this->returnMessage(self::SUCCESS_CODE,'邮件发送成功',true);

        } catch (\Exception $e) {
            return $this->returnMessage(self::ERROR_CODE,'邮件发送失败',$mail->ErrorInfo);
        }
    }
}