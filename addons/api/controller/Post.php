<?php

/**
 * @className  ：帖子接口管理
 * @description：增加帖子，删除帖子，编辑帖子，查询帖子
 * @author     :calfbbs技术团队
 * Date        : 2017年11月26日 15:29:20
 */

namespace Addons\api\controller;

use Addons\api\model\PostModel;
use Addons\api\validate\PostValidate;

class Post extends PostModel
{
    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();
    }

    /**
     * method:POST
     * 添加帖子方法
     *
     * @param int    $uid   关联发帖用户id
     * @param int    $cid   绑定分类id
     * @param string $title 帖子的标题
     * @param string $text  帖子内容
     *
     * @return array $data   响应数据
     */
    public function addPost()
    {
        /**
         * 移除表情字符串
         */
        $this->post['title'] = $this->remove_emoji($this->post['title']);

        $validate = new PostValidate($this->post);

        $validateResult = $validate->addPostValidate();

        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }

        /**
         * 初始化数据
         */
        $validateResult['status']       = 0;
        $validateResult['reply_count']  = 0;
        $validateResult['visits_count'] = 0;
        $validateResult['thumb_cnt']    = 0;
        $validateResult['create_time']  = time();
        $validateResult['change_time']  = time();

        /**
         * 插入数据到数据库
         */
        $result = $this->insertPost($validateResult);


        if ($result) {
            return $this->returnMessage(1001, '响应成功', (int)$result);
        } else {
            return $this->returnMessage(2001, '响应错误', $result);
        }
    }



    /**
     * method:POST
     * 更新帖子
     *
     * @param int    $id       帖子id
     * @param int    $uid      关联发帖用户id
     * @param int    $edit_uid 修改者uid
     * @param int    $cid      绑定分类id
     * @param string $title    帖子的标题
     * @param string $text     帖子内容
     * @param int    $status   帖子状态：0未解决，1已解决，2精华
     *
     * @return array $data   响应数据
     */
    public function changePost()
    {
        /**
         * 移除表情字符串
         */
        $this->post['title'] = $this->remove_emoji($this->post['title']);

        $validate       = new PostValidate($this->post);
        $validateResult = $validate->changePostValidate();
        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }

        $result = $this->findPost($validateResult['id']);

        if ( !$result) {
            return $this->returnMessage(2001, '响应错误', ['id' => '该帖不存在']);
        }


        if (($result['uid'] == $validateResult['edit_uid'] && $result['uid'] == $validateResult['uid']) || $this->checkAdminUser($validateResult['edit_uid'])) {
            unset($validateResult['edit_uid']);

            $validateResult['change_time'] = time();

            $result = $this->updatePost($validateResult, ['id' => $validateResult['id']]);

            if ($result) {
                return $this->returnMessage(1001, '响应成功', $result);
            } else {
                return $this->returnMessage(2001, '响应错误', $result);
            }


        }
        return $this->returnMessage(2001, '响应错误', ['edit_uid' => '用户权限不足']);
    }

    /**
     * method:POST
     * 删除帖子
     *
     * @param int $id  帖子id
     * @param int $uid 管理员id
     */
    public function delPost()
    {
        $validate       = new PostValidate($this->post);
        $validateResult = $validate->delPostValidate();
        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }
        $result = $this->checkAdminUser($validateResult['uid']);

        if ( !$result) {
            return $this->returnMessage(2001, '响应错误', ['uid' => '不是管理员，权限不够']);
        }


        $result = $this->findPost($validateResult['id']);

        if ( !$result) {
            return $this->returnMessage(2001, '响应错误', ['id' => '该帖不存在']);
        } else if ($result['status'] == 3) {
            return $this->returnMessage(2001, '响应错误', ['id' => '该帖已删除']);
        } else {
            $data = ['status' => 3, 'change_time' => time()];
        }

        /**
         * 传入数据到数据库
         */
        $result = $this->deletePost($data, ['id' => $validateResult['id']]);

        if ($result) {
            return $this->returnMessage(1001, '响应成功', $result);
        } else {
            return $this->returnMessage(2001, '响应错误', $result);
        }
    }

    /**
     * method: GET
     * 获取帖子列表
     *
     * @param int    $cid          绑定分类id
     * @param int    $uid          用户id
     * @param int    $current_page 当前页
     * @param int    $page_size    每页显示数量
     * @param string $sort         排序
     * @param int    $status       状态：0未解决，1已解决，2精华
     */
    public function getPostList()
    {
        $get = $this->getDefaultPage($this->get);

        $validate       = new PostValidate($get);
        $validateResult = $validate->getPostListValidate();
        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }

        /**
         * 获取当前页数总条数
         */
        $count = $this->countPost($validateResult);

        if ($count > 0) {
            $column = 'p.id, p.cid, p.uid, p.title, p.reply_count, p.visits_count, p.status, p.top, p.thumb_cnt, p.description, p.create_time, p.change_time,u.username, u.avatar, c.name as cname';
            $result = $this->selectPost($validateResult, $column);
        }

        $data['pagination'] = $this->getPagination($validateResult['page_size'], $validateResult['current_page'], $count);

        $data['list'] = empty($result) ? [] : $result;
        return $this->returnMessage(1001, '响应成功', $data);
    }

    /**
     * @function 后台获取帖子列表
     */
    public function getPostListByAdmin()
    {
        $post = $this->getDefaultPage($this->post);

        $validate = new PostValidate($post);

        $validateResult = $validate->getPostListByAdminValidate();
        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }


        //!$this->checkAdminUser($validateResult['admin_id']) &&
        //show_json(['code' => 2001, 'message' => '响应错误', 'data' => ['uid' => '不是管理员，权限不够']]);

        /**
         * 获取当前页数总条数
         */
        $count = $this->countPost($validateResult);

        if ($count > 0) {
            /**
             * 查询字段，可变
             */
            $column = 'p.* ,u.username,c.name';

            $result = $this->selectPostByAdmin($validateResult, $column);
        }

        $data['pagination'] = $this->getPagination($validateResult['page_size'], $validateResult['current_page'], $count);

        $data['list'] = empty($result) ? [] : $result;
        return $this->returnMessage(1001, '响应成功', $data);
    }

    /**
     * @function 帖子详情
     *
     * @param string $id 帖子id
     */
    public function getPost()
    {
        $validate       = new PostValidate($this->get);
        $validateResult = $validate->getPostValidate();
        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }
        $result = $this->findPost($validateResult['id']);
        if ( !$result) {
            return $this->returnMessage(2001, '响应错误', ['id' => '该帖不存在']);
        }
        //else if ($result['status'] == 3) {
        //    return $this->returnMessage(2001, '响应错误', ['id' => '该帖已删除']);

        //}
        return $this->returnMessage(1001, '响应成功', $result);
    }


    /**
     * method:GET
     * 更新点赞数、访问量
     *
     * @param int $id   帖子id
     * @param int $type 类型 (1-访问量 2-点赞数)
     *
     * @return array $data   响应数据
     */
    public function changeVisitRelies()
    {
        $validate       = new PostValidate($this->get);
        $validateResult = $validate->changeVisitReliesValidate();
        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }
        $result = $this->hasPost($validateResult['id']);
        if ( !$result) {
            return $this->returnMessage(2001, '响应错误', ['id' => '该帖不存在']);
        }

        $result = $validateResult['type'] == 1 ? $this->updateVisit($validateResult['id']) :
            $this->updateRelies($validateResult);

        if ($result) {
            return $this->returnMessage(1001, '响应成功', $result);
        } else {
            return $this->returnMessage(2001, '响应错误', $result);
        }
    }

    /**
     * 提取分页信息
     *
     * @param int $page_size    每页显示数量
     * @param int $current_page 当前页码
     * @param int $count        总条数
     *
     * @return array
     */
    public function getPagination($page_size, $current_page, $count)
    {
        $pagination['total']        = (int)$count;
        $pagination['page_count']   = $count > 0 ? ceil($count / $page_size) : 0;
        $pagination['current_page'] = (int)$current_page;
        $pagination['page_size']    = (int)$page_size;

        return $pagination;
    }

    /**
     * 获取默认分页参数
     *
     * @param array $data 分页预处理数据
     *
     * @return array
     */
    public function getDefaultPage($data)
    {
        $data['page_size']    = empty($data['page_size']) ? 10 : $data['page_size'];
        $data['current_page'] = empty($data['current_page']) ? 1 : $data['current_page'];
        $data['sort']         = empty($data['sort']) ? 'DESC' : $data['sort'];

        return $data;
    }


    /**
     * @function 本周热议、点赞、访问量排序
     */
    public function getTimeMax()
    {
        $validate = new PostValidate($this->get);

        $validateResult = $validate->getTimeMaxValidate();
        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }

        /**
         * 查询字段
         */
        $column = 'id,title,create_time,' . $validateResult['orderBy'];

        $end = time();

        $begin = $end - 7 * 24 * 3600;

        $data = $this->getMaxList($begin, $end, $column, $validateResult['num'], $validateResult['orderBy'], $validateResult['sort']);


        if ($data){
            return $this->returnMessage(1001,'响应成功', $data);
        }else{
            return $this->returnMessage(2001,'响应错误', $data);
        }
    }

    /**
     * @function 置顶列表
     */
    public function getTopPosts()
    {
        $validate = new PostValidate($this->get);

        $validateResult = $validate->getTopPostsValidate();

        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }
        $column = 'p.id, p.cid, p.uid, p.title, p.reply_count, p.visits_count, p.status, p.thumb_cnt, p.create_time, p.change_time, u.username, u.avatar, c.name as cname';
        $data   = $this->getTopPostsList($validateResult, $column);

        if ($data) {
            return $this->returnMessage(1001, '响应成功', $data);
        } else {
            return $this->returnMessage(2001, '响应错误', $data);
        }
    }


    /**
     * 获取单条帖子数据
     *
     * @param int $id 帖子数据id
     */
    public function getPostOne()
    {
        $validate       = new PostValidate($this->get);
        $validateResult = $validate->getPostOneValidate();
        /**
         * 判断验证是否有报错信息
         */
        if (@$validateResult->code == 2001) {
            return $validateResult;
        }
        $result = $this->findPost($validateResult['id']);

        if ($result) {
            return $this->returnMessage(1001, '响应成功', $result);
        } else {
            return $this->returnMessage(2001, '响应错误', $result);
        }
    }
    /**
     * 获取用户发帖数据量
     * @param uid--session-<
     * */
    public function getUserPostNum()
    {

        $validate       = new PostValidate();
        $validateResult = $validate->getUserNum($this->get);
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        $result = $this->getUserPostModel($validateResult['uid']);
        if ($result) {
            return $this->returnMessage(1001,'当前用户发帖量数据获取成功', $result);
        } else {
            return $this->returnMessage(2001,'当前用户发帖量数据获取失败', $result);
        }
    }



    /**
     * @function 字符串汉字（UTF-8）数量
     * @author   Felix <Fzhengpei@gmail.com>
     *
     * @param      $limit
     * @param bool $include
     * @param      $string
     *
     * @return bool
     */
    public function minCharacter($limit, $include = true, $string)
    {
        $num = mb_strlen(preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $string), 'utf-8');
        return ($num > $limit || ($include === TRUE && $num === $limit));
    }

    /**
     * @function 移除表情
     * @author   Felix <Fzhengpei@gmail.com>
     *
     * @param $text
     *
     * @return mixed
     */
    public function remove_emoji($text)
    {
        return preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $text);
    }

}