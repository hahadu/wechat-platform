<?php

namespace Hahadu\WechatPlatform\BuildData;
use Exception;
class DeliverySendData extends AbstractBuild
{
    /**
     * @var int 订单ID
     */
    public $order_id;

    /**
     * @var string 商家自定义订单ID 与OrderId二选一
     */
    public $out_order_id;

    /**
     * @var string required 用户openid
     */
    public $openid;

    /**
     * @var int required 发货完成标志位, 0: 未发完, 1:已发完
     */
    public $finish_all_delivery = 1;

    /**
     * @var array 快递信息，delivery_type=1时必填
     */
    public $delivery_list;

    /**
     * @var string	完成发货时间，finish_all_delivery = 1 必传
     */
    public $ship_done_time;

    /**
     * @return void
     * @throws \Throwable
     */
    public function checkData()
    {
        throw_unless($this->openid, Exception::class,'openid 不能为空');
        throw_unless($this->order_id && $this->out_order_id, Exception::class, "商家自定义订单id(out_order_id)与order_id至少选一个");
        if($this->finish_all_delivery == 1){
            throw_unless($this->delivery_list, Exception::class, '物流信息不能为空');
            throw_unless($this->delivery_list['delivery_id'], Exception::class, '快递公司ID，通过获取快递公司列表获取，将影响物流信息查询');
            throw_unless($this->delivery_list['waybill_id'], Exception::class, '快递单号');
            foreach ($this->delivery_list['product_info_list'] as $product_info){
                throw_unless($product_info['out_product_id'], Exception::class, '订单里的out_product_id');
                throw_unless($product_info['out_sku_id'], Exception::class, '订单里的out_sku_id');

            }
        }

    }

    public function DeliveryList($DeliveryList){
        $this->delivery_list = $DeliveryList;
    }

}
