<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10
 * Time: 22:35
 */

namespace app\agent\controller;

use app\admin\model\Attachment;
use app\common\controller\Agentbase;

use think\Exception;
use think\Log;
use think\Request;
use think\File;
use think\Db;
use app\admin\model\Goods as GoodsModel;

class Goods extends  Agentbase
{
   
    //商品列表
    public function goodsList()
    {
        //$model = new GoodsModel();
        //$goodslist = $model->getList();
        $where['is_delete'] = 0;
        
        $goodslist = Db::name('goods')->where($where)->paginate(20);
        foreach ($goodslist as $k => &$v)
        {   
            switch ($v['goods_state']) {
                case 0:
                    $stata_name = '未发布';
                    break;
                case 1:
                    $stata_name = '已上架';
                    break;
                case 2:
                    $stata_name = '已下架';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
            if($v['goods_class'] == 1)
            {
                $v['class_name'] = '实物产品';
            }
            else
            {
                $v['class_name'] = '虚拟产品';
            }
            if($v['is_prize'] == 1)
            {
                $v['prize'] = '奖品';
            }
            else
            {
                $v['prize'] = '商品';
            }
            $v['state_name'] = $stata_name;
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            //$v['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
            $goodslist[$k] = $v;
        }
        $this->assign('meta_title','商品列表');
        $this->assign('goodslist', $goodslist);
        return $this->view->fetch('index');
    }

    /**
     * 商品添加
     */
    public function goodsAdd()
    {  
        //判断是否表单提交数据
        if (!empty($_POST)){
            $data = [];
            // print_r($_POST['editor']);die;
            if(isset($_POST['editor']) && $_POST['editor'] != '')
            {
                $goodsinfo = str_replace('alt=""', 'style="width:100%"', $_POST['editor']);
            }
            
            $id = isset($_POST['id'])?$_POST['id']:'';
            $data['goods_name']  = isset($_POST['goodsname'])?$_POST['goodsname']:'';
            $data['goods_price'] = isset($_POST['goods_price'])?$_POST['goods_price']:'';
            $data['goods_info']  = isset($goodsinfo)?$goodsinfo:'';
            $data['check_type']  = isset($_POST['check_type'])?$_POST['check_type']:'';
            $data['goods_class']  = isset($_POST['goods_class'])?$_POST['goods_class']:'';
            $data['is_prize']  = isset($_POST['is_prize'])?$_POST['is_prize']:'';
            $data['subtitle']  = isset($_POST['subtitle'])?$_POST['subtitle']:'';
            $attach_id   = isset($_POST['attach_id'])?$_POST['attach_id']:'';
            $img_list = explode(',',$attach_id);
            $data['image_url'] = $img_list[0];
            $data['img_list'] = $attach_id;
            
            $model = new GoodsModel();
            if($id)
            {   
                $data['update_time'] = time();//date('Y-m-d H:i:s');
                $res = $model->saveGoods($data,['id'=>$id]);
            }
            else
            {   
                $data['create_time'] = time();
                $res = $model->insert($data);
            }
            
            if ($res){
                return $this->success('保存成功','Goods/goodsList');
            }

        }
        $this->assign('meta_title','商品添加');
        return $this->view->fetch('add');
    }
    //修改商品
    public function editGoods()
    {  
        
        $id = input('id');
        $data = [];
        $data['goods_state'] = input('state');
        $model = new GoodsModel();
        $info = $model->getInfo(['id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        $data['update_time'] = date('Y-m-d H:i:s');
        $res = $model->saveGoods($data,['id'=>$id]);
        if(!$res)
        {
            return $this->error('修改失败');
        }
        return $this->success('保存成功','Goods/goodsList');
    }
    
    //删除商品
    public function delGoods()
    {  
        $id = input('id');
        $data = [];
       
        $model = new GoodsModel();
        $info = $model->getInfo(['id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        $data['is_delete'] = 1;
        $data['update_time'] = date('Y-m-d H:i:s');
        $res = $model->saveGoods($data,['id'=>$id]);
        if(!$res)
        {
            return $this->error('删除失败');
        }
        return $this->success('删除成功','Goods/goodsList');
    }
    
     //修改商品页面
    public function edit()
    {  
        
        $id = input('id');
        $data = [];
       
        $model = new GoodsModel();
        $info = $model->getInfo(['id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        $this->assign('meta_title','商品编辑');
        $this->assign('goodsinfo', $info);
        return $this->view->fetch();
    }
    
    
     //修改商品页面
    public function edit1()
    {  
        
        $id = input('id');
        $data = [];
       
        $model = new GoodsModel();
        $info = $model->getInfo(['id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        
        $this->assign('goodsinfo', $info);
        return $this->view->fetch();
    }


    /**
     * 图片上传
     * @return int
     */
    public  function fileupload()
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
        foreach($tmp_name as $k=>$v)
        {
            $type = $name_arr[$k];
            $arr=explode('.', $type);
            $extension = end($arr);
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
    
    
    /**
     * 图片上传
     * @return int
     */
    public  function fileupload1()
    {   
        
        $file = $_FILES['file'];
        //var_dump($file);
         if ($file["error"] == 0)
        {
             // 成功 
            
             // 判断传输的文件是否是图片，类型是否合适
            
             // 获取传输的文件类型
            
            $typeArr = explode("/", $file["type"]);
        
         
           // 如果是图片类型
        
            $imgType = array('jpg', 'png', 'jpeg', 'gif');
        
            if(!in_array($typeArr[1], $imgType))
            { 
                $arr = [
                'code' => '-1000',
                'msg' => '非法文件类型！'
                ];
                return json($arr);
            }
            // 图片格式是数组中的一个
        
           // 类型检查无误，保存到文件夹内
            $images_name = '';
            $img_name = date('Ymdhis');
            $img_path = $_SERVER['DOCUMENT_ROOT'] . '/public' . '/uploads/'.date('Ymd').'/';
            //判断目录是否存在
            if (!file_exists($img_path)){
                mkdir($img_path);
            }
            $path = '/public' . '/uploads/'.date('Ymd').'/'.$img_name.'.'.$typeArr[1];
            $images_name = $img_path.$img_name.'.'.$typeArr[1];
            
           // 将上传的文件写入到文件夹中
        
           // 参数1: 图片在服务器缓存的地址
        
           // 参数2: 图片的目的地址（最终保存的位置）
        
           // 最终会有一个布尔返回值
        
           $bol = move_uploaded_file($file["tmp_name"], $images_name);
        
           if($bol)
           {
               $arr = [
                'code' => 1000,
                'msg'  => '上传成功',
                'data' => $path
              ];
              return json($arr);
        
           }
           else
           {
               $arr = [
                'code' => '-1000',
                'msg' => '上传失败！'
                ];
                return json($arr);
           }
       


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