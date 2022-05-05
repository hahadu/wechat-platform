<?php

namespace Hahadu\WechatPlatform\Shop;

use Hahadu\WechatPlatform\Model\PlatformMediaUploadList;
use Hahadu\WechatPlatform\Platfrom;
use Exception;
/**
 * 自定义版交易组件接入商品前必需接口
 *
 */
class Before extends Platfrom
{
    const WECHAT_IMG_UPLOAD_CACHE_KEY_PREFIX = "wechat_img_upload_cache_key_prefix:";
    /**
     * 获取商品类目
     * @return mixed
     */
    public function getCarList(){
        $this->setPath('cat/get');
        $list = $this->post([],'third_cat_list');
        return $list;
    }

    /**
     * @param string $imgUrl
     * @param int $respType
     * @param int $uploadType
     * @return \Illuminate\Contracts\Cache\Repository|mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function imgUpload(string $imgUrl, int $respType=1, int $uploadType=1){
        if(cache()->has(self::WECHAT_IMG_UPLOAD_CACHE_KEY_PREFIX.$imgUrl)){
            $mediaData = cache()->get(self::WECHAT_IMG_UPLOAD_CACHE_KEY_PREFIX.$imgUrl);
        }else{
            $data = [
                'resp_type' => (string)$respType,
                'upload_type' => (string)$uploadType,
                "img_url" => $imgUrl,
            ];
            $this->setPath('img/upload');
            $mediaData = $this->uploadFile($data,'img_info');
            cache()->set(self::WECHAT_IMG_UPLOAD_CACHE_KEY_PREFIX,$mediaData);

        }
//        $media_upload = PlatformMediaUploadList::where('file', $imgUrl)->first();
//        if(null==$media_upload){
//            $data = [
//                'resp_type' => (string)$respType,
//                'upload_type' => (string)$uploadType,
//                "img_url" => $imgUrl,
//            ];
//            $this->setPath('img/upload');
//            $mediaData = $this->uploadFile($data,'img_info');
//            $media_upload = new PlatformMediaUploadList();
//            $media_upload->file = $imgUrl;
//            $media_upload->mediaData = $mediaData;
//            $media_upload->save();
//        }else{
//
//            $mediaData = $media_upload['mediaData'];
//        }
        return $mediaData;


    }

    /**
     *
     * @param $action string 如请求shop/cat/get 则 action='cat/get'
     * @return void
     */
    private function setPath($action){
        $this->path = "shop/$action?access_token=";
    }



}
