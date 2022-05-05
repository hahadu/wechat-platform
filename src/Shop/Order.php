<?php

namespace Hahadu\WechatPlatform\Shop;

use Hahadu\WechatPlatform\BuildData\OrderData;
use Hahadu\WechatPlatform\Platfrom;

/**
 * 自定义版交易组件订单接口
 *
 * https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/order/getpaymentparams.html
 */
class Order extends Platfrom
{

    /**
     * 该接口仅用于在微信侧生成一笔业务订单，若需要拉起收银台，则需要调用生成支付订单接口。
     *
     * 注：
     *
     * 调用该接口成单后，如果想要修改订单，需要调用更新订单相关接口；
     * 生成业务订单时，微信侧会对金额进行校验，请确保金额相关信息满足： sum(sku_real_price) + freight = order_price = sku.sale_price * cnt +freight-discounted_price+additional_price 否则将生成订单失败，其中sku_real_price为订单中某一类SKU的实付款（单个SKU标价SKU个数 - 单个SKU优惠价格SKU个数）。
     * 如果带入trace_id，则在解析完毕之后会发出回调(订单归因成功回调)[https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/order/checkBeforeAddOrder.html]
     *
     * @param $create_time    string    是    创建时间，yyyy-MM-dd HH:mm:ss，与微信服务器不得超过5秒偏差
     * @param $out_order_id    string    是    商家自定义订单ID(字符集包括大小写字母数字，长度小于128个字符）
     * @param $openid    string    是    用户的openid
     * @param $order_detail    OrderDetail    是    订单详细数据
     * @param $delivery_detail    DeliveryDetail    是    配送信息
     * @param $path    string    是    订单详情页路径
     * @param $address_info    AddressInfo    是    地址信息
     * @param $fund_type    number    是    订单类型：0，普通单，1，二级商户单
     * @param $expire_time    number    是    秒级时间戳，订单超时时间，获取支付参数将使用此时间作为prepay_id 过期时间;时间到期之后，微信会流转订单超时取消（status = 181）
     * @param $aftersale_duration    number    ❌    废弃，请使用更新售后期接口
     * @param $trace_id    string    是    会影响主播归因、分享员归因等，从下单前置检查获取
     *
     * @return mixed
     */
    public function add(OrderData $data)
    {

        $this->setPath('add');
        return $this->post($data->filterArray(),'');

    }

    /**
     * 获取订单列表
     * @param $orderData array[page]    number    是    第x页，大于等于1
     * @param $orderData array[page_size]    number    是    每页订单数，上限100
     * @param $orderData array[sort_order]    number    是    1:desc, 2:asc
     * @param $orderData array[start_create_time]    string    否    起始创建时间
     * @param $orderData array[end_create_time]    string    否    最终创建时间
     */
    public function getOrderList($orderData){
        $this->setPath('get_list');
        $orderData = $this->post($orderData, null);
        unset($orderData['errcode']);
        return $orderData;

    }

    /**
     * 获取订单详情
     * 可以按照支付单号或者外部订单号来查询业务单详情、支付单详情、支付单状态
     * @param $order_id
     * @param $openid
     * @return mixed
     */
    public function getOrderDetail($order_id,$openid){
        $this->setPath('get');

        return $this->post(compact($order_id,$openid),'order');
    }

    /**
     * 生成支付参数
     * 调用接口发起支付单请求，需要先生成业务订单才可以发起生成支付订单。 注： 1:一旦发起支付单，则业务订单的价格不可进行修改，若需要修改，请先关闭支付单，重新发起一笔支付订单。 2:每次需要拉起收银台时，请先调用此接口获取最新的支付参数。 3:使用本接口的订单需要在生成订单时将fund_type设为1
     * @param $data array[order_id]    number    是    微信侧订单id
     * @param $data array[out_order_id]    string    是    商家自定义订单ID
     * @param $data array[openid]    string    是    用户的openid
     * @return mixed
     */
    public function getPaymentParams($data){
        $this->setPath('getpaymentparams');
        return $this->post($data);
    }

    /**
     * 更改订单价格
     * <br/>1，改价将影响订单实付价；
     * <br/>2，只有在订单未支付的状态时才可以调用此接口；
     * <br/>3，调用此订单前，旧支付参数会失效。
     * <br/>4，必须上传所有商品的实际价格，会对订单金额重新校验
     * <br/>5，订单只能修改一次
     * @param $priceData array[out_order_id    string    是    商家自定义订单ID(字符集包括大小写字母数字，长度小于128个字符）
     * @param $priceData array[openid    string    是    用户的openid
     * @param $priceData product_info[]    OrderProductInfo[]    是    商品价格信息
     * @param $priceData product_info[].out_product_id    string    是    外部商品spuid
     * @param $priceData product_info[].out_sku_id    string    是    外部商品skuid
     * @param $priceData product_info[].sale_price    number    是    生成订单时商品的售卖价（单位：分），可以跟上传商品接口的价格不一致
     * @param $priceData product_info[].sku_real_price    number    是    sku总实付价
     * @param $priceData array[price_info]    PriceInfo    是    订单价格信息 ｜
     * @param $priceData price_info.freight    number    是    运费
     * @param $priceData price_info.discounted_price    number    否    折扣费用
     * @param $priceData price_info.additional_price    number    否    其他费用
     * @param $priceData price_info.additional_remarks    string    否    其他费用说明
     * @param $priceData price_info.order_price    number    是    订单总价
     * @return mixed
     */
    public function changePrice($priceData){
        $this->setPath('change_price');
        return $this->post($priceData);
    }

    /**
     * 更新订单地址
     * @param $priceData
     * @return mixed
     */
    public function updateAddress($priceData){
        $this->setPath('update_address');
        return $this->post($priceData);
    }

    /**
     * 支付
     * @param $payData array[order_id    number(uint64)    否    订单ID
     * @param $payData array[out_order_id    string    否    商家自定义订单ID，与 order_id 二选一
     * @param $payData array[openid    string    是    用户的openid
     * @param $payData array[action_type    number    是    类型，默认1:支付成功,2:支付失败,3:用户取消,4:超时未支付;5:商家取消;10:其他原因取消
     * @param $payData array[action_remark    string    否    其他具体原因
     * @param $payData array[transaction_id    string    否    支付订单号，action_type=1且order/add时传的pay_method_type=0时必填
     * @param $payData array[pay_time    string    否    支付完成时间，action_type=1时必填，yyyy-MM-dd HH:mm:ss
     * @return mixed
     */
    public function orderPay($payData){
        $this->setPath('pay');
        return $this->post($payData);
    }

    /**
     * 关闭订单
     * @param $orderId
     * @param $openId
     * @return mixed
     */
    public function closeOrder($orderId,$openId){
        $this->setPath('close');
        $orderData = [
            "order_id"=>$orderId,
            "openid"=>$openId,
        ];
        return $this->post($orderData);
    }

    /**
     *
     * @param $action string 如请求shop/cat/get 则 action='cat/get'
     * @return void
     */
    private function setPath($action){
        $this->path = "shop/order/$action?access_token=";
    }




}
