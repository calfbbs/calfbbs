<?php
/**
 * @className：api应用调用类
 * @description：api调用
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */

namespace Addons\admin\model;

use \Curl\Curl;
class ApiModel
{
    public static $curl;

    /**
     * 设置默认的APPTOKEN到请求头部
     */
    public function __construct()
    {
        global $_G;

        if(!self::$curl){
            /**
             * 如果是使用curl 请求接口
             */
            if(@$_G['config']['REQUEST_API']=="curl"){
                self::$curl=new Curl();

            }else{
                /**
                 * 直接本地调用接口
                 */
                self::$curl=new \Addons\api\controller\Api();
            }
            self::$curl->setHeader('APPTOKEN',$_G['config']['APPTOKEN']);
        }

    }

    /**curl get方法
     * @param $url
     *
     * @return mixed
     */
    public function get($url, $data = array()){
        return self::$curl->get($url,$data);
    }

    /**curl post 方法
     * @param       $url
     * @param array $data
     *
     * @return mixed
     */
    public function post($url, $data = array()){
        return self::$curl->post($url, $data);
    }


}