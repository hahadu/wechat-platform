<?php

namespace Hahadu\WechatPlatform;
use App\Models\AppsConfig;
use EasyWeChat\MiniProgram\Application;
use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Arr;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Illuminate\Support\Facades\Redis;

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
    protected $appConfig = [];

    const PLAT_TYPE_OPEN = 'open_plat';
    const PLAT_TYPE_APP = 'miniprogram_app';
    //protected $redis;

    public function __construct($appConfig=[],$type = self::PLAT_TYPE_OPEN)
    {
        $this->guzzle    = new Client();
        $this->appConfig = $appConfig;
        switch ($type){
            case self::PLAT_TYPE_OPEN:
                $this->setOpenplatAccessToken();
                break;
            default:
                $this->setAccessToken();
                break;
        }
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
        $miniProgramApp = app('miniProgram',$this->appConfig);
        $token = $miniProgramApp->access_token->getToken($refresh);
        return $this->access_token = 'access_token='.$token['access_token'];
    }

    private function setOpenplatAccessToken(bool $refresh = false){
        $authorizer_refresh_token = Redis::get($this->appConfig['authorizer_refresh_token_key']);
        if(null==$authorizer_refresh_token){
            $authorizer_refresh_token = AppsConfig::where('type',AppsConfig::WECHAT_MINI_PROGRAM)->where('biz_appid',$this->appConfig['app_id'])->select(['biz_appid','refresh_token'])->first()->refresh_token;
            Redis::set($this->appConfig['authorizer_refresh_token_key'],$authorizer_refresh_token);
        }

        /** @var \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application $miniProgramApp */
        /** @var \EasyWeChat\OpenPlatform\Application $openApp */
        $openApp = app('wechatOpenApp');
        $miniProgramApp = $openApp->miniProgram($this->appConfig['app_id'],$authorizer_refresh_token);
        $token = $miniProgramApp->access_token->getToken($refresh);
        return $this->access_token = 'access_token='.$token['authorizer_access_token'];

    }
    protected function requestUrl(){
        return $this->requestHost.$this->path.$this->access_token;
    }

    protected function setRequestHead($head){
        $this->heads = $head;
    }
    protected function post(array $data=[], $getKey="data",$postType=''){
        if(empty($data)){
            $data = new \stdClass();
        }
        $fromFata = ["body"=>json_encode($data, JSON_UNESCAPED_UNICODE)];

        if(null!=$this->heads){
            $fromFata['headers'] = $this->heads;
        }
        $request = new GuzzleRequest('POST',$this->requestUrl());
        if($postType=='formdata'){
            $post = $this->guzzle->sendAsync($request,$data)->wait();
        }else{
            $post = $this->guzzle->post($this->requestUrl(), $fromFata);

        }

        throw_if($post->getStatusCode()!=200, \Exception::class, $post->getStatusCode()." response  error code");
        $content = json_decode($post->getBody()->getContents(), true);
        if(Arr::get($content,'errcode')==40001){
            //如果提示权限失败，重新获取token后请求
            $this->setAccessToken(true);
            return $this->post($data,$getKey);
        }
        if(null!=$getKey){
            throw_if(!isset($content[$getKey]), Exception::class, "wechat api response Error ：".( Arr::get($content,'errmsg',null)), $content['errcode']??0);
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
            throw_if(!isset($content[$preKey]), Exception::class, "wechat api response Error ：" . ($content['errmsg']??null), $content['errcode']??null);
            return $content[$preKey];
        }else{
            return $content;
        }
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
