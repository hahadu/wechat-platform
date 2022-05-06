<?php

namespace Hahadu\WechatPlatform\LivePlay;

use Hahadu\WechatPlatform\Platfrom;

/**
 * 直播组件
 */
class Room extends Platfrom
{

    /**
     * 创建直播间
     * @param $name    String    是    直播间名字，最短3个汉字，最长17个汉字，1个汉字相当于2个字符
     * @param $coverImg    String    是    背景图，填入mediaID（mediaID获取后，三天内有效）；图片mediaID的获取，请参考以下文档： https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/New_temporary_materials.html；直播间背景图，图片规则：建议像素1080*1920，大小不超过2M
     * @param $startTime    Number    是    直播计划开始时间（开播时间需要在当前时间的10分钟后 并且 开始时间不能在 6 个月后）
     * @param $endTime    Number    是    直播计划结束时间（开播时间和结束时间间隔不得短于30分钟，不得超过24小时）
     * @param $anchorName    String    是    主播昵称，最短2个汉字，最长15个汉字，1个汉字相当于2个字符
     * @param $anchorWechat    String    是    主播微信号，如果未实名认证，需要先前往“小程序直播”小程序进行实名验证, 小程序二维码链接：https://res.wx.qq.com/op_res/9rSix1dhHfK4rR049JL0PHJ7TpOvkuZ3mE0z7Ou_Etvjf-w1J_jVX0rZqeStLfwh
     * @param $subAnchorWechat    String    否    主播副号微信号，如果未实名认证，需要先前往“小程序直播”小程序进行实名验证, 小程序二维码链接：https://res.wx.qq.com/op_res/9rSix1dhHfK4rR049JL0PHJ7TpOvkuZ3mE0z7Ou_Etvjf-w1J_jVX0rZqeStLfwh
     * @param $createrWechat    String    否    创建者微信号，不传入则此直播间所有成员可见。传入则此房间仅创建者、管理员、超管、直播间主播可见
     * @param $shareImg    String    是    分享图，填入mediaID（mediaID获取后，三天内有效）；图片mediaID的获取，请参考以下文档： https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/New_temporary_materials.html；直播间分享图，图片规则：建议像素800*640，大小不超过1M；
     * @param $feedsImg    String    是    购物直播频道封面图，填入mediaID（mediaID获取后，三天内有效）；图片mediaID的获取，请参考以下文档： https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/New_temporary_materials.html; 购物直播频道封面图，图片规则：建议像素800*800，大小不超过100KB；
     * @param $isFeedsPublic    Number    否    是否开启官方收录 【1: 开启，0：关闭】，默认开启收录
     * @param $type    Number    是    直播间类型 【1: 推流，0：手机直播】
     * @param $closeLike    Number    是    是否关闭点赞 【0：开启，1：关闭】（若关闭，观众端将隐藏点赞按钮，直播开始后不允许开启）
     * @param $closeGoods    Number    是    是否关闭货架 【0：开启，1：关闭】（若关闭，观众端将隐藏商品货架，直播开始后不允许开启）
     * @param $closeComment    Number    是    是否关闭评论 【0：开启，1：关闭】（若关闭，观众端将隐藏评论入口，直播开始后不允许开启）
     * @param $closeReplay    Number    否    是否关闭回放 【0：开启，1：关闭】默认关闭回放（直播开始后允许开启）
     * @param $closeShare    Number    否    是否关闭分享 【0：开启，1：关闭】默认开启分享（直播开始后不允许修改）
     * @param $closeKf    Number    否    是否关闭客服 【0：开启，1：关闭】 默认关闭客服（直播开始后允许开启）
     * @param array $data
     * @return void
     */
    public function create(array $data){
        $this->setPath('create');
        $this->post($data);
    }

    private function setPath($action){
        $this->path = "wxaapi/broadcast/room/$action?access_token=";
    }


}
