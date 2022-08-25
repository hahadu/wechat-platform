<?php

namespace Hahadu\WechatPlatform\Shop;

use Hahadu\WechatPlatform\Platfrom;

class Audit extends Platfrom
{

    /**
     * 上传类目资质
     * 返回申请id
     * @param array $data
     * @return mixed
     */
    public function auditCategory(array $data){
        $this->setPath('audit_category');

        return $this->post($data,'');
    }

    /**
     * 上传品牌信息，返回品牌id
     * @param array $data
     * @return mixed
     */
    public function auditBrand(array $data){
        $this->setPath('audit_brand');

        return $this->post($data,'');

    }


    /**
     * 获取曾经提交的小程序审核资质
     * 请求类目会返回多次的请求记录，请求品牌只会返回最后一次的提交记录
     * 图片经过转链，请使用高版本 chrome 浏览器打开
     * 如果曾经没有提交，没有储存历史文件，或是获取失败，接口会返回1050006
     * 注：该接口返回的是曾经在小程序方提交过的审核，非组件的入驻审核！
     * @param $reqType
     * @return mixed
     */
    public function getMiniappCertificate($reqType){
        $this->setPath('audit_brand');

        $data = ['req_type'=>$reqType];
        return $this->post($data,'');
    }

    /**
     * 根据审核id，查询品牌和类目的审核结果。
     * @param $audit_id
     * @return mixed
     */
    public function getAuditResult($audit_id){
        $this->setPath('result');
        return $this->post(['audit_id'=>$audit_id],'data');
    }

    private function setPath($action){
        $this->path = "shop/audit/$action?access_token=";
    }

}
