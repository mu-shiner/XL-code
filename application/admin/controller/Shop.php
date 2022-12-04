<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10
 * Time: 22:35
 */

namespace app\admin\controller;

use app\admin\model\Attachment;
use app\common\controller\Adminbase;

use think\Exception;
use think\Log;
use think\Request;
use think\File;
use think\Db;
use app\shop\model\Shop as ShopModel;
use app\shop\model\ShopCert;
use app\admin\model\Service;


class Shop extends  AdminBase
{
    /**
     * 初始化
     */
    // public function _initialize()
    // {
        
    // }
    //店铺列表
    public function shopList()
    {
        //$model = new ShopModel();
        $where['is_delete'] = 0;
        isset($_GET['service_id']) && $_GET['service_id']!=''?$where['service_id'] = $_GET['service_id']:'';
        isset($_GET['shopname']) && $_GET['shopname']!=''?$where['shop_name'] = ['like','%'.$_GET['shopname'].'%']:'';
        $shoplist = Db::name('shop')->where($where)->paginate(20,false,['query'=>request()->param()]);
        //$shoplist = $model->getList();
        $service = new Service();
        foreach ($shoplist as $k => &$v)
        {   
            switch ($v['shop_status']) {
                case 0:
                    $stata_name = '待提交';
                    break;
                case 1:
                    $stata_name = '已通过';
                    break;
                case 2:
                    $stata_name = '待审核';
                    break;
                case 2:
                    $stata_name = '审核失败';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
            
            switch ($v['check_type']){
                case '1':
                    $type_name = '休闲娱乐';
                    break;
                case '2':
                    $type_name = '餐饮行业';
                    break;
                case '3':
                    $type_name = '美容美发';
                    break;
                case '4':
                    $type_name = '旅游行业';
                    break;
                default:
                    // code...
                    break;
            }
            
            switch ($v['take_down']) {
                case 0:
                    $take_name = '上架';
                    break;
                case 1:
                    $take_name = '下架';
                    break;
               
                default:
                    $take_name = '';
                    break;
            }
            if($v['service_id'] == 0)
            {
                $v['service_name'] = '';
            }
            else
            {
                $serinfo = $service->getInfo(['service_id'=>$v['service_id']],'service_name');
                $v['service_name'] = $serinfo['service_name'];
            }
            
            $v['state_name'] = $stata_name;
            $v['type_name'] = $type_name;
            $v['take_name'] = $take_name;
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $shoplist[$k] = $v;
            //$v['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
        }
        $ser = new Service();
        $serlist = $ser->getList();
        $this->assign('meta_title','店铺列表');
        $this->assign('shoplist', $shoplist);
        $this->assign('serlist', $serlist);
        return $this->view->fetch('index');
    }

    /**
     * 店铺添加
     */
    public function shopAdd()
    {   
        //var_dump($_POST);die;
        $data = [];
        $id = isset($_POST['shop_id'])?$_POST['shop_id']:'';
        if(!$id)
        {
            return $this->error('shopid错误');
        }
        //print(htmlspecialchars($_POST['goods_content']));die;
        if(isset($_POST['goods_content']) && $_POST['goods_content'] != '')
        {
            $goods_content = str_replace('=""', '', $_POST['goods_content']);
        }
        //print(htmlspecialchars($goods_content));die;
        //print($goods_content);
        isset($_POST['province_name'])?$data['province_name']  = $_POST['province_name']:'';
        isset($_POST['city_name'])?$data['city_name'] = $_POST['city_name']:'';
        isset($_POST['district_name'])?$data['district_name']  = $_POST['district_name']:'';
        isset($_POST['address'])?$data['address']  = $_POST['address']:'';
        isset($goods_content)?$data['goods_content']  = $goods_content:'';
        isset($_POST['shop_status'])?$data['shop_status']  = $_POST['shop_status']:'';
        isset($_POST['fasle_info'])?$data['fasle_info']  = $_POST['fasle_info']:'';
        isset($_POST['longitude'])?$data['longitude']  = $_POST['longitude']:'';
        isset($_POST['latitude'])?$data['latitude']  = $_POST['latitude']:'';
        isset($_POST['settlement_price'])?$data['settlement_price']  = $_POST['settlement_price']:'';
        isset($_POST['service_id'])?$data['service_id']  = $_POST['service_id']:'0';
        
        $model = new ShopModel();
        if($data['shop_status'])
        {
            $data['audit_time'] = time();
        }
        //var_dump($data);die;
        $res = $model->saveShop($data,['shop_id'=>$id]);
       
        if ($res){
            return $this->success('操作成功！','Shop/ShopList');
        }else{
            return $this->success('操作失败！','Shop/ShopList');
        }
    }
    //修改店铺
    public function editShop()
    {  
        $id = input('id');
        $data = [];
        input('take_down') !== null?$data['take_down'] = input('take_down'):'';
        input('is_top') !== null?$data['is_top'] = input('is_top'):'';
        
        $model = new ShopModel();
        $info = $model->getInfo(['shop_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        if($data['is_top'] == 1 && !$info['longitude'])
        {
            return $this->error('未完成定位，不能置顶');
        }
        $res = $model->saveShop($data,['shop_id'=>$id]);
        if(!$res)
        {
            return $this->error('操作失败');
        }
        return $this->success('操作成功','Shop/ShopList');
    }
    
    //删除店铺
    public function delShop()
    {  
        $id = input('id');
        $data = [];
       
        $model = new ShopModel();
        $info = $model->getInfo(['shop_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        $data['is_delete'] = 1;
        $res = $model->saveShop($data,['shop_id'=>$id]);
        if(!$res)
        {
            return $this->error('删除失败');
        }
        return $this->success('删除成功','Shop/ShopList');
    }
    
   
    
     //修改店铺页面
    public function edit()
    {  
        
        $id = input('id');
        $data = [];
       
        $model = new ShopModel();
        $shopinfo = $model->getInfo(['shop_id'=>$id]);
        if(!$shopinfo)
        {
            return $this->error('id错误');
        }
        
        switch ($shopinfo['check_type']){
                case '1':
                    $shopinfo['check_name'] = '休闲娱乐';
                    break;
                case '2':
                    $shopinfo['check_name'] = '餐饮行业';
                    break;
                case '3':
                    $shopinfo['check_name'] = '美容美发';
                    break;
                case '4':
                    $shopinfo['check_name'] = '旅游行业';
                    break;
                default:
                    // code...
                    break;
            }
        
        $cert = new ShopCert();
        $info = $cert->getInfo(['cert_id'=>$shopinfo['cert_id']]);
        $ser = new Service();
        $serlist = $ser->getList();
        $this->assign('meta_title','店铺编辑');
        $this->assign('info', $info);
        $this->assign('shopinfo', $shopinfo);
        $this->assign('serlist', $serlist);
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
        $img_path = ROOT_PATH . '/upload' . '/Shop/'.date('Ymd').'/';
        //判断目录是否存在
        if (!file_exists($img_path)){
            mkdir($img_path);
        }
        $path = '/upload' . '/Shop/'.date('Ymd').'/';

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

    
     //店铺提现列表
    public function shopWithdrawList()
    {   
        isset($_GET['withdraw_type'])&&$_GET['withdraw_type']!=''?$where['withdraw_type'] = $_GET['withdraw_type']:'';
        $shop = new ShopModel();
        if(isset($_GET['shopname']))
        {
            $info = $shop->getInfo(['shop_name'=>$_GET['shopname']],'shop_id');
            if($info)
            {
                $where['shop_id'] = $info['shop_id'];
            }
            else
            {
                $where['shop_id'] = 0;
            }
        }
      
        $where['is_delete'] = 0;
        //var_dump($where);die;
        $list = Db::name('shop_withdraw')->where($where)->order('create_time DESC')->paginate(20,false,['query'=>request()->param()]);
        
        foreach ($list as $k => &$v)
        {   
            switch ($v['status']) {
                case -1:
                    $stata_name = '未通过';
                    break;
                case 0:
                    $stata_name = '审核中';
                    break;
                case 1:
                    $stata_name = '待财务审核';
                    break;
                case 2:
                    $stata_name = '审核通过';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
          
            switch ($v['withdraw_type'])
            {
                case 'bank':
                    $entryType = '银行卡';
                    $accountNo = $v['bank_code'];
                    $real_name = $v['bank_name'];
                break;
                case 'alipay':
                    $entryType = '支付宝';
                    $accountNo = $v['alipay_code'];
                    $real_name = $v['alipay_name'];
                    break;
                case 'wx':
                    $entryType = '微信';
                    $accountNo = $v['wechat'];
                    break;
                default:
                    
                    break;
            }
            $v['real_name'] = $real_name;
            $shopinfo = $shop->getInfo(['shop_id'=>$v['shop_id']],'shop_name');
            $v['shop_name'] = $shopinfo['shop_name'];
            $v['status_name'] = $stata_name;
            $v['entryType'] = $entryType;
            $v['accountNo'] = $accountNo;
            $v['create_time']  = date('Y-m-d H:i:s',$v['create_time']);
            $v['fail_time']  = $v['fail_time']?date('Y-m-d H:i:s',$v['fail_time']):'';
            $list[$k] = $v;
        }
        $this->assign('meta_title','提现列表');
        $this->assign('list', $list);
        return $this->view->fetch('withdrawlist');
    }
}