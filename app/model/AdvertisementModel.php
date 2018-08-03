<?php
/**
 * @className：广告控制器
 * @description：获取广告列表
 * @author:calfbb技术团队
 * Date: 2017/10/13
 */

namespace App\model;
use App\model\ApiModel;
class AdvertisementModel extends  ApiModel
{
    /**
     * 获取广告列表
     */
    public function getAdvertisementList($cid=1,$page_size=20,$current_page=1)
    {
        global $_G;
        $where['cid']=$cid;
        $where['page_size']=$page_size;
        $where['current_page']=$current_page;
        $data=$this->get(url("api/advertisement/getAdvertisementList"),$where);
        if($data->code==1001 && $data->data->list){
            return  (array)$data->data->list;
        }
        return [];
    }
}