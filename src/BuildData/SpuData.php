<?php

namespace Hahadu\WechatPlatform\BuildData;

use Exception;
use function throw_if;
use function throw_unless;

/**
 * 商品数据 <br>
 * document : https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/SPU/add_spu.html
 */
class SpuData extends AbstractBuild
{

    const DEFAULT_BRAND_ID = 2100000000;
    /**
     * @var int 微信端商品id 一般和 $out_product_id 二选一
     */
    public $product_id ;
    /**
     * @var string 是    商家自定义商品ID 一般和 $product_id 二选一
     */
    public $out_product_id;
    /**
     * @var string 是    标题，字符类型，最少不低于3，最长不超过60。商品标题不得仅为数字、字母、字符或上述三种的组合
     */
    public $title;
    /**
     * @var string 是    绑定的小程序商品路径
     */
    public $path;
    /**
     * @var string 否    商品立即购买链接
     */
    public $direct_path;
    /**
     * @var string|array $head_img 是    主图，多张，列表，图片类型，最多不超过9张
     */
    public $head_img;
    /**
     * @var string|array $qualification_pics 否    商品资质图片，图片类型，最多不超过5张
     */
    public $qualification_pics;

    /**
     * @var array[desc] string     否    商品详情图文，字符类型，最长不超过2000 <br/>
     * @var array[imgs] string|array      否    商品详情图片，图片类型，最多不超过50张 <br/>
     * [
     * "desc" => "xxxxx",
     * <br/>"imgs" =>
     * [
     * "https://mmecimage.cn/p/wx77e672d6d34a4bed/HNTiaPWTllJ5R2pq9Jv9jRD5bZOWmq2svUUzJcZbcg"
     * ]
     * ]
     */
    public $desc_info = [];
    /**
     * @var int int $third_cat_id 是    第三级类目ID
     */
    public $third_cat_id;
    /**
     * @var int int $brand_id 是    品牌id 无品牌使用默认值：2100000000
     */
    public $brand_id = self::DEFAULT_BRAND_ID;
    /**
     * @var string string $info_version 否    预留字段，用于版本控制
     */
    public $info_version = "V0.0.1";

    /**
     * @var array   是    sku数组 <br/>
     * @var array[key][out_product_id] 是    商家自定义商品ID <br/>
     * @var array[key][out_sku_id] 是    商家自定义skuID<br/>
     * @var array[key][thumb_img] 是    sku小图<br/>
     * @var array[key][sale_price] 是    售卖价格，以分为单位，数字类型，最大不超过10000000（1000万元）<br/>
     * @var array[key][market_price] 是    市场价格，以分为单位，数字类型，最大不超过10000000（1000万元）<br/>
     * @var array[key][stock_num] 是    库存，数字类型，最大不超过10000000（1000万）<br/>
     * @var array[key][barcode]        否    条形码 <br/>
     * @var array[key][sku_code]        否    商品编码，字符类型，最长不超过20 <br/>
     * @var array[key][attr_key]        是    销售属性key（自定义），字符类型，最长不超过40 <br/>
     * @var array[key][attr_value]        是    销售属性value（自定义），字符类型，最长不超过40，相同key下不能超过100个不同value <br/>
     */
    public $skus = [] ;
    /**
     * @var int[] array  是    商品使用场景,1:(default)视频号，3:订单中心
     */
    public $scene_group_list = [1];

    /**
     * 转数组
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this;
    }

    /**
     * 过滤为空的值
     * @return array
     */
    public function filterArray(): array
    {
        return array_filter($this->toArray());
    }


    public function toJson(){
        return json_encode($this);
    }

    public function filterJson(){
        return json_encode($this->filterArray());
    }

    /**
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->toJson();
    }
    public function __serialize(): array
    {
        return $this->toArray();
    }

    /**
     * 验证数据是否合法
     *
     * @return void
     * @throws \Throwable
     */
    public function checkData(){
        //Collection::make()->toArray()
        throw_unless($this->out_product_id, Exception::class, "out_product_id 不能为空");
        throw_unless($this->title, Exception::class, "title 不能为空");
        throw_if(mb_strlen($this->title)<3 || mb_strlen($this->title) >=30, Exception::class, '最少不低于3，最长不超过60。商品标题不得仅为数字、字母、字符或上述三种的组合');
        throw_unless($this->path, Exception::class, "path 不能为空");
        throw_unless($this->head_img, Exception::class, "head_img 不能为空");
        throw_if(count($this->head_img)>=9, Exception::class, "head_img 主图最多不超过9张");
        throw_unless($this->third_cat_id!==null, Exception::class, "third_cat_id 不能为空");
        if(null!==$this->qualification_pics){
            throw_if(count($this->qualification_pics)>5, Exception::class,'qualification_pics 商品资质图片最多不超过5张');
        }
        throw_unless($this->desc_info, Exception::class, "商品详情图文 desc_info 不能为空");
        throw_if(mb_strlen($this->desc_info['desc']) > 2000, Exception::class, '商品详情图文 desc_info，字符类型，最长不超过2000');
        throw_unless($this->brand_id!==null, Exception::class, "brand_id 不能为空");
        throw_unless($this->info_version, Exception::class, "info_version 不能为空");
        throw_unless($this->skus, Exception::class, "skus 不能为空");
        foreach ($this->skus as $sku){
            throw_unless($sku['out_product_id'], Exception::class, "skus[].out_product_id 不能为空");
            throw_unless($sku['out_sku_id'], Exception::class, "skus[].out_sku_id 不能为空");
            throw_unless($sku['thumb_img'], Exception::class, "skus[].thumb_img 不能为空");
            throw_unless($sku['sale_price'], Exception::class, "skus[].sale_price 不能为空");
            throw_unless($sku['market_price'], Exception::class, "skus[].market_price 不能为空");
            throw_unless($sku['stock_num'], Exception::class, "skus[].stock_num 不能为空");
            throw_unless($sku['sku_attrs'], Exception::class, "skus[].sku_attrs 不能为空");
            foreach ($sku['sku_attrs'] as $attr){
                throw_unless($attr['attr_key'], Exception::class, "skus[].sku_attrs[].attr_key 不能为空");
                throw_unless($attr['attr_value'], Exception::class, "skus[].sku_attrs[].attr_value 不能为空");
            }
        }
        throw_unless($this->scene_group_list, Exception::class, "scene_group_list 不能为空");

    }


    public function __toString()
    {
        return $this->toJson($this);
    }


}
