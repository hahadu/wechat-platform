<?php

namespace Hahadu\WechatPlatform\BuildData;

abstract class AbstractBuild
{
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

    abstract function checkData();
    public function __toString()
    {
        return $this->toJson($this);
    }



}
