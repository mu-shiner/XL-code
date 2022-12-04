<?php

namespace app\api\controller;

use app\common\controller\Apibase;
use app\api\model\Login as LoginModel;
use wxsdk\example\WxPayConfig;
use app\api\model\Users;
use app\api\model\Pay;

class WxLogin extends Apibase
{   
   
    /**
     * 取公众号微信openid
     */
    public function getOpenid()
    {   
        $code = $this->params["code"];
        if(!$code)
        {   
            return $this->error_req(100, "无code");
        }
       
        $config = new Pay();
        $appid  = $config->PhdAppId;
        $secret = $config->PhdAppSecret;
        
        $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        //$this->write_log($oauth2Url);
        $oauth2 = $this->getJson($oauth2Url);
        //$this->write_log($oauth2);
        // 获得 access_token 和openid
        //$access_token = $oauth2["access_token"];
        $openid = $oauth2['openid'];
        if(!$openid)
    	{
    		return $this->error_req(100, "系统错误");
    	}
    	return $this->success_req($openid);
    }
    
    
    /**
     * 取小程序微信openid
     */
    public function getWxAppOpenid()
    {   
        $code = $this->params["code"];
        if(!$code)
        {   
            return $this->error_req(100, "无code");
        }
       
        $config = new Pay();
        $appid  = $config->YtkAppid;
        $secret = $config->YtkAppSecret;
        
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='
            . $appid . '&secret=' . $appsecret . '&js_code='
            . $code . '&grant_type=authorization_code';
        
        
        $this->write_log($oauth2Url);
        $oauth2 = $this->getJson($url);
        $this->write_log($oauth2);
        // 获得 access_token 和openid
        //$access_token = $oauth2["access_token"];
        $openid = $oauth2['openid'];
        if(!$openid)
    	{
    		return $this->error_req(100, "系统错误");
    	}
    	return $this->success_req($openid);
    }
   
    
}
