<?php
/**
 * @className  ：帖子接口数据字段验证
 * @description：对接口传入的参数进行验证及过滤
 * @author     :calfbbs技术团队
 * Date        : 2017年10月30日 23:25:57
 */

namespace Addons\api\validate;

use framework\library\Validator;
use \Addons\api\validate\BaseValidate;

class PostValidate extends BaseValidate
{

    public $data;

    public function __construct(array $data = [])
    {
        $this->data = $this->trimStrings($data);
    }

    /** 插入数据传入参数验证
     *
     * @param array $data
     */
    public function addPostValidate()
    {
        $validator = new Validator($this->data);
        $validator
            ->required('用户不能为空')
            ->integer('用户必须是一个整型integer')
            ->min(1, TRUE, '用户最小为1')
            ->validate('uid');
        $validator
            ->integer('分类必须是一个整型integer')
            ->min(1, TRUE, '分类最小为1')
            ->validate('cid');
        $validator
            ->minLength(1, '标题不能为空')
            ->maxLength(255, '字符太长,不能超过255字节')
            ->validate('title');
        $validator
            ->filter(function($val) {
                $val = preg_replace("/<[^><]*script[^><]*>/i",'',$val);
                return $val;
            })
            ->minLength(1, '内容不能为空')
            ->validate('text');

        //if (isset($data['description'])) {
//        $validator
//            ->minLength(1, '描述不能为空')
//            //->maxLength(4000, '描述字符太长')
//            ->validate('description');
//        //}

        /*$validator
            ->required('该参数值不能为空')
            ->oneOf('0,1,2,3', '该参数值有误')
            ->validate('status');*/

        return $this->returnValidate($validator);
    }

    /** 更新数据传入参数验证
     *
     * @param array $data
     */
    public function changePostValidate()
    {
        $validator = new Validator($this->data);

        $validator
            ->integer('帖子id必须是一个整型integer')
            ->min(1, TRUE, '帖子id最小为1')
            ->validate('id');

        $validator
            ->integer('分类必须是一个整型integer')
            ->min(1, TRUE, '分类最小为1')
            ->validate('cid');

        $validator
            ->integer('用户必须是一个整型integer')
            ->min(1, TRUE, '用户最小为1')
            ->validate('uid');

        $validator
            ->integer('编辑者必须是一个整型integer')
            ->min(1, TRUE, '编辑者最小为1')
            ->validate('edit_uid');

        $validator
            ->minLength(1, '标题不能为空')
            ->maxLength(255, '标题字符太长')
            ->validate('title');

        $validator
            ->filter(function($val) {
                $val = preg_replace("/<[^><]*script[^><]*>/i",'',$val);
                return $val;
            })
            ->minLength(1, '内容不能为空')
            ->validate('text');

        //if (isset($data['description'])) {
//        $validator
//            ->minLength(1, '描述不能为空')
//            //->maxLength(4000, '描述字符太长')
//            ->validate('description');
        //}

        /**
         * 状态
         */
        if (isset($this->data['status'])) {
            $validator
                ->oneOf('0,1,2', '帖子状态有误')
                ->validate('status');
        }


        if (isset($this->data['top'])) {
            $validator
                ->oneOf('0,1', '置顶参数值有误')
                ->validate('top');
        }

        return $this->returnValidate($validator);
    }

    /**
     * 删除数据传入参数验证
     *
     * @param array $data
     */
    public function delPostValidate()
    {
        $validator = new Validator($this->data);

        $validator
            ->integer('帖子id必须是一个整型integer')
            ->min(1, TRUE, '帖子id最小为1')
            ->validate('id');

        $validator
            ->integer('用户id必须是一个整型integer')
            ->min(1, TRUE, '用户id最小为1')
            ->validate('uid');

        return $this->returnValidate($validator);
    }

    /**
     * 获取帖子数据列表传入参数验证
     *
     * @param array $data
     */
    public function getPostListValidate()
    {
        $validator = new Validator($this->data);

        if (isset($this->data['cid']))
            $validator
                ->integer('分类必须是一个整型')
                ->min(1, TRUE, '分类最小为1')
                ->validate('cid');

        if (isset($this->data['uid']))
            $validator
                ->integer('用户id必须是一个整型')
                ->min(1, TRUE, '用户id最小为1')
                ->validate('uid');

        if (isset($this->data['status']))
            $validator
                ->oneOf('0,1,2', '帖子状态有误')
                ->validate('status');

        if (isset($this->data['title'])) {
            $validator
                ->maxLength(255, '标题太长')
                ->validate('title');
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

    /**
     * @function 获取后台 帖子数据列表传入参数验证
     *
     * @param array $data
     *
     * @return mixed
     */
    public function getPostListByAdminValidate()
    {
        $validator = new Validator($this->data);

        if (isset($this->data['cid'])) {
            $validator
                ->integer('分类必须是一个整型integer')
                ->min(1, TRUE, '分类最小为1')
                ->validate('cid');
        }
        if (isset($this->data['uid'])) {
            $validator
                ->integer('用户id必须是一个整型integer')
                ->min(1, TRUE, '用户id最小为1')
                ->validate('uid');
        }
        if (isset($this->data['title'])) {
            $validator
                ->maxLength(255, '标题太长')
                ->validate('title');
        }
        /**
         * 管理员id
         */
        //$validator
        //    ->required('管理员id必填')
        //    ->integer('管理员id必须是一个整型integer')
        //    ->min(1, TRUE, '参数最小为1')
        //    ->validate('admin_id');
        $validator
            ->integer('页大小必须是一个整型integer')
            ->between(10, 100, TRUE, '页大小只能为10-100')
            ->validate('page_size');
        $validator
            ->integer('当前页必须是一个整型integer')
            ->min(1, TRUE, '当前页最小为1')
            ->validate('current_page');
        $validator
            ->oneOf('DESC,ASC', '排序方式有误')
            ->validate('sort');
        $validator
            ->oneOf('0,1,2', '帖子状态有误')
            ->validate('status');

        return $this->returnValidate($validator);
    }

    /**
     * @function 帖子详情验证
     *
     * @param array $data
     *
     * @return mixed
     */
    public function getPostValidate()
    {
        $validator = new Validator($this->data);

        $validator
            ->required('帖子id必填')
            ->integer('帖子id必须是一个整型')
            ->min(1, TRUE, '帖子id最小为1')
            ->validate('id');

        return $this->returnValidate($validator);
    }

    /**
     * 点赞数、访问量传入参数验证
     *
     * @param array $data
     */
    public function changeVisitReliesValidate()
    {
        $validator = new Validator($this->data);

        $validator
            ->integer('帖子id必须是一个整型integer')
            ->min(1, TRUE, '帖子id最小为1')
            ->validate('id');

        $validator
            ->required('类型不能为空')
            ->oneOf('1,2', '类型有误,不符合约定')
            ->validate('type');
        if ( !empty($this->data['action'])) {
            $validator
                ->required('模式不能为空')
                ->oneOf('add,minus', '模式有误,不符合约定')
                ->validate('action');
        }
        return $this->returnValidate($validator);
    }


    /**
     * @function 本周热议验证
     *
     * @param array $data
     */
    public function getTimeMaxValidate()
    {
        $validator = new Validator($this->data);

        $validator
            ->required('获取数量不能为空')
            ->integer('获取数量必须是整数')
            ->min(1, true, '获取数量最小为1')
            ->validate('num');

        $validator
            ->required('排序类别不能为空')
            ->oneOf('reply_count,visits_count,thumb_cnt')
            ->validate('orderBy');

        $validator
            ->required('排序方式不能为空')
            ->oneOf('DESC,ASC')
            ->validate('sort');

        return $this->returnValidate($validator);
    }

    /**
     * @function 置顶参数验证
     *
     * @param array $data
     *
     * @return mixed
     */
    public function getTopPostsValidate()
    {
        $validator = new Validator($this->data);

        $validator
            ->integer('置顶参数不能为空')
            ->oneOf('0,1', '置顶参数不符合约定')
            ->validate('top');

        if (isset($this->data['cid'])) {
            $validator
                ->integer('分类必须是整数')
                ->validate('cid');
        }

        $validator
            ->required('数量不能为空')
            ->integer('数量必须是整数')
            ->between(1, 20, TRUE, '数量只能为1-20')
            ->validate('num');

        return $this->returnValidate($validator);
    }


    /** 获取单条帖子数据传入参数验证
     *
     * @param array $data
     */
    public function getPostOneValidate()
    {
        $validator = new Validator($this->data);
        $validator
            ->requestMethod('GET');
        $validator
            ->integer('帖子id必须是一个整型integer')
            ->validate('id');

        return $this->returnValidate($validator);

    }


    /** 获取用户发帖量
     * @param int $uid
     */
    public function getUserNum(array $data=array()){
        $validator = new \Framework\library\Validator($data);
        $validator
            ->requestMethod('GET');
        $validator
            ->integer('该参数值必须是一个整型integer')
            ->validate('uid');
        return $this->returnValidate($validator);

    }

}