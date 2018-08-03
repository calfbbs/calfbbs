<?php
/**
 * @className：基础类
 * @description：应用验证,用户access验证,公共方法
 * @author:calfbbs技术团队
 * Date: 2017/10/23
 * Time: 下午3:25
 */

namespace Addons\api\model;

class BaseModel
{
    public  static $header;
    public  static $requsetApi="local";
    public  $post;
    public  $get;


    /**
     * 应用token验证
     */
    public function vaildateAppToken(){
        global $_G;
        /**
         * 获取请求header中的APP_TOKEN 并跟当前应用APP_TOKEN比较是否一致
         */
        if(!self::$header){
            self::$header=getallheaders();
            self::$requsetApi="curl";
        }


       /* if($_G['config']['APPTOKEN'] !=@self::$header['APPTOKEN'] && $_G['config']['APPTOKEN'] !=@self::$header['Apptoken']){
            $this->returnMessage(2001,'APPTOKEN验证错误,请检查应用TOKEN是否配置正确',false);
        }*/


        if(self::$requsetApi=='local'){
            $this->get=self::$header['REQUEST']=="get" ? self::$header['data'] : $_GET;
            $this->post=self::$header['REQUEST']=="post" ? self::$header['data'] : $_POST;
        }else{
            $this->get=$_GET;
            $this->post=$_POST;
            $token=@$_GET['token'] ? @$_GET['token'] : @$_GET['post'];
            $this->checkToken($token);
        }

        if(@self::$header['REQUEST']){
            $_SERVER['REQUEST_METHOD']=strtoupper(self::$header['REQUEST']); //将字母大写
        }
    }

    /** 返回信息
     * @param $code 返回的code码
     * @param $message 返回提示信息
     * @param $data  返回的数据
     */
   public function returnMessage($code,$message,$data=false){
       if(self::$requsetApi=="curl"){
           show_json(['code'=>(int)$code,'message'=>$message,'data'=>$data]);
       }else{
           return json_decode(json_encode(['code'=>(int)$code,'message'=>$message,'data'=>$data]));
       }

   }

    /**
     * 提取分页信息
     * @param $page_size
     * @param $current_page
     * @param $total_records
     * @return array
     */
    public  function getPagination($page_size,$current_page,$count)
    {
        $pagination['total'] = (int)$count;
        $pagination['page_count'] = $count>0?ceil($count/$page_size):0;
        $pagination['current_page'] = (int)$current_page;
        $pagination['page_size'] = (int)$page_size;
        return $pagination;
    }

    private function checkToken($token){


        /**
         * 不需要验证的控制器及方法
         */

        $login=$this->notValidate();
        if($login==false){
            if(!$token){
                return  $this->returnMessage(2001,'token不能为空',false);
            }
            $tokenServices=new \Addons\api\services\user\TokenServices();
            return $tokenServices->VerifyToken($token);
        }

    }


    /**
     * 不需要验证的控制器及方法
     */
    private function notValidate(){
        $action=[
            'user'=>[
                'index','login','adduser','forget','resetpassword','logout'
            ],
            'token'=>[
                'getusertoken'
            ]
        ];
        $C=strtolower(C);
        $A=strtolower(A);
        $login=false;
        foreach ($action as $c=>$a){
            if(is_array($a)){
                foreach($a as $v){
                    if($c==$C && $v==$A){
                        $login=true;
                        continue;
                    }
                }

                if($login==true){
                    continue;
                }
            }else{
                if($c==$C && $a==$A){
                    $login=true;
                    continue;
                }
            }

        }
        return $login;
    }

}