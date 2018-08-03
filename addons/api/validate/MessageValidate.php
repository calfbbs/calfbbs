<?php
/**
 * @className：消息接口数据字段验证
 * @description：对接口传入的参数进行验证及过滤
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */


namespace Addons\api\validate;

use \Addons\api\validate\BaseValidate;
class MessageValidate  extends BaseValidate
{


    /** 删除数据传入参数验证
     * @param array $data
     */
    public function delMessageValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        $validator
            ->required('该参数值不能为空')
            ->integer('传入的id只能为整数类型')
            ->validate('id');
        $validator
            ->required('该参数值不能为空')
            ->integer('传入的uid只能为整数类型')
            ->validate('puid');
        return $this->returnValidate($validator);

    }

    /** 获取消息数据列表传入参数验证
     * @param array $data
     */
    public function getMessageListValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);

        if(!empty($data['uid'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('uid');
        }
        if(!empty($data['is_read'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('is_read');
        }

        if(!empty($data['page_size'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('page_size');
        }
        if(!empty($data['current_page'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('current_page');
        }

        $validator
            ->integer('通知puid错误')
            ->validate('puid');

        return $this->returnValidate($validator);

    }

    /**
     * 添加消息
     * @param uid 通知人id
     * @param puid 被通知人id
     * @param posts_id 通知的帖子id
     * */
    public function addMassageValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator->integer('通知人参数错误')->validate('uid');
        $validator->integer('被通知人参数错误')->validate('puid');
        $validator->integer('通知帖子id参数错误')->validate('posts_id');
        return $this->returnValidate($validator);
    }

    /**
     * 清空消息验证
     * @param puid 通知人puid
     * */
    public function emptyMassageValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator->integer('被通知人参数错误')->validate('puid');
        return $this->returnValidate($validator);
    }
}