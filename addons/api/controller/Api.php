<?php
namespace Addons\api\controller;

class Api
{
    private $m,$c,$a;
    private $param=[];
    /**curl get方法
     * @param $url
     *
     * @return mixed
     */
    public function get($url, $data = array()){
        return $this->action($url,'get',$data);

    }

    /**curl post 方法
     * @param       $url
     * @param array $data
     *
     * @return mixed
     */
    public function post($url, $data = array()){
        return $this->action($url,'post',$data);
    }

    public function setHeader($APPTOKEN,$APPTOKENTWO){

    }

    /** 实例化类,调用方法
     * @param $url
     * @param $action
     * @param $data
     *
     * @return mixed
     */
    private function action($url,$action,$data){
        global $_G;
            $this->pathinfo($url);
            $base=new \Addons\api\model\BaseModel();
            $base::$header=['APPTOKEN'=>$_G['config']['APPTOKEN'],'REQUEST'=>$action,'data'=>array_merge($data,$this->param)];
            $className="\addons\\".$this->m."\controller\\".$this->c;
            $class=new $className();
            $action=$this->a;
            return $class->$action();

    }

    /** 将url参数分解 m模块 c控制器 a方法
     * @param $url
     */
    private function pathinfo($url)
    {
        $url=parse_url($url);
        /**
         * 如果是普通路由
         */
        if(@$url['query']){
            $result= trim(str_replace('=', '&', $url['query'],$count),'&');
            $newUrl=explode('&',$result);
            $this->m=$newUrl[1];
            $this->c=ucfirst($newUrl[3]);
            $this->a=$newUrl[5];
        }else{
            /**
             * 如果是伪静态路由
             */
            $pathStr = str_replace($_SERVER['SCRIPT_NAME'], '', $url['path'],$count);
            $pathStr2 = str_replace($_SERVER['REQUEST_URI'], '', $_SERVER['SCRIPT_NAME'],$count2);
            $path=@trim($pathStr,'?');
            $path=@trim($path,'/');

            if($count < 1 && $count2 > 0){
                $newUrl = explode('/', $path);
                if(!empty($newUrl)){
                    $newUrl = explode('/', $path);
                    $newUrl[2]=$this->delSuffix($newUrl[2]);
                }

            }else{
                $newUrl = explode('/', $path);
                $newUrl[2]=$this->delSuffix($newUrl[2]);
            }
            $this->m=$newUrl[0];
            $this->c=ucfirst($newUrl[1]);
            $this->a=$newUrl[2];

        }
        $newParam=$newUrl;
        $pathLenth = count($newParam);

        /**
         * 最后一尾参数去掉后缀
         */
        if(@isset($newParam[$pathLenth-1])){
            $newParam[$pathLenth-1]=$this->delSuffix($newParam[$pathLenth-1]);
        }

        $i = @$url['query'] && @!empty($_GET['m']) ? 6 : 3;
        while ($i < $pathLenth) {
            if (isset($newParam[$i + 1])) {
                $this->param[$newParam[$i]] = $newParam[$i + 1];
            }
            $i = $i + 2;
        }

    }

    /**
     * 删除后缀
     */
    public function delSuffix($action){
        $route = \Framework\library\conf::all('route');
        if($route['SUFFIX_STATUS']){
            $action=str_replace($route['SUFFIX'],'',$action);
        }
        return $action;

    }

}