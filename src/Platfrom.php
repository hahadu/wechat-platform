<?php

namespace Hahadu\WechatPlatform;
use EasyWeChat\MiniProgram\Application;
use GuzzleHttp\Client;
use Exception;


class Platfrom
{
    protected $requestHost="https://api.weixin.qq.com/";
    protected $access_token;
    protected $path;

    /**
     * @var Client
     */
    protected $guzzle;
    protected $heads = null;

    //protected $redis;

    public function __construct()
    {
        $this->guzzle = new Client();
        $this->setAccessToken();
    }

    /**
     * @return Platfrom
     */
    static public function init(): Platfrom
    {
        return (new self());
    }

    private function setAccessToken(bool $refresh = false){
        /** @var Application $miniProgramApp */
        $miniProgramApp = app('miniProgram');
        $token = $miniProgramApp->access_token->getToken($refresh);
        return $this->access_token = $token['access_token'];
    }

    protected function requestUrl(){
        return $this->requestHost.$this->path.$this->access_token;
    }

    protected function setRequestHead($head){
        $this->heads = $head;
    }
    protected function post(array $data=[], $getKey="data"){
        $fromFata = ["body"=>json_encode($data, JSON_UNESCAPED_UNICODE)];
        if(null!=$this->heads){
            $fromFata['headers'] = $this->heads;
        }
        $post = $this->guzzle->post($this->requestUrl(), $fromFata);

        throw_if($post->getStatusCode()!=200, \Exception::class, $post->getStatusCode()." response  error code");
        $content = json_decode($post->getBody()->getContents(), true);
        //dump($post->getHeaders());
        if(isset($content['errcode']) && $content['errcode']==40001){
            //如果提示权限失败，重新获取token后请求
            $this->setAccessToken(true);
            return $this->post($data,$getKey);
        }
        if(null!=$getKey){
            throw_if(!isset($content[$getKey]), Exception::class, "wechat api response Error ：".( $content['errmsg']??null), $content['errcode']??null);
            return $content[$getKey];
        }else{
            return $content;
        }
    }

    protected function get(array $data=null, $preKey="data"){
        $fromFata = ["body"=>json_encode($data, JSON_UNESCAPED_UNICODE)];
        if(null!=$this->heads){
            $fromFata['headers'] = $this->heads;
        }
        $get = $this->guzzle->get($this->requestUrl(), $fromFata);
        throw_if($get->getStatusCode()!=200, \Exception::class, $get->getStatusCode()." response  error code");
        $content = json_decode($get->getBody()->getContents(), true);
        if(isset($content['errcode']) && $content['errcode']==40001){
            $this->setAccessToken(true);
            return $this->get($data,$preKey);
        }
        if(null!=$preKey){
            throw_if(!isset($content[$preKey]), Exception::class, "wechat api response Error ：".( $content['errmsg']??null), $content['errcode']??null);
        }

        return $content[$preKey];
    }


    protected function uploadFile(array $data, $preKey="img_info"){
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->requestUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: multipart/form-data'
            ],
        ]);

        $response = json_decode(curl_exec($curl),true);

        curl_close($curl);
        if(isset($response['errcode']) && $response['errcode']==40001){
            $this->setAccessToken(true);
            return $this->get($data,$preKey);
        }

        if(null!=$preKey){
            throw_if(!isset($response[$preKey]), Exception::class, "wechat api response Error ：".( $response['errmsg']??null), $response['errcode']??null);
        }

        return $response[$preKey];
    }


}
