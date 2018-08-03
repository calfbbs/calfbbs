<?php
/**
 * @author rock
 * Date: 2018/1/25 下午10:09
 */

namespace App\model;

use App\model\ApiModel;
class NavModel extends  ApiModel
{
    public function getNavList(){

        $where['page_size']=10;
        $where['current_page']=1;
        $data=$this->get(url("api/Nav/getNavList"),$where);
        if($data->code==1001 && $data->data->list){
            return  (array)$data->data->list;
        }
        return [];
    }
}