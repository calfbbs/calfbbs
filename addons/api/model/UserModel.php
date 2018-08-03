<?php

namespace Addons\api\model;

class UserModel extends BaseModel {
    /**
     * @var string $tableName 用户表名
     */
    private static $tableName = 'user';

    private static $replieTableName = 'replies';

    private static $postTableName = 'posts';

    /**
     * 插入一条用户数据
     * @param array $params 参数
     * @return int  bool  true/false
     */
    public function create(array $params = [])
    {
        return db_insert(self::$tableName, $params);
    }

    /**
     * 更新用户资料
     * @param array $params 参数
     * @param array $where 条件
     * @return bool true/false
     */
    public function update(array $params = [], array $where = [])
    {
        return db_update(self::$tableName, $params, $where);
    }

    /**
     * 删除某个用户
     * @param array $where 条件
     * @return bool true/false
     */
    public function delete(array $where)
    {
        return ! !db_delete(self::$tableName, $where);
    }

    /**
     * 获取一条用户数据
     * @param array $where 条件
     * @param string $fields 需要的字段
     * @return array $data 用户数组集合
     */
    public function getUser(array $where, $fields = "*")
    {
        return db_find(self::$tableName, $fields, $where);
    }


    /**
     * 获取所有用户列表
     * @param array $params 条件
     * @param string $fields 需要查询的字段
     * @return $result 用户数组集合
     */
    public function getUsers(array $params, $fields = "*")
    {
        $where = null;

        if ( !empty($params['email'])) {
            $where['email'] = ['=', $params['email']];
        }

        if ( !empty($params['username'])) {
            $where['username'] = ['like', '%' . $params['username'] . '%'];
        }

        $wheres = null;
        $datas = [];

        $count = count($where);

        if ($count > 0) {
            $wheres = 'where ';
            $i = 0;
            foreach ( $where as $k => $val ) {
                if ($i == 0) {
                    //拼接sql语句
                    $wheres .= $k . ' ' . $val[0] . ' :' . $k . ' ';
                    $datas = [":$k" => $val[1]];
                } else {
                    $wheres .= ' and ' . $k . ' ' . $val[0] . ' :' . $k;
                    $datas[':' . $k] = $val[1];
                }

                $i++;

                if ($i == $count) {
                    $wheres .= ' ';
                }
            }
        }

        $page_size = isset($params['page_size']) ? $params['page_size'] : 10;

        $current_page = isset($params['current_page']) ? $params['current_page'] : 1;

        $limit = ($current_page - 1) * $page_size;

        //获取总页数
        $counts = db_fetch("SELECT count(uid)AS uid FROM " . table_prefix(self::$tableName) . ' ' . $wheres, $datas);

        $counts = isset($counts['uid']) ? $counts['uid'] : 0;

        $result['list'] = db_query("SELECT {$fields} FROM " . table_prefix(self::$tableName) . ' ' . $wheres . 'ORDER BY create_time desc LIMIT ' . $limit . ',' . $page_size, $datas);

        $result['pagination'] = [
            'total'        => $counts,
            'page_count'   => $counts > 0 ? ceil($counts / $page_size) : 0,
            'current_page' => $current_page,
            'page_size'    => $page_size,
        ];

        return $result;
    }

    /**
     * 获取搜索用户列表
     * @param array $params 条件
     * @param string $fields 需要查询的字段
     * @return $result 用户数组集合
     */
    public function getSearchUsers(array $params, $fields = "*")
    {
        $where = null;

        if ( !empty($params['email'])) {
            $where['email'] = ['like', '%' . $params['email'] . '%'];
        }

        if ( !empty($params['username'])) {
            $where['username'] = ['like', '%' . $params['username'] . '%'];
        }

        $wheres = null;
        $datas = [];

        $count = count($where);

        if ($count > 0) {
            $wheres = 'where ';
            $i = 0;
            foreach ( $where as $k => $val ) {
                if ($i == 0) {
                    //拼接sql语句
                    $wheres .= $k . ' ' . $val[0] . ' :' . $k . ' ';
                    $datas = [":$k" => $val[1]];
                } else {
                    $wheres .= ' or ' . $k . ' ' . $val[0] . ' :' . $k;
                    $datas[':' . $k] = $val[1];
                }

                $i++;

                if ($i == $count) {
                    $wheres .= ' ';
                }
            }
        }
        $page_size = isset($params['page_size']) ? $params['page_size'] : 10;

        $current_page = isset($params['current_page']) ? $params['current_page'] : 1;

        $limit = ($current_page - 1) * $page_size;

        //获取总页数
        $counts = db_fetch("SELECT count(uid)AS uid FROM " . table_prefix(self::$tableName) . ' ' . $wheres, $datas);

        $counts = isset($counts['uid']) ? $counts['uid'] : 0;

        $result['list'] = db_query("SELECT {$fields} FROM " . table_prefix(self::$tableName) . ' ' . $wheres . 'ORDER BY create_time desc LIMIT ' . $limit . ',' . $page_size, $datas);

        $result['pagination'] = [
            'total'        => $counts,
            'page_count'   => $counts > 0 ? ceil($counts / $page_size) : 0,
            'current_page' => $current_page,
            'page_size'    => $page_size,
        ];

        return $result;
    }


    /**
     * 获取某个用户的所有最新提问
     * @param array $params 参数
     * @return array $result  问题帖子及分页信息
     */
    public function questions(array $params)
    {
        $page_size = isset($params['page_size']) ? $params['page_size'] : 10;

        $current_page = isset($params['current_page']) ? $params['current_page'] : 1;

        $count = db_count(self::$postTableName, ['uid' => $params['uid']]);

        $result['list'] = db_select(self::$postTableName, "*", ['uid' => $params['uid']], $current_page, $page_size, ['create_time' => 'DESC']);

        $result['pagination'] = [
            'total'        => $count,
            'page_count'   => $count > 0 ? ceil($count / $page_size) : 0,
            'current_page' => $current_page,
            'page_size'    => $page_size,
        ];

        return $result;
    }


    /**
     * 获取某个用户的最新回答
     * @param $params
     * @return array $result  回答数据及分页
     */
    public function answers($params)
    {
        $page_size = isset($params['page_size']) ? $params['page_size'] : 10;

        $current_page = isset($params['current_page']) ? $params['current_page'] : 1;

        $count = db_count(self::$replieTableName, array('uid' => $params['uid']));

        $limit = ($current_page - 1) * $page_size;
        $sql = "SELECT r.*,p.title, p.id AS reid FROM " . table_prefix(self::$replieTableName) . " AS r LEFT JOIN " . table_prefix(self::$postTableName) . " p ON r.reid=p.id  WHERE r.uid=:uid ORDER BY r.top,r.create_time DESC LIMIT " . $limit . ',' . $page_size;

        $result['list'] = db_query($sql, [':uid' => $params['uid']]);

        $result['pagination'] = [
            'total'        => $count,
            'page_count'   => $count > 0 ? ceil($count / $page_size) : 0,
            'current_page' => $current_page,
            'page_size'    => $page_size,
        ];

        return $result;
    }

}