<?php

namespace Hahadu\WechatPlatform\Model;


use App\Models\Model;
use Hahadu\WechatPlatform\Shop\Before;


class PlatformMediaUploadList  extends Model
{
    protected $table = 'platform_media_upload_list';

    protected $casts = [
        'mediaData' => 'json'
    ];

    /**
     * 上传图片
     * @param $imgUrl
     * @return \Illuminate\Contracts\Cache\Repository|mixed
     * @throws \Exception
     */
    public function uploadWechatPlatformImage($imgUrl)
    {
        $media_upload = $this->where('file', $imgUrl)->first();
        if (null == $media_upload) {

            $mediaData = (new Before())->imgUpload($imgUrl);
            $media_upload = new self();
            $media_upload->file = $imgUrl;
            $media_upload->mediaData = $mediaData;
            $media_upload->save();
        } else {

            $mediaData = $media_upload['mediaData'];
        }

        return $mediaData;
    }


}
