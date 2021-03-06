<?php

namespace Hahadu\WechatPlatform\BuildData;

use Exception;
//class OrderDetail{}

class OrderData extends AbstractBuild
{
    /**
     * @var string    必须    创建时间，yyyy-MM-dd HH:mm:ss，与微信服务器不得超过5秒偏差
     */
    public $create_time;
    /**
     * @var string    必须    商家自定义订单ID(字符集包括大小写字母数字，长度小于128个字符）
     */
    public $out_order_id;
    /**
     * @var string    必须    用户的openid
     */
    public $openid;
    /**
     * @var []    必须    订单详细数据
     */
    public $order_detail ;
    /**
     * @var array[]    必须    配送信息 <br/>
     * @var array[delivery_type] 必须 1: 正常快递, 2: 无需快递, 3: 线下配送, 4: 用户自提，视频号场景目前只支持 1，正常快递
     */
    public $delivery_detail = ['delivery_type'=>1];
    /**
     * @var string    必须    订单详情页路径
     */
    public $path;
    /**
     * @var self::AddressInfo    必须    地址信息
     */
    public $address_info = [];
    /**
     * @var number 必须    订单类型：0，普通单，1，二级商户单
     */
    public $fund_type = 0;
    /**
     * @var    number    必须    秒级时间戳，订单超时时间，获取支付参数将使用此时间作为prepay_id 过期时间;时间到期之后，微信会流转订单超时取消（status = 181）
     */
    public $expire_time;
    /**
     * @var string    必须    会影响主播归因、分享员归因等，从下单前置检查获取
     */
    public $trace_id;

    /**
     * @param $OrderDetail array[product_infos]  必须 商品列表
     * @param $OrderDetail product_infos[out_product_id]    string    是    外部商品spuid
     * @param $OrderDetail product_infos[out_sku_id]    string    是    外部商品skuid
     * @param $OrderDetail product_infos[product_cnt]    number    是    商品个数
     * @param $OrderDetail product_infos[sale_price]    number    是    生成订单时商品的售卖价（单位：分），可以跟上传商品接口的价格不一致
     * @param $OrderDetail product_infos[sku_real_price]    number    是    sku总实付价
     * @param $OrderDetail product_infos[title]    string    是    生成订单时商品的标题
     * @param $OrderDetail product_infos[head_img]    string    是    生成订单时商品的头图
     * @param $OrderDetail product_infos[path]    string    是    绑定的小程序商品路径
     * @param $OrderDetail array[price_info]  必须    价格信息
     * @param $OrderDetail price_info[freight]    number    是    运费
     * @param $OrderDetail price_info[discounted_price]    number    否    折扣费用
     * @param $OrderDetail price_info[additional_price]    number    否    其他费用
     * @param $OrderDetail price_info[additional_remarks]    string    否    其他费用说明
     * @param $OrderDetail price_info[order_price]    number    是    订单总价
     * @param $OrderDetail array[pay_info]   fund_type = 0 必传｜
     * @param $OrderDetail pay_info[pay_method_type]  0: 微信支付, 1: 货到付款, 2: 商家会员储蓄卡（默认0）
     * @return void
     */
    public function OrderDetail($OrderDetail)
    {
        $this->order_detail = $OrderDetail;
    }

    /**
     * 必须    地址信息
     * @param $AddressInfo array[receiver_name]    string    必须    收件人姓名
     * @param $AddressInfo array[detailed_address]    string    必须    详细收货地址信息
     * @param $AddressInfo array[tel_number]    string    必须    收件人手机号码
     * @param $AddressInfo array[country]    string    否    国家
     * @param $AddressInfo array[province]    string    否    省份
     * @param $AddressInfo array[city]    string    否    城市
     * @param $AddressInfo array[town]    string    否    乡镇
     * @return void
     */
    public function AddressInfo($AddressInfo){
        $this->address_info = $AddressInfo;
    }

    /**
     * @return mixed
     * @throws \Throwable
     */
    function checkData()
    {

        throw_unless(is_string($this->create_time),Exception::class,'create_time 创建时间，yyyy-MM-dd HH:mm:ss，与微信服务器不得超过5秒偏差');
        throw_unless(strlen($this->out_order_id)<=128,Exception::class,'out_order_id 商家自定义订单ID(字符集包括大小写字母数字，长度小于128个字符）');

        throw_if(null==$this->order_detail, Exception::class,'order_detail 不能为空');
        throw_unless(is_array($this->order_detail), Exception::class,'order_detail 为数组');
        throw_unless(is_array($this->order_detail['product_infos']), Exception::class,'product_infos 为数组');
        throw_unless(is_array($this->order_detail['price_info']), Exception::class,'price_info 为数组');
        throw_unless(is_array($this->order_detail['pay_info']), Exception::class,'pay_info 为数组');
        //foreach ()
        throw_unless($this->address_info['receiver_name'],Exception::class,'收件人姓名不能为空');
        throw_unless($this->address_info['detailed_address'],Exception::class,'收件人详细地址不能为空');
        throw_unless($this->address_info['tel_number'],Exception::class,'收件人手机号码不能为空');

        throw_if(null===$this->order_detail['pay_info']['pay_method_type'],Exception::class,'pay_method_type 支付方式默认0: 微信支付, 1: 货到付款, 2: 商家会员储蓄卡（默认0）');
        throw_unless(in_array($this->order_detail['pay_info']['pay_method_type'],[0,1,2]),Exception::class,'pay_method_type 支付方式默认0: 微信支付, 1: 货到付款, 2: 商家会员储蓄卡（默认0）');
        throw_unless($this->order_detail['pay_info']['prepay_id'],Exception::class, 'prepay_id required');
        throw_unless($this->order_detail['pay_info']['prepay_time'],Exception::class, 'prepay_id prepay_time');

        throw_unless($this->order_detail['price_info']['order_price'],Exception::class,'订单总价');
        throw_if(null===$this->order_detail['price_info']['freight'],Exception::class,'运费');

        foreach ($this->order_detail['product_infos'] as $product_infos){
            throw_unless($product_infos['path'],Exception::class,'绑定的小程序商品路径');
            throw_unless($product_infos['head_img'],Exception::class,'生成订单时商品的头图');
            throw_unless($product_infos['title'],Exception::class,'生成订单时商品的标题');
            throw_unless($product_infos['sku_real_price'],Exception::class,'sku总实付价');
            throw_unless($product_infos['sale_price'],Exception::class,'生成订单时商品的售卖价（单位：分），可以跟上传商品接口的价格不一致');
            throw_unless($product_infos['product_cnt'],Exception::class,'商品个数');
            throw_unless($product_infos['out_sku_id'],Exception::class,'外部商品skuid');
            throw_unless($product_infos['out_product_id'],Exception::class,'外部商品spuid');
        }
        throw_if(null == $this->delivery_detail, Exception::class ,'配送信息不能为空');
        throw_if(null == $this->expire_time, Exception::class ,'秒级时间戳，订单超时时间，获取支付参数将使用此时间作为prepay_id 过期时间;时间到期之后，微信会流转订单超时取消（status = 181）');
    }
}
