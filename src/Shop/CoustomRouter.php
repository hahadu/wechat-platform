<?php

namespace Hahadu\WechatPlatform\Shop;

use Hahadu\WechatPlatform\Platfrom;

class CoustomRouter extends Platfrom
{
    public function postCoustomRoute($path = '',$data = [],$getKey='',$formdata=''){
        $this->path = $path . '?';
        return $this->post($data,$getKey,$formdata);
    }
    public function getCoustomRoute($path = '',$data = [],$preKey=null){
        $this->path = $path . '?';
        return $this->get($data,$preKey);
    }

    public function setRequestHostUrl($requestHost = 'https://api.weixin.qq.com/')
    {
        $this->requestHost = $requestHost;
    }
}
