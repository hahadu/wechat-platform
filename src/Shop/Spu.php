<?php

namespace Hahadu\WechatPlatform\Shop;

use Hahadu\WechatPlatform\BuildData\SpuData;
use Hahadu\WechatPlatform\Platfrom;

class Spu extends Platfrom
{
    //protected $path="shop/spu/add?access_token=";

    public function add(SpuData $spu_data)
    {

        //dump(strlen($spu_data->title));
        $spu_data->checkData();

        $this->setPath('add');
        //return $spu_data;

        return $this->post($spu_data->filterArray());

    }

    /**
     * @param SpuData $spu_data
     * @return mixed
     * @throws \Throwable
     */
    public function update(SpuData $spu_data)
    {

        $spu_data->checkData();

        $this->setPath('update');

        return $this->post($spu_data->filterArray());

    }

    /**
     * 微信平台product_id
     * @param int $product_id
     * @return mixed
     */
    public function del(int $product_id){
        $this->setPath('del');
        return $this->post(['product_id'=>$product_id]);

    }

    public function get_detail(int $product_id, $need_edit_spu=0){
        $this->setPath('get');
        return $this->post(compact("product_id","need_edit_spu"),'spu');
    }

    /**
     * @return mixed
     */
    public function get_list($status=null,$page=1,$pageSize = 10){

        $this->setPath('get_list');
        $data = array_filter([
            "status" => $status,            // 选填，不填时获取所有状态商品
//            "start_create_time" => "2020-12-25 00:00:00",     // 选填，与end_create_time成对
//            "end_create_time" => "2020-12-26 00:00:00",       // 选填，与start_create_time成对
//            "start_update_time" => "2020-12-25 00:00:00",     // 选填，与end_update_time成对
//            "end_update_time" => "2020-12-26 00:00:00",       // 选填，与start_update_time成对
            "page" => $page,
            "page_size" => $pageSize,        // 不超过100
//            "need_edit_spu" => 1      // 默认0:获取线上数据, 1:获取草稿数据
        ]);
        $response = $this->post($data,'');
        if($response['errcode']==0){
            unset($response['errcode']);
        }

        return $response;


    }

    private function setPath($action){
        $this->path = "shop/spu/$action?access_token=";
    }




}
