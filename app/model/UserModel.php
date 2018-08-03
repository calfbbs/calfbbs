<?php
/**
 * @className：用户表相关数据库模型
 * @description：用户数据操作
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */
namespace  App\model;

use PDO;//引入pdo类库，确保php开启pdo扩展
use Exception;
use PDOException;
class UserModel extends  ApiModel
{
	//获取用户信息
	public function getUserOne($uid)
	{
		global $_G;
        $where['uid'] = $uid;
        $data=$this->get(url("api/user/getUserInfo"),$where);

        if($data->code==1001 && $data->data){
            return  (array)$data->data;
        }
        return [];
	}

	/**
     * 最近的回答
     */
    public function getAnswers($uid){
        $where['uid'] = $uid;
        $where['page_size'] = 20;
        $where['current_page'] = 1;
        $data=$this->get(url("api/user/getAnswers"),$where);

        if($data->code==1001 && $data->data->list){
            return  (array)$data->data->list;
        }
        return [];
    }


	/**
     * 最新的提问
     */
	public function getQuestions($uid){
        $where['uid'] = $uid;
        $where['page_size'] = 20;
        $where['current_page'] = 1;
        $data=$this->get(url("api/user/getQuestions"),$where);

        if($data->code==1001 && $data->data->list){
            return  (array)$data->data->list;
        }
        return [];
    }
}