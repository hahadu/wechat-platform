<?php

namespace Hahadu\WechatPlatform\Shop;

use Hahadu\Collect\Collection;
use Hahadu\WechatPlatform\BuildData\DeliverySendData;
use Hahadu\WechatPlatform\Platfrom;

/**
 * 物流接口
 */
class Delivery extends Platfrom
{

    static public function init(): Platfrom
    {
        return parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * 订单确认收货
     * ```把订单状态从30（待收货）流转到100（完成）```
     * @param $recieveData array[order_id    number    否    订单ID
     * @param $recieveData array[out_order_id    string    否    商家自定义订单ID，与 order_id 二选一
     * @param $recieveData array[openid    string    是    用户的openid
     * @return mixed
     */
    public function recieve($recieveData){
        $this->setPath('send');
        return $this->post($recieveData, null);
    }

    /**
     * 订单发货
     * @param $sendData array[order_id]    number    否    订单ID
     * @param $sendData array[out_order_id]    string    否    商家自定义订单ID，与 order_id 二选一
     * @param $sendData array[openid]    string    是    用户的openid
     * @param $sendData array[finish_all_delivery]    number    是    发货完成标志位, 0: 未发完, 1:已发完
     * @param $sendData delivery_list    DeliveryInfo[]    否    快递信息，delivery_type=1时必填
     * @param $sendData delivery_list[].delivery_id    string    是    快递公司ID，通过获取快递公司列表获取，将影响物流信息查询
     * @param $sendData delivery_list[].waybill_id    string    是    快递单号
     * @param $sendData delivery_list[].product_info_list[]    DeliveryProduct[]    是    物流单对应的商品信息
     * @param $sendData delivery_list[].product_info_list[].out_product_id    string    是    订单里的out_product_id
     * @param $sendData delivery_list[].product_info_list[].out_sku_id    string    是    订单里的out_sku_id
     * @param $sendData array[ship_done_time]    string    否    完成发货时间，finish_all_delivery = 1 必传
     *
     * @return mixed
     */
    public function deliverySend(DeliverySendData $sendData){
        $this->setPath('send');
        $sendData->checkData();
        return $this->post($sendData->filterArray(), null);
    }

    /**
     * 获取快递公司列表
     * @return Collection
     */
    public function getCompanyList():Collection
    {
        $this->setPath('get_company_list');

        $list = $this->post([],'company_list');
        return Collection::make($list);
    }

    private function setPath($action)
    {
        $this->path = "shop/delivery/$action?access_token=";
    }

}
