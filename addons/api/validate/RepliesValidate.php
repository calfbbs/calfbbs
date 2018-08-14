<?php
/**
 * @className：帖子回复接口参数校验
 * @description：对接口传入的参数进行验证及过滤
 * @author:calfbbs技术团队
 * Date: 2017/11/16
 * Time: 下午9:28
 */
namespace Addons\api\validate;

class RepliesValidate  extends BaseValidate
{


    /**
     * 插入数据传入参数验证
     * @param array $data
     */

    public function addRepliesValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('puid');

        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('reid');

        if(!empty($data['top'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('top');
        }

        $validator
            ->required('该参数值不能为空')
            ->minlength('1', '该参数必须知识一个非空字符')
            ->validate('reply_text');

        return $this->returnValidate($validator);

    }



    /**
     * php  内存耗点 格式化
     * */

    public function convert($size){
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }



    /** 删除数据传入参数验证
     * @param array $data
     */

    public function delRepliesValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('id');
        return $this->returnValidate($validator);
    }

    /** 删除数据传入参数验证
     * @param array $data
     */

    public function showRepliesValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('reid');
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

    /** 获取单向帖子回复的回复数据
     * @param array $data
     */

    public function getReplyRepliesListValidate(array $data=array())
    {
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('puid');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('reid');

        return $this->returnValidate($validator);

    }

    /**
     *  帖子回复内容的数据修改
     *
     * */

    public function updateRepliestValidate(array $data=array())
    {

        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('id');
        if(!empty($data['top'])){
            $validator
                ->integer('该参数值必须是一个整型integer')
                ->validate('top');
        }

        $validator
            ->required('帖子编辑内容不准为空')
            ->validate('reply_text');


        return $this->returnValidate($validator);


    }

    /** 写入回帖参数验证
     * @param array $data
     *
     * @return mixed
     */
    public function insthumbRepiesValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('rid');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('reid');
        return $this->returnValidate($validator);
    }


    /** 获取回帖用户是否点过赞参数验证
     * @param array $data
     *
     * @return mixed
     */
    public function getPraiseRecordValidate(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('rid');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');
        $validator
            ->required('该参数值不能为空')
            ->integer('该参数值必须是一个整型integer')
            ->validate('reid');
        return $this->returnValidate($validator);
    }
}