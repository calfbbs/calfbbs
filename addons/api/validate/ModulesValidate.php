<?php
/**
 * @className 插件接口数据字段验证
 * @description：对接口传入的参数进行验证及过滤
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */


namespace Addons\api\validate;

use \Addons\api\validate\BaseValidate;
class ModulesValidate  extends BaseValidate
{

    /** 获取插件数据传入参数验证
     * @param array $data
     */
    public function getModulesValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        $validator->required('该参数不能为空')->validate('dir_name');
        return $this->returnValidate($validator);

    }
    /** 获取模块列表验证
     * @return mixed
     */
    public function getModuleListsValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        if(isset($data['name'])){
            $validator
                ->required('该参数不能为空')
                ->validate('name');
        }
        $validator
            ->integer('页大小必须是一个整型')
            ->between(10, 100, TRUE, '页大小只能为10-100')
            ->validate('page_size');

        $validator
            ->integer('当前页必须是一个整型')
            ->min(1, TRUE, '当前页最小为1')
            ->validate('current_page');
        if (isset($this->data['sort'])) {
            $validator
                ->oneOf('DESC,ASC', '排序有误')
                ->validate('sort');
        }
        if (isset($this->data['orderBy'])) {
            $validator
                ->required('排序类别不能为空')
                ->oneOf('reply_count,create_time')
                ->validate('orderBy');
        }
        return $this->returnValidate($validator);

    }


    public function addModulesValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        $validator->required('插件名称不能为空')->validate('name');
        $validator->required('插件作者不能为空')->validate('author');
        $validator->required('插件描述不能为空')->validate('descr');
        $validator->required('插件logo不能为空')->validate('logo');
        $validator->required('版本信息不能为空')->validate('version');
        $validator->required('版本信息不能为空')->validate('dir_name');
        return $this->returnValidate($validator);

    }

    public function delModulesValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        $validator
            ->required('该参数值不能为空')
            ->validate('dir_name');


        return $this->returnValidate($validator);
    }
}