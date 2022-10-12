<?php

namespace Hahadu\WechatPlatform\Shop;

use Hahadu\WechatPlatform\Platfrom;

class CoustomRouter extends Platfrom
{
    public function postCoustomRoute($path = '',$data = []){
        $this->path = $path . '?access_token=';
        return $this->post($data,'');
    }

}
