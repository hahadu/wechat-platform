<?php

namespace Hahadu\WechatPlatform\Shop;

use Hahadu\WechatPlatform\Platfrom;

/**
 * 微信平台功能交易组件商家入驻
 */
class Account extends Platfrom
{
    /**
     * 获取已申请成功的类类目列表
     * @return mixed
     */
    public function getCategoryList(){
        $this->setPath('get_category_list');

        return $this->post();
    }

    /**
     * 获取申请成功的品牌列表
     * @return mixed
     */
    public function getBrandList(){
        $this->setPath('get_brand_list');
        return $this->post();
    }


    /**
     * 获取微信平台商家信息
     * @return mixed
     */
    public function getInfo(){
        $this->setPath('get_info');
        return $this->post([],'data');
    }

    /**
     * @param $service_agent_path    string    必填    小程序path
     * @param $service_agent_phone    string    必填    客服联系方式
     * @param $service_agent_type    array[]    必填    客服类型，支持多个，0: 小程序客服（会校验是否开启），1: 自定义客服path 2: 联系电话
     * @param $default_receiving_address array[]    Address默认退货地址，用于售后超时的情况下，会让用户将商品退往此地址。
     * @param $default_receiving_address array[receiver_name]    string    是    收件人姓名
     * @param $default_receiving_address array[detailed_address]    string    是    详细收货地址信息
     * @param $default_receiving_address array[tel_number]    string    是    收件人手机号码
     * @param $default_receiving_address array[country]    string    否    国家
     * @param $default_receiving_address array[province]    string    否    省份
     * @param $default_receiving_address array[city]    string    否    城市
     * @param $default_receiving_address array[town]    string    否    乡镇
     * 更新微信平台商家信息
     * @throws \Throwable
     */
    public function update_info(array $default_receiving_address, $service_agent_path, $service_agent_phone, array $service_agent_type=[0] ){
        //throw_unless(isset($default_receiving_address["receiver_name"])&&isset($default_receiving_address["detailed_address"])&&isset($default_receiving_address["tel_number"]), \Exception::class, '收货地址、姓名、电话必传');

        $data = [
            "service_agent_path" => $service_agent_path,
            "service_agent_phone" => $service_agent_phone, //必填
            "service_agent_type" => $service_agent_type,
            "default_receiving_address" => $default_receiving_address,
        ];
        $this->setPath('update_info');
        $options = [
            'multipart' => [
                [
                    'name' => 'service_agent_path',
                    'contents' => $service_agent_path
                ],
                [
                    'name' => 'service_agent_phone',
                    'contents' => $service_agent_phone
                ],
                [
                    'name' => 'service_agent_type[]',
                    'contents' => $service_agent_type[0]
                ],
                [
                    'name' => 'default_receiving_address[receiver_name]',
                    'contents' => $default_receiving_address['receiver_name']
                ],
                [
                    'name' => 'default_receiving_address[detailed_address]',
                    'contents' => $default_receiving_address['detailed_address']
                ],
                [
                    'name' => 'default_receiving_address[tel_number]',
                    'contents' => $default_receiving_address['tel_number']
                ],
            ]];

        return $this->post($options,null);

    }

    private function setPath($action){
        $this->path = "shop/account/$action?access_token=";
    }



}
