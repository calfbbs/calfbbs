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
class NavValidate  extends BaseValidate
{



    /** 插入数据传入参数验证
     * @param array $data
     */
     public function addNavValidate(array $data=array()){
         $validator = new \Framework\library\Validator($data);
         $validator
             ->required('导航栏名称不能为空')
             ->validate('name');
         $validator
             ->required('路径不能为空')
             ->url('导航栏请检查地址是否正确')
             ->validate('path');
         if(!empty($data['sort'])){
             $validator

                 ->integer('该参数值必须是一个整型integer')
                 ->validate('sort');
         }
          if(!empty($data['image_url'])){
              $validator
                  //->url('图片请检查地址是否正确')
                  ->required('导航栏名称不能为空')
                  ->validate('image_url');
          }
         if(!empty($data['status'])){
             $validator
                 ->between(0,1,'状态值请填写1或者2')
                 ->integer('请正确填写状态，必须为整数')
                 ->validate('status');
         }


         return $this->returnValidate($validator);

     }

    /** 更新数据传入参数验证
     * @param array $data
     */
    public function changeNavValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        $validator
            ->required('id不能为空')
            ->integer('id必须是一个整型integer')
            ->validate('id');
    
        if(!empty($data['name'])){
            $validator
                ->required('导航栏不能为空')
                ->validate('name');
        }
        if(!empty($data['path'])){
            $validator
                ->required('导航栏路径不能为空')
                ->url('导航栏请检查地址是否正确')
                ->validate('path');
        }
        if(!empty($data['status'])){
            $validator
                ->required('导航栏状态不能为空')
                ->between(0,1,'状态值请填写1或者2')
                ->integer('导航栏状态必须是一个整型integer')
                ->validate('status');
        }

        if(!empty($data['sort'])){
            $validator
                ->integer('排序值必须是一个整型integer')
                ->validate('sort');
        }
        if(!empty($data['image_url'])){
            $validator
                ->required('导航栏名称不能为空')
               // ->url('图片请检查地址是否正确')
                ->validate('image_url');
        }

        return $this->returnValidate($validator);

    }

    /** 删除数据传入参数验证
     * @param array $data
     */
    public function delNavValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        $validator
            ->required('id不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('id');


        return $this->returnValidate($validator);

    }

    /** 获取广告数据列表传入参数验证
     * @param array $data
     */
    public function getNavListValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        
        $validator
            ->required('page_size不能为空')
            ->integer('page_size必须是一个整型integer')
            ->validate('page_size');
        $validator
            ->required('current_page参数值不能为空')
            ->integer('current_page必须是一个整型integer')
            ->validate('current_page');

        return $this->returnValidate($validator);

    }

    /** 获取单条导航栏数据传入参数验证
     * @param array $data
     */
    public function getNavOneValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->requestMethod('GET');
        $validator
            ->integer('该参数值必须是一个整型integer')
            ->validate('id');


        return $this->returnValidate($validator);

    }

}