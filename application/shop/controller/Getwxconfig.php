<?php
namespace app\shop\controller;

use app\common\controller\Apibase;
use think\Cache;
use app\api\model\Pay;


class GetWxConfig extends Apibase
{   
    public function getInfo()
    {   
        $data = $this->params;
        $this->write_log($data);
        $jsapi_ticket = $this->getJsapi_ticket();
        $nonceStr = "x".rand(10000,100000)."x"; //随机字符串
        $timestamp = time(); //时间戳
        $url = $data['url'];
        $signature = $this->getSignature($jsapi_ticket,$nonceStr, $timestamp, $url);
        $pay = new Pay();
        $result = array("appId"=>$pay->PhdAppId, "jsapi_ticket"=>$jsapi_ticket, "nonceStr"=>$nonceStr,"timestamp"=>$timestamp,"url"=>$url,"signature"=>$signature);
        return $this->success_req($result);
     
    }
    
 
 function getSignature($jsapi_ticket,$noncestr, $timestamp, $url){
  $string1 = "jsapi_ticket=".$jsapi_ticket."&noncestr=".$noncestr."&timestamp=".$timestamp."&url=".$url;
 
  $sha1 = sha1($string1);
  return $sha1;
 }
 
 public function getJsapi_ticket(){
      
      $redis = Cache::getHandler();
      //从缓存从读取键值 $key 的数据
      $jsapi_ticket = $redis -> get("jsapi_ticket");
      $this->write_log($jsapi_ticket);
      //如果没有缓存数据
      if ($jsapi_ticket == false) {
       $access_token = $this->getAccess_token();
       $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket'; 
       $data = array('type'=>'jsapi','access_token'=>$access_token); 
       $header = array(); 
       $response = json_decode($this->curl_https($url, $data, $header, 5),true);
       $this->write_log($response);
       $jsapi_ticket = $response['ticket'];
        //var_dump($access_token);die;
       //写入键值 $key 的数据
       $redis -> set("jsapi_ticket", $jsapi_ticket, 7000);
      }
      return $jsapi_ticket;
 }
 
 public function getAccess_token(){
      $redis = Cache::getHandler();
      //从缓存从读取键值 $key 的数据
      $access_token = $redis -> get("access_token");
      //$this->write_log($access_token);
      $pay = new Pay();
      //如果没有缓存数据
      if ($access_token == false) {
       $url = 'https://api.weixin.qq.com/cgi-bin/token'; 
       $data = array('grant_type'=>'client_credential','appid'=>$pay->PhdAppId,'secret'=>$pay->PhdAppSecret); 
       $header = array();
     
       $response = json_decode($this->curl_https($url, $data, $header, 5),true); 
       $access_token = $response['access_token'];
       //$this->write_log($response);
       
       //写入键值 $key 的数据
       $redis -> set("access_token", $access_token, 7000);
      }
      return $access_token;
 }
 
 /** curl 获取 https 请求 
 * @param String $url 请求的url 
 * @param Array $data 要發送的數據 
 * @param Array $header 请求时发送的header 
 * @param int $timeout 超时时间，默认30s 
 */
 public function curl_https($url, $data=array(), $header=array(), $timeout=30){ 
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 
      curl_setopt($ch, CURLOPT_URL, $url); 
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
      curl_setopt($ch, CURLOPT_POST, true); 
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
     
      $response = curl_exec($ch);
     
      if($error=curl_error($ch)){ 
      die($error); 
      }
     
      curl_close($ch);
     
      return $response;
 
 } 
 
//  function object2array_pre(&$object) {
//     if (is_object($object)) {
//         $arr = (array)($object);
//     } else {
//         $arr = &$object;
//     }
//     if (is_array($arr)) {
//         foreach($arr as $varName => $varValue){
//             $arr[$varName] = $this->object2array($varValue);
//         }
//     }
//     return $arr;
// }
// function object2array(&$object) {
//     $object =  json_decode( json_encode( $object),true);
//     return  $object;
// }
 
}
?>