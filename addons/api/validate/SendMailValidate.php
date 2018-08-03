<?php
/**
 * @className   ：邮件接口验证
 * @author      : calfbbs技术团队
 * Date         : 2018年3月18日 22:35:54
 */

namespace Addons\api\validate;

use framework\library\Validator;

class SendMailValidate extends BaseValidate
{
    public $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function sendValidate()
    {
        $validator = new Validator($this->data);
        $validator
            ->requestMethod('POST');

        $validator
            ->required('email不能为空')
            ->email('email有误')
            ->validate('email');
        $validator
            ->required('内容不能为空')
            ->validate('content');
        $validator
            ->required('主题不能为空')
            ->validate('subject');

        return $this->returnValidate($validator);
    }
}