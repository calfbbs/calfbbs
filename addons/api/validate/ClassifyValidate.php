<?php
/**
 * @className：无限分类接口数据字段验证
 * @description：对接口传入的参数进行验证及过滤
 * @author:calfbbs技术团队
 * Date: 2017/10/25
 * Time: 下午6:25
 */

namespace Addons\api\validate;
use \Addons\api\validate\BaseValidate;
class  ClassifyValidate extends BaseValidate
{

    /** 插入数据传入参数验证
     * @param array $data
     */
    public function addClassifyValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->validate('name');
        $validator
            ->integer('该参数值必须是一个整型integer')
            ->validate('pid');

        return $this->returnValidate($validator);
    }

    /** 删除数据传入参数验证
     * @param array $data
     */
    public function delClassifyValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->validate('id');
        return $this->returnValidate($validator);
    }

    /** 修改数据传入参数验证
     * @param array $data
     */
    public function updateClassifyValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->validate('id');
        $validator
            ->integer('该参数值必须是一个整型integer')
            ->validate('pid');
        $validator
            ->required('该参数值不能为空')
            ->validate('name');
        return $this->returnValidate($validator);
    }

    /**获取分类数据列表传入参数验证
     * @function
     * @param array $data
     * @return mixed
     */
    public function getClassifyValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('page_size');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('current_page');

        return $this->returnValidate($validator);
    }

    /** 获取单条分类数据传入参数验证
     * @param array $data
     */
    public function getClassifyOneValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->requestMethod('GET');
        $validator
            ->integer('该参数值必须是一个整型integer')
            ->validate('id');


        return $this->returnValidate($validator);

    }
}