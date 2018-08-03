<?php
/**
 * @className：图片处理验证类
 * @description：对接口传入的参数进行验证及过滤
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */


namespace Addons\api\validate;

use \Addons\api\validate\BaseValidate;
class FilesValidate   extends BaseValidate
{

    /** 上传图片传入参数验证
     * @param array $data
     */

    public function uploadFileValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator->requestMethod("POST");
        if(!empty($data['width'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('width');
        }

        return $this->returnValidate($validator);

    }

    /** 删除图片传入参数验证
     * @param array $data
     */

    public function deleteFileValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator->requestMethod("GET");
        $validator
                ->required('该参数值不能为空')
                ->validate('path');

        return $this->returnValidate($validator);

    }
}