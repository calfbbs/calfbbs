<?php
/**
 * @className：微信用户表相关数据库模型
 * @description：微信用户数据操作
 * @author:calfbb技术团队
 * Date: 2018/03/29
 */
namespace  App\model;

use PDO;//引入pdo类库，确保php开启pdo扩展
use Exception;
use PDOException;
class RegisterModel extends  ApiModel
{
	//获取用户信息
	public function getRegisterOne($param)
	{
		global $_G;
        $data=$this->get(url("login/register/getRegisterInfo"),$param);
        if($data->code==1001 && $data->data){
            return  (array)$data->data;
        }
        return [];
	}
    //删除用户信息
    public function delRegister($param){
        $where['type']='uid';
        $where['uid']=$param['uid'];
        $data=$this->get(url("login/register/getRegisterInfo"),$where);

        if($data->code==1001 && $data->data){
            $id=$data->data->id;
            $type=$param['type'];
            $data=$this->get(url("login/register/deleteRegister"),['id'=>$id,'type'=>$type]);
            return $data;
        }
    }
    // public function logger($content){
    //     $logsize=100000;
    //     $log="/www/web/default/log.txt";
    //     if(file_exists($log)&&filesize($log)>$logsize){
    //         unlink($log);
    //     }
    //     file_put_contents($log,date('H:i:s')." ".$content."\n",FILE_APPEND);
    // } 

	
}
