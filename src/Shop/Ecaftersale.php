<?php

namespace Hahadu\WechatPlatform\Shop;

use Hahadu\WechatPlatform\Platfrom;

class Ecaftersale extends Platfrom
{

    /**
     * 生成售后单
     * 创建售后之前，请商家确保同步了默认退款地址
     * @param $addData array[out_order_id    string    否    商家自定义订单ID
     * @param $addData array[order_id    number    否    和out_order_id二选一
     * @param $addData array[out_aftersale_id    string    否    商家自定义售后ID
     * @param $addData array[openid    string    是    用户的openid
     * @param $addData array[type    number    是    售后类型，1:退款,2:退款退货
     * @param $addData array[product_info    object    是    售后商品
     * @param $addData product_info.out_product_id    string    否    商家自定义商品ID
     * @param $addData product_info.product_id    number    否    微信侧商品ID，和out_product_id二选一
     * @param $addData product_info.out_sku_id    string    否    商家自定义sku ID, 如果没有则不填
     * @param $addData product_info.sku_id    number    否    微信侧sku_id
     * @param $addData product_info.product_cnt    number    必填    参与售后的商品数量
     * @param $addData array[refund_reason    string    是    退款原因
     * @param $addData array[refund_reason_type    number    是    见枚举值定义 emAfterSalesReason
     * @param $addData array[orderamt    number    是    退款金额，单位分
     * @param $addData array[media_list    Object Media    否    图片or视频附件，结构体，列表
     * @return mixed
     */
    public function add($addData){
        $this->setPath('add');
        return $this->post($addData, null);

    }

    /**
     * 售后列表
     * @param $data
     * @return mixed
     */
    public function getList($data){
        $this->setPath('get_list');
        return $this->post($data,'');
    }

    public function getDetail($aftersale_id,$out_aftersale_id=null){
        $this->setPath('get');
        $data = array_filter(compact('aftersale_id','out_aftersale_id'));
        return $this->post($data,'');

    }

    /**
     * 同意退款
     * @param $aftersale_id int 微信侧售后单号
     * @param $out_aftersale_id string 外部售后单号，和aftersale_id二选一
     * @return mixed
     */
    public function acceptrefund($aftersale_id , $out_aftersale_id=null){
        $this->setPath('acceptrefund');
        $data = array_filter(compact('aftersale_id','out_aftersale_id'));
        return $this->post($data, null);
    }

    /**
     * 同意退货
     * <br/>https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/aftersale/acceptreturn.html
     * @param $acceptData array aftersale_id    number    否    微信侧售后单号
     * @param $acceptData array[out_aftersale_id    string    否    外部售后单号，和aftersale_id二选一
     * @param $acceptData array[address_info    object    是    商家收货地址
     *
     * @return mixed
     */
    public function acceptreturn($acceptData){
        $this->setPath('acceptreturn');
        return $this->post($acceptData,null);

    }

    /**
     * 拒绝售后
     * @param $aftersale_id int 微信侧售后单号
     * @param $out_aftersale_id string 外部售后单号，和aftersale_id二选一
     * @return mixed
     */
    public function reject($aftersale_id , $out_aftersale_id=null){
        $this->setPath('reject');
        $acceptData = array_filter(compact('aftersale_id','out_aftersale_id'));
        return $this->post($acceptData,null);

    }



    private function setPath($action)
    {
        $this->path = "shop/ecaftersale/$action?access_token=";
    }

}
