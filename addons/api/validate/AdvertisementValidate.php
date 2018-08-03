<?php
/**
 * @className：广告接口数据字段验证
 * @description：对接口传入的参数进行验证及过滤
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */


namespace Addons\api\validate;
use \Addons\api\validate\BaseValidate;
class AdvertisementValidate extends BaseValidate
{

    /** 插入数据传入参数验证
     * @param array $data
     */

    public function addAdvertisementValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->validate('name');
        if(!empty($data['cid'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('cid');
        }

        if(!empty($data['sort'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('sort');
        }
        if(!empty($data['image'])){
            $validator
                ->filter(function($val) {
                    $val = trim($val);
                    return $val;
                })
                ->required('该参数值不能为空')
                ->validate('image');
        }
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->between(1,2,true)
            ->validate('type');
        $validator
            ->required('该参数值不能为空')
            ->url('该参数值必须是一个正确的url')
            ->validate('url_path');
        return $this->returnValidate($validator);

    }


    /** 更新数据传入参数验证
     * @param array $data
     */
    public function changeAdvertisementValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('id');

        if(!empty($data['name'])){
            $validator
                ->required('该参数值不能为空')
                ->validate('name');
        }

        if(!empty($data['cid'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('cid');
        }

        if(!empty($data['sort'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('sort');
        }
        if(!empty($data['image'])){

            $validator
                ->filter(function($val) {
                    $val = trim($val);
                    return $val;
                })->required('该参数值不能为空')
                ->validate('image');
        }
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->between(1,2,true)
            ->validate('type');
        $validator
            ->required('该参数值不能为空')
            ->url('该参数值必须是一个正确的url')
            ->validate('url_path');
        return $this->returnValidate($validator);

    }

    /** 删除数据传入参数验证
     * @param array $data
     */
    public function delAdvertisementValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('id');


        return $this->returnValidate($validator);

    }

    /** 获取广告数据列表传入参数验证
     * @param array $data
     */
    public function getAdvertisementListValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        if(!empty($data['cid'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('cid');
        }


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

    /** 获取单条广告数据传入参数验证
     * @param array $data
     */
    public function getAdvertisementOneValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->requestMethod('GET');
        $validator
            ->integer('该参数值必须是一个整型integer')
            ->validate('id');


        return $this->returnValidate($validator);

    }
}