<?php

/**
 * @className：广告相关接口管理
 * @description：增加广告，删除广告，编辑广告，查询广告
 * @author:calfbbs技术团队
 * Date: 2017/11/04
 * Time: 下午3:25
 */

namespace Addons\api\controller;
use Addons\api\model\NavModel;
use Addons\api\validate\NavValidate;
class Nav extends NavModel
{
    public function __construct()
    {
        /**
         * 验证APP_TOKEN
         */
        $this->vaildateAppToken();
    }

    /**
     * 添加导航栏方法
     * @param varchar  $name   导航栏名称
     * @param varchar  $path   路径名称
     * @param varchar  $image_url   导航栏图标地址
     * @param int $sort 排序
     * @param int $status 导航栏状态
     * @return array $data   响应数据
     */
    public function addNav(){
            /**
             * get 字段参数验证是否符合条件
             */
            $validate=new NavValidate();
            $validateResult=$validate->addNavValidate($this->get);
            /**
             * 判断验证是否有报错信息
             */

            if(@$validateResult->code==2001){
                return $validateResult;
            }
            /**
             * 插入数据到数据库
             */
            $result=$this->insertNav($validateResult);

           
        if($result){
            return $this->returnMessage(1001,'响应成功',(int)$result);

        }else{
            return $this->returnMessage(2001,'响应错误',$result);
        }
    }

    /**
     * 更新导航栏
     * @param id  $id   更新广告id
     * @param varchar  $name   导航栏名称
     * @param int $sort 排序
     * @param string $path 导航栏地址
     * @param int $status 导航栏状态
     * @param varchar  $image_url   导航栏图标地址
     * @return array $data   响应数据
     */
    public function changNav(){
        /**
         * get 字段参数验证是否符合条件
         */

        $validate = new NavValidate();
        $validateResult = $validate->changeNavValidate($this->get);
       
        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }


        /**
         * 判断更新数据是否存在
         */

        $navOne=$this->findNav(['id'=>$validateResult['id']],['id','name']);

        /**
         * 插入数据到数据库
         */
        if($navOne){
            $result=$this->updateNav($validateResult,['id'=>$validateResult['id']]);

            if($result){
                return $this->returnMessage(1001,'响应成功',$result);

            }elseif ($result ===0){
                return $this->returnMessage(2001,'已是最新数据',null);

            }else{
                return $this->returnMessage(2001,'响应错误',$result);
            }

        }else{
            return $this->returnMessage(2001,'响应错误','没有id为'.$validateResult['id'].'的数据');
        }



    }

    /**
     * 删除导航栏
     * @param id  $id   更新广告id
     */
    public function delNav(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new NavValidate();
        $validateResult=$validate->delNavValidate($this->get);

        /**
         * 判断验证是否有报错信息
         */
        if(@$validateResult->code==2001){
            return $validateResult;
        }
        /**
         * 判断删除数据是否存在
         */

        $navOne = $this->findNav(['id'=>$validateResult['id']],['id','name']);


        /**
         * 插入数据到数据库
         */


        if($navOne){
            $result=$this->deleteNav(['id'=>$validateResult['id']]);


            if($result){
                return $this->returnMessage(1001,'响应成功',$result);
            }else{
                return $this->returnMessage(2001,'响应错误',$result);


            }

        }else{
            return   $this->returnMessage(2001,'响应错误','没有id为'.$validateResult['id'].'的数据');
        }

    }

    /**
     * 获取导航栏列表
     * @param int $current_page 当前页
     * @param int $page_size 每页显示数量
     */
    public function getNavList(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new NavValidate();
       
        $validateResult=$validate->getNavListValidate($this->get);
        if(@$validateResult->code==2001){
            return $validateResult;
        }

        /**
         * 获取当前页数总条数
         */
        $count=$this->countNav($validateResult);
        $result="";
        if($count > 0){
            /**
             * 查询广告列表
             */
            $result=$this->selectNav($validateResult);

        }

        $data['pagination']=$this->getPagination($validateResult['page_size'],$validateResult['current_page'],$count);

       
        if($result){
            $data['list']=$result;
            return  $this->returnMessage(1001,'响应成功',$data);

        }else{
            $data['list']=[];
            return  $this->returnMessage(2001,'响应错误',$data);

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

    /**
     * 获取单条导航栏
     * @param int $id 广告数据id
     */
    public function getNavOne(){
        /**
         * get 字段参数验证是否符合条件
         */
        $validate=new NavValidate();
        $validateResult=$validate->getNavOneValidate($this->get);

        $result = $this->findNav($validateResult);
        
        if($result){
            return $this->returnMessage(1001,'响应成功',$result);

        }else{
            return $this->returnMessage(2001,'响应错误',false);
        }
    }

}