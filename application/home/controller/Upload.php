<?php
namespace app\home\controller;
use app\common\controller\Homebase;
use think\Request;
use think\Db;
/**
 * Login Controller
 */
class Upload extends Homebase{
    
     /**
     * 图片上传
     * @return int
     */
    public function fileupload()
    {
        $tmp_name = isset($_FILES['image_data']['tmp_name'])?$_FILES['image_data']['tmp_name']:'';
        $name_arr = isset($_FILES['image_data']['name'])?$_FILES['image_data']['name']:'';
      
        $images_name = '';
        $img_name = date('Ymdhis');
        $img_path = ROOT_PATH . '/upload' . '/goods/'.date('Ymd').'/';
        //判断目录是否存在
        if (!file_exists($img_path)){
            mkdir($img_path);
        }
        $path = '/upload' . '/goods/'.date('Ymd').'/';

        $type_arr  = $path_arr = [];
        $attach_id_str = '';
        $i=0;
        //$imgType = array('jpg', 'png', 'jpeg');
            
        foreach($tmp_name as $k=>$v)
        {
            $type = $name_arr[$k];
            $arr=explode('.', $type);
            $extension = end($arr);
            // if(!in_array($extension, $imgType))
            // { 
            //     $arr = [
            //     'code' => '-1000',
            //     'msg' => '非法文件类型！'
            //     ];
            //     return json($arr);
            // }
            move_uploaded_file($v,$img_path.$img_name.$k.'.'.$extension);
            $images_name  .=  $img_name.$k.'.jpg'.',';
//            $file_name[] = $img_name.$k; //文件名
            $type_arr[$i] = $extension; //文件扩展名
            $path_arr[$i] = $path.$img_name.$k.'.'.$extension; // 文件上传路径
            $i++;
        }

        if (empty($type_arr) || empty($path_arr)){
            $arr = [
                'code' => '-1000',
                'msg' => '数据为空！'
            ];
            return json($arr);
        }
        $data = [];
        foreach ($path_arr as $key => $val){
                // $data['type'] = ($type_arr[$key] == 'png' || $type_arr == 'jpg' || $type_arr == 'jpeg' || $type_arr == 'gif')?1:0;
                // $data['image_url'] = $path_arr[$key];
                // $data['extension'] = $type_arr[$key];
                // $data['create_time'] = date('Y-m-d H:i:s');
                // $res = Attachment::insertGetId($data);
                $attach_id_str .=   $path_arr[$key].',';
            
        }
        if (!empty($attach_id_str)){
            $arr = [
                'code' => 1000,
                'msg'  => '上传成功',
                'data' => !empty($attach_id_str)?rtrim($attach_id_str, ","):$attach_id_str
            ];
            return json($arr);
        }

    }
    
    
     public function imgUpload(){

        if(request()->isPost()){
        
            //先判断有无图片上传
            
            $file=request()->file('imgFile');
            
            //移动到框架根目录/public/uploads/目录下
            
            if($file){
            
                $info=$file->move(ROOT_PATH.'public' . DS . 'uploads');
                
                //var_dump($info);
                
                if($info){
                
                    $data=array(
                    
                    'url'=>'http://tc.om/public/uploads/'.$info->getSaveName(),
                    
                    'error'=>0
                    
                    );
                    
                    exit(json_encode($data));
                
                }
                else
                {
                    
                    $error['error']=1;
                    
                    $error['message']=$file->getError();
                    
                    exit(json_encode($error));
                
                }
            
            }
        
        }
    
    }

}

