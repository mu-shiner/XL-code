<?php
namespace app\shop\controller;
use app\common\controller\Apibase;

class Upload extends Apibase
{
    public function index()
    {   
        
        $file = $_FILES["file"];
        
        $this->write_log($file);
        // 先判断有没有错
        
        if ($file["error"] == 0)
        {
            if($file['size'] > 10000000)//限制10M
            {
                return $this->error_req(100, "文件大小超出限制");
            }
             // 成功 
            
             // 判断传输的文件是否是图片，类型是否合适
            
             // 获取传输的文件类型
            
            $typeArr = explode("/", $file["type"]);
        
         
           // 如果是图片类型
        
            $imgType = array('jpg', 'png', 'jpeg');
        
            if(!in_array($typeArr[1], $imgType))
            { 
               return $this->error_req(100, "非法文件类型");
            }
            // 图片格式是数组中的一个
        
           // 类型检查无误，保存到文件夹内
        
           // 给图片定一个新名字 (使用时间戳，防止重复)
           $urlname = "/upload/shop/".time().".".$typeArr[1];
           $imgname = $_SERVER['DOCUMENT_ROOT'].$urlname;
        
           // 将上传的文件写入到文件夹中
        
           // 参数1: 图片在服务器缓存的地址
        
           // 参数2: 图片的目的地址（最终保存的位置）
        
           // 最终会有一个布尔返回值
        
           $bol = move_uploaded_file($file["tmp_name"], $imgname);
        
           if($bol)
           {
                return $this->success_req($urlname);
        
           }
           else
           {
                return $this->error_req(100, "上传失败");
           };
        
        } else {
        
            // 失败
            return $this->error_req(100, $file["error"]);
         
        
        };
    }
}