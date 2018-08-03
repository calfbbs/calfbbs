<?php
/**
 * @className  ：帖子接口数据模型
 * @description：增加帖子，删除帖子，编辑帖子，查询帖子，更新点赞、访问量
 * @author     :calfbbs技术团队
 * Date        : 2017年10月30日 23:28:08
 */

namespace Addons\api\model;

use Addons\api\model\BaseModel;
use think\facade\Session;

class PostModel extends BaseModel
{
    /**
     * @var string $tableName 帖子表
     */
    private static $tableName = "posts";

    /**
     * @var string 用户表
     */
    private static $userTableName = "user";

    /**
     * @var string 分类表
     */
    private static $classfyTableName = "classify";


    /** 插入一条数据到帖子
     *
     * @param array $data 传入数据
     *
     * @return int | bool $result
     */
    public function insertPost($data)
    {
        $result = db_insert(self::$tableName, $data);

        return $result;
    }

    /** 更新一条数据到帖子
     *
     * @param array $data 传入数据
     *
     * @return int | bool $result
     */
    public function updatePost($data, $where)
    {
        $result = db_update(self::$tableName, $data, $where);

        return $result;
    }

    /**
     * 假删除一条帖子数据
     *
     * @param array $data 传入数据
     *
     * @return int | bool $result
     */
    public function deletePost($data, $where)
    {
        return db_update(self::$tableName, $data, $where);
    }

    /** 真删除一条帖子数据
     *
     * @param array $data 传入数据
     *
     * @return int | bool $result
     */
    public function deleteRealPost($where)
    {
        return db_delete(self::$tableName, $where);
    }

    /**
     * 获取帖子数据列表
     *
     * @param array $data 传入数据
     *
     * @return array | bool $result
     */
    public function selectPost($data, $column = '*')
    {
        $sql = 'SELECT ' . $column . ' FROM ' . table_prefix(self::$tableName) . ' AS p LEFT JOIN ' . table_prefix(self::$userTableName) .
            ' AS u ON u.uid = p.uid LEFT JOIN ' . table_prefix(self::$classfyTableName) . ' AS c ON c.id = p.cid ';

        /**
         * 条件
         */
        if ( !isset($data['status'])) {
            $sql              .= 'WHERE p.`status` != :status ';
            $where[':status'] = 3;
        } else {
            $sql              .= 'WHERE p.`status` = :status ';
            $where[':status'] = $data['status'];
        }

        if ( !empty($data['cid'])) {
            $sql           .= 'AND p.`cid` = :cid ';
            $where[':cid'] = $data['cid'];
        }


        if ( !empty($data['uid'])) {
            $sql           .= 'AND p.`uid`=:uid';
            $where[':uid'] = $data['uid'];
        }

        if ( !empty($data['title'])) {
            $sql             .= ' AND title LIKE :title';
            $where[':title'] = "%" . $data['title'] . "%";
        }

        /**
         * 利用索引进行分页 倒序时不可用
         */
         //$sql .= ' and p.id > '.($data['current_page'] - 1) * $data['page_size'] ;

        /**
         * 排序
         */
        if ( !empty($data['orderBy']) && !empty($data['sort'])) {
            $sql .= ' ORDER BY p.' . $data['orderBy'] . ' ' . $data['sort'];
        }


        /**
         * 限制条数
         */
        $sql .= ' LIMIT ' . ($data['current_page'] - 1) * $data['page_size'] . "," . $data['page_size'];

        return db_query($sql, $where);
    }

    /**
     * @function
     *
     * @param        $data
     * @param string $column
     *
     * @return mixed
     */
    public function selectPostByAdmin($data, $column = '*')
    {
        $sql = "SELECT $column FROM " . table_prefix(self::$tableName) .
            ' AS p LEFT JOIN ' . table_prefix(self::$userTableName) . " AS u ON u.uid = p.uid" .
            ' LEFT JOIN ' . table_prefix(self::$classfyTableName) . ' AS c ON c . id = p . cid ';

        if (is_bool($data['status'])) {
            $sql .= 'WHERE p . status != :status ';
            /**
             * 3已删除
             */
            $where[':status'] = 3;
        } else {
            $sql .= 'WHERE p . status = :status ';

            $where[':status'] = $data['status'];
        }

        if ( !empty($data['cid'])) {
            $sql .= ' AND cid =:cid ';

            $where[':cid'] = $data['cid'];

        }

        if ( !empty($data['uid'])) {
            $sql .= ' AND uid =:uid ';

            $where[':uid'] = $data['uid'];
        }

        if ( !empty($data['title'])) {
            $sql .= ' AND title LIKE :title';

            $where[':title'] = "%" . $data['title'] . "%";
        }

        /**
         * 利用索引进行分页 倒序时不可用
         */
        // $sql .= ' and p.id > '.($data['current_page'] - 1) * $data['page_size'] ;

        /**
         * 排序
         */
        $sql .= ' ORDER BY p . create_time ' . $data['sort'];


        /**
         * 限制条数
         */
        $sql .= ' LIMIT ' . ($data['current_page'] - 1) * $data['page_size'] . "," . $data['page_size'];


        return db_query($sql, $where);
    }

    /**
     * 统计帖子数据总条数
     *
     * @param array $data 传入数据
     *
     * @return int | bool $result
     */
    public function countPost($data)
    {

        $sql = 'SELECT count(*) AS num FROM ' . table_prefix(self::$tableName);

        if ( !isset($data['status'])) {
            $sql              .= ' WHERE `status` != :status';
            $where[':status'] = 3;
        } else {
            $sql              .= ' WHERE `status` = :status';
            $where[':status'] = $data['status'];
        }

        if ( !empty($data['uid'])) {
            $sql           .= ' AND `uid` = ' . ':uid';
            $where[':uid'] = $data['uid'];

        }

        if ( !empty($data['cid'])) {
            $sql           .= ' AND cid = :cid';
            $where[':cid'] = $data['cid'];
        }

        if ( !empty($data['title'])) {
            $sql             .= ' AND title LIKE :title';
            $where[':title'] = '%' . $data['title'] . '%';
        }


        return db_fetch($sql, $where)['num'];
    }

    /**
     * 验证管理员身份
     *
     * @param $uid
     */
    public function checkAdminUser($uid)
    {
        $user = db_fetch("SELECT `status` FROM " . table_prefix('user') . " WHERE uid = :uid LIMIT 1", [':uid' => $uid]);
        if ( !empty($user) && $user['status'] == 2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证帖子存在
     *
     * @param string $id 帖子id
     *
     * @return bool
     */
    public function hasPost($id)
    {
        return db_has(self::$tableName, ['id' => $id]);
    }

    /**
     * 获取单条帖子记录
     *
     * @param string $id 帖子id
     *
     * @return bool
     */
    public function findPost($id, $column = '*')
    {
        return db_find(self::$tableName, $column, ['id' => $id]);
    }

    /**
     * 更新访问量
     *
     * @param string $id 帖子id
     *
     * @return bool
     */
    public function updateVisit($id)
    {
        return db_query("UPDATE " . table_prefix('posts') . " SET `visits_count` = `visits_count`+ 1 WHERE id = :id",
            [':id' => $id]);
    }

    /**
     * 更新回帖数
     *
     * @param string $id 帖子id
     *
     * @return bool
     */
    public function updateRelies($data)
    {
        if ($data['action'] == "add") {
            $action = "`reply_count`+ 1";
        } else if ($data['action'] == "minus") {
            $action = "`reply_count`- 1";
        } else {
            $action = "`reply_count`+ 1";
        }
        return db_query("UPDATE " . table_prefix('posts') . " SET `reply_count` = $action WHERE id = :id",
            [':id' => $data['id']]);
    }

    /**
     * @function 某段时间之内帖子排序
     *
     * @param        $begin
     * @param        $end
     * @param string $column
     * @param string $num
     *
     * @return mixed
     */
    public function getMaxList($begin, $end, $column = '*', $num = '10', $orderBy, $sort)
    {
        $sql = 'SELECT ' . $column . ' FROM ' . table_prefix(self::$tableName) . ' WHERE `status` != 3 AND `create_time` BETWEEN :begin AND :end ORDER BY ' . $orderBy . ' ' . $sort . ' LIMIT ' . $num;

        $where = [
            ':begin' => $begin,
            ':end'   => $end,
        ];

        return db_query($sql, $where);
    }

    /**
     * @function 获取置顶帖子
     *
     * @param $data
     * @param $column
     *
     * @return mixed
     */
    public function getTopPostsList($data, $column = '*')
    {
        $sql = 'SELECT ' . $column . ' FROM ' . table_prefix(self::$tableName) . ' AS p LEFT JOIN ' . table_prefix(self::$userTableName) .
            ' AS u ON u . uid = p . uid LEFT JOIN ' . table_prefix(self::$classfyTableName) . ' AS c ON c . id = p . cid WHERE p . `status` != 3 AND p . `top` = :top ';


        if ( !empty($data['cid'])) {
            $sql          .= ' AND `cid` = :cid ';
            $where['cid'] = $data['cid'];
        }
        $sql .= " ORDER BY p.id ASC ";
        $sql .= 'LIMIT ' . $data['num'];

        $where[':top'] = $data['top'];

        return db_query($sql, $where);
    }
    /**
     * @param 获取用户帖子数量
     * @var uid
     * */
    public function getUserPostModel($uid)
    {
       $sql = 'SELECT count(*) AS num  FROM '.table_prefix(self::$tableName) ." where uid = ".$uid;
       return db_query($sql);
    }
}