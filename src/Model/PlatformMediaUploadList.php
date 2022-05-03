<?php

namespace Hahadu\WechatPlatform\Model;


use App\Models\Model;


class PlatformMediaUploadList  extends Model
{
    protected $table = 'platform_media_upload_list';

    protected $casts = [
        'mediaData' => 'json'
    ];





}
