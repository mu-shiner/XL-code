<?php
/**
 * 支付
 */

namespace app\api\model;
use think\Model;
use think\Db;

class Pay extends Model
{   
    public $MchNo = 'M1652688790';
    //微信小程序支付配置
    public $WxAppletAppId = '6269eec2f72085f47d81c0e0';
    public $WxAppletApiKey = 'x86OyI8afJGGXXNS5Qj96yAxeBcO3PFWEyTFNXlKgqmFUxsw6nKDeOcNmp4ywhFhYO1RUp3xXaYrNeV8UwqS45KCl4KsNbdIbWSInH16nmuh9CumNXDDKIBbLqRP9qz8';
    //微信浏览器支付配置
    public $WxAppId = '628208e2f72085f47d81c0e4';
    public $WxApiKey = '9JpjoItjfkPT1UJU24vPTa6EPYuAiUxtR6a4UXgLi7rBlU7U7KTcpYjik8b1lacmKJxCT8U71vUL6221EfxN8OLtuJ2HiAKI3skmspOiuDfzSytpIyW6JHeab8T7D4Kf';
    //支付宝wap支付、转账
    public $AliMchNo = 'M1655793632';
    public $AppId = '62b167e1f72085f47d81c0ec';
    public $ApiKey = '0gZCT3YtVo7qRcaWOkheAzSfPFuRXS4emnf5F9rjfQNKT26gO78b2VJs6tyM5upK0tRBFVAnDmJrJbK5PfmDvzo7XsznZBTgHBA0ge1ubI3mEvukThCclCCanYMmvRU6';
    
    public $ApiBase = 'https://pay.bdtc100.com';//支付中心
    //回调
    public $GroupNotifyUrl = 'http://tc.om/Api/OrderNotify/GroupPayNotify';
    public $WithdrawNotifyUrl = 'http://tc.om/Api/OrderNotify/WithdrawNotify';
    public $UsersOrderNotifyUrl = 'http://tc.om/Api/OrderNotify/UsersOrderPayNotify';
    public $ShopWithdrawNotifyUrl = 'http://tc.om/Api/OrderNotify/ShopWithdrawNotify';
    public $ServiceWithdrawNotifyUrl = 'http://tc.om/Api/OrderNotify/ServiceWithdrawNotify';
    
    //提现
    public $WithdrawMchNo = 'M1654939868';
    public $WithdrawAppId = '62a460dcf72085f47d81c0e8';
    public $WithdrawApiKey = 'GohA9iiwbgNlkxtrP4IagFffE4SJF9jV9lwsqrgybO1TEfIJuS3SaKMPDs9mpMR4hOX9zf2C0ZacqZglNqZcXqBn3cVirObqx19ZAkHLpOl2bZPtjRbq24ddoUa3jXMf';
    
    public $WithdrawMchNo2 = 'M1652688790';
    public $WithdrawAppId2 = '62aaf83ef72085f47d81c0eb';
    public $WithdrawApiKey2 = '9KpJOUmSVR8WRHmZVUL8K6gWFFyvpishvIhFUY2rSa7rb1VpcKuERbyRMRljifGbIwXv4zQgkxJYEtT33mmKT5Fj0CrbQNcGgo10ATLSf5cK3KsDXWjYjVtQ7Q3jN7od';
    
    //店铺提现
    public $ShopWithdrawMchNo = 'M1654939868';
    public $ShopWithdrawAppId = '62a460dcf72085f47d81c0e8';
    public $ShopWithdrawApiKey = 'GohA9iiwbgNlkxtrP4IagFffE4SJF9jV9lwsqrgybO1TEfIJuS3SaKMPDs9mpMR4hOX9zf2C0ZacqZglNqZcXqBn3cVirObqx19ZAkHLpOl2bZPtjRbq24ddoUa3jXMf';
    
    
    //优团客微信公众号
    public $PhdAppId = 'wxbb5892c3708ff99d';
    public $PhdAppSecret = '6e0c5cfe1891b3b53cf605ca68a2d047';
    //推送消息模版id
    public $GroupSuccessTemplateId = 'CUV1S_0CiULnCQznLwkkzkaZGxWjbVrnNu78a5yJBlc';//团购成功
    public $GroupFailTemplateId = 'QVkl7TkNluQ-lPzJOH2Yy4y9KaMJoKo5ZYfpa7StUdY';//团购失败
    public $TradeSuccessTemplateId = 'Otux4dEHlqqDHGvUrLwxD2q0jia9lIKrbgzzgp0zgqU';//通用券转让成功
    public $TradeFailTemplateId = 'ryaxwiedzvGedco4lp_5tqWpUCdgAbnHz_7_IGMPjrs';//通用券转让失败
    
    //云零售小程序
    public $YtkAppid = 'wx31977fcea6b64f75';
    public $YtkAppSecret = '99002783a636d7df2b4da4cfc2a98349';
    
    public function getSigna($params,$key)
    {
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
            $str .= "$k=$v&";
        }
        $str .= 'key='.$key;
        
        return strtoupper(md5($str));
    }
    
    public function getParam($params)
    {
        $str = '';
        foreach ($params as $k => $v) {
            $str .= "$k:$v:";
        }
        
        return $str;
    
    }
    
    //post请求
    function post_curls($url, $post, $headers=null)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
      
        if (!empty($headers)) {curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);} // 设置HTTP头信息
        $res = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $res; // 返回数据，json格式
    }
    
    //公众号推送消息
    public function template($data)
    {
        //获得access_token
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->PhdAppId."&secret=".$this->PhdAppSecret;
        
        $weix = file_get_contents($url);//获得网页输出
        $obj=json_decode($weix,true );//解码
        
        $access_token= $obj['access_token'];//网页授权接口调用凭证
        //发送模板消息
        $fasuerl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        
        $params = json_encode($data);
        $res=$this->post_curls($fasuerl,$params);
        return $res;
    }


    
}