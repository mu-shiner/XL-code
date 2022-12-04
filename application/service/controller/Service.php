<?php

namespace app\service\controller;

use app\common\controller\Apibase;
use think\Cache;
use app\admin\model\Service as ServiceModel;
use app\shop\model\Shop;

class Service extends Apibase
{   
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $token = $this->checkShopToken(1);
        if(!$this->service_id)
        {
            return $this->error_req(100, '用户未登录');exit;
        }
    }
   /**
     * 服务中心信息
     * @return mixed
     */
    public function serviceInfo()
    {   
        $service = new ServiceModel();
        $info = $service->getInfo(['service_id'=>$this->service_id]);
        //var_dump($info);die;
        if(!$info['invitation_qr_code'])
        {
            $info['invitation_qr_code'] = $this->scerweima($this->service_id,$info['invitation_code']);
            $service->saveService(['invitation_qr_code'=>$info['invitation_qr_code']],['service_id'=>$this->service_id]);
        }
        return $this->success_req($info);
    }
    
    
    public function getShopList()
    {   
        $shop = new Shop();
        $where['service_id'] = $this->service_id; 
        isset($this->params['shop_name'])?$where['shop_name'] = $this->params['shop_name']:'';
        $this->write_log($where);
        $list = $shop->getList($where,'shop_name,logo,province_name,city_name,district_name,address,all_account,telephone,mobile');
        return $this->success_req($list);
    }
    
    //生成带logo的二维码
    function scerweima($service_id,$invitation_code)
    {   
        vendor('phpqrcode');
        
        $pathname = "/upload/qrcode/service/ser_".$service_id.'.png';
        $path = $_SERVER['DOCUMENT_ROOT'].$pathname;
        $dir_name=dirname($path);
        //目录不存在就创建
          if(!file_exists($dir_name))
          {
            $res = mkdir($dir_name,0755,true);
          }
          
        $url = 'http://tc.om/mshop/pages/apply/register?inviteCode='.$invitation_code;//邀请码内容
                    
        $errorCorrectionLevel = 'H';    //容错级别  
        $matrixPointSize = 6;           //生成图片大小  
        
        \QRcode::png($url,$path , $errorCorrectionLevel, $matrixPointSize, 2);
        
        $logo = 'public/img/logo.png';     //准备好的logo图片   
        $QR = $path;            //已经生成的原始二维码图  
     
        if (file_exists($logo)) {   
            $QR = imagecreatefromstring(file_get_contents($QR));           //目标图象连接资源。
            $logo = imagecreatefromstring(file_get_contents($logo));       //源图象连接资源。
            
            $QR_width = imagesx($QR);            //二维码图片宽度   
            $QR_height = imagesy($QR);            //二维码图片高度   
            $logo_width = imagesx($logo);        //logo图片宽度   
            $logo_height = imagesy($logo);        //logo图片高度   
            $logo_qr_width = $QR_width / 4;       //组合之后logo的宽度(占二维码的1/5)
            $scale = $logo_width/$logo_qr_width;       //logo的宽度缩放比(本身宽度/组合后的宽度)
            $logo_qr_height = $logo_height/$scale;  //组合之后logo的高度
            $from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点
            
            //重新组合图片并调整大小
            /*
             *    imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
             */
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height); 
            imagepng($QR, $path); 
            return $pathname;
        } 
    }
    
    
    
}
