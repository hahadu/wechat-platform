<?php

namespace Hahadu\WechatPlatform\Shop;
use Hahadu\WechatPlatform\Platfrom;
/**
 * 自定义组件开通申请流程
 *
 */
class Register extends Platfrom
{

    /**
     * 接入申请
     *
     * 通过此接口开通自定义版交易组件，将发送法务协议确认到管理员微信，请服务商以组件开通回调/轮询开通状态作为开通判断。<br/>
     * 如果账户已接入标准版组件，则无法开通，请到微信公众平台取消标准组件的开通。<br/>
     * @return mixed
     */
    public function apply()
    {
        $this->setPath('apply');
        return $this->post([]);

    }

    /**
     * 获取接入状态
     * 如果账户未接入，将返回错误码1040003。
     * @return mixed
     */
    public function check(){
        $this->setPath('check');
        return $this->post();
    }

    /**
     * 完成接入任务
     *
     * @param int $access_info_item  6:完成 spu 接口，<br/>7:完成订单接口 / <br/>19:完成二级商户号订单，<br/>8:完成物流接口，<br/>9:完成售后接口 / <br/>20:完成二级商户号售后，<br/>10:测试完成，<br/>11:发版完成
     * @return mixed
     */
    public function finishAccessInfo(int $access_info_item){
        if(!in_array($access_info_item,[6,7,8,9,10,11,19,20])){
            throw new \Exception('非法参数值');
        }
        $this->setPath('finish_access_info');
        return $this->post(['access_info_item'=>$access_info_item]);
    }

    /**
     * 场景接入申请
     * @param int $scene_group_id  1:视频号
     * @return mixed
     */
    public function applyScene(int $scene_group_id){
        $this->setPath('apply_scene');
        return $this->post(['scene_group_id'=>$scene_group_id]);
    }

    /**
     *
     * @param $action string 如请求shop/cat/get 则 action='cat/get'
     * @return void
     */
    private function setPath($action){
        $this->path = "shop/register/$action?access_token=";
    }


}
