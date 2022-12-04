<?php

namespace app\shop\controller;

use app\common\controller\Apibase;
use think\Cache;
use app\shop\model\Verify as VerifyModel;
use app\shop\model\Shop as ShopModel;
use app\shop\model\ShopCert;
use app\shop\model\Admin;

class Shop extends Apibase
{   
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $token = $this->checkShopToken();
        if(!$this->shop_id)
        {
            return $this->error_req(100, '用户未登录');
        }
    }
   /**
     * 店铺信息
     * @return mixed
     */
    public function shopInfo()
    {   
        $shop = new ShopModel();
        $shopinfo = $shop->getInfo(['shop_id'=>$this->shop_id],'shop_id,shop_name,shop_status,logo,check_type,account');
        switch ($shopinfo['check_type']) {
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
        $id = $this->params['login_id'];
        $admin = new Admin();
        $admininfo = $admin->getInfo(['id'=>$id],'is_manager');
        $shopinfo['is_manager'] = $admininfo['is_manager'];
        return $this->success_req($shopinfo);
    }
    //店铺提交审核信息
    public function saveShopAudit()
    {   
        $this->write_log($this->params);
        $shop_id = $this->shop_id;
        
        $logo = isset($this->params[ 'logo' ]) ? $this->params[ 'logo' ] : '';//店铺Logo
        $province_name = isset($this->params[ 'province_name' ]) ? $this->params[ 'province_name' ] : '';//店铺所在省
        $city_name = isset($this->params[ 'city_name' ]) ? $this->params[ 'city_name' ] : '';//店铺所在市
        $district_name = isset($this->params[ 'district_name' ]) ? $this->params[ 'district_name' ] : '';//店铺所在区/县
        $address = isset($this->params[ 'address' ]) ? $this->params[ 'address' ] : '';//店铺所在地址
        $longitude = isset($this->params[ 'longitude' ]) ? $this->params[ 'longitude' ] : '';//经度
        $latitude = isset($this->params[ 'latitude' ]) ? $this->params[ 'latitude' ] : '';//纬度
        //$check_type = isset($this->params[ 'check_type' ]) ? $this->params[ 'check_type' ] : 0;//核销类型
        $check_type = 1;//核销类型
        $goods_content = isset($this->params[ 'goods_content' ]) ? $this->params[ 'goods_content' ] : '';//套餐
        $bank_account_name = isset($this->params[ 'bank_account_name' ]) ? $this->params[ 'bank_account_name' ] : '';//银行开户名
        $bank_account_number = isset($this->params[ 'bank_account_number' ]) ? $this->params[ 'bank_account_number' ] : '';//银行帐号
        $bank_address = isset($this->params[ 'bank_address' ]) ? $this->params[ 'bank_address' ] : '';//开户行地址
        $alipay_code = isset($this->params[ 'alipay_code' ]) ? $this->params[ 'alipay_code' ] : '';//支付宝帐号
        $alipay_name = isset($this->params[ 'alipay_name' ]) ? $this->params[ 'alipay_name' ] : '';//支付宝名称
        
        if(!$logo || !$province_name || !$city_name || !$district_name || !$address)
        {
            return $this->error_req(100, "缺少参数");
        }
        if((!$bank_account_name || !$bank_account_number || !$bank_address) && (!$alipay_code || !$alipay_name))
        {
            return $this->error_req(100, "结算信息不能为空");
        }
        $shop = new ShopModel();
        $shopinfo = $shop->getInfo(['shop_id'=>$shop_id],'shop_id,shop_status,cert_id');
        if($shopinfo['shop_status'] == 1)
        {
            return $this->error_req(100, "店铺状态已变更");
        }
        
        $data = [
                "logo" => $logo,
                "province_name" => $province_name,
                "city_name" => $city_name,
                "district_name" => $district_name,
                "address" => $address,
                "longitude" => $longitude,
                "latitude" => $latitude,
                "check_type" => $check_type,
                //"goods_content" => $goods_content,
                "shop_status" => 2
            ];
        
        //店铺表信息
        $req = $shop->saveShop($data,['shop_id'=>$shop_id]);
        //$this->write_log($shop->getLastSql());
        if(!$req)
        {
            return $this->error_req(100, "保存失败1");
        }
        $shopcert = new ShopCert();
        $arr = [
                'bank_account_name' => $bank_account_name,
                'bank_account_number' => $bank_account_number,
                'bank_address' => $bank_address,
                'alipay_code' => $alipay_code,
                'alipay_name' => $alipay_name,
            ];
        //存公司信息
        $req = $shopcert->saveInfo($arr,['cert_id'=>$shopinfo['cert_id']]);
        //$this->write_log($shopcert->getLastSql());
        // if(!$req)
        // {
        //     return $this->error_req(100, "保存失败2");
        // }
        return $this->success_req('提交成功');
    }
    
    
    public function getShopAudit()
    {
        $model = new ShopModel();
        $shopinfo = $model->getInfo(['shop_id'=>$this->shop_id]);
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
        
        $info = array_merge($info,$shopinfo);
        return $this->success_req($info);
    }
    
    
    public function adminList()
    {
        $admin = new Admin();
        $list = $admin->getList(['app_module'=>'shop','shop_id'=>$this->shop_id,'is_manager'=>0],'id,username,status');
        return $this->success_req($list);
    }
    
    //添加或修改子帐号
    public function addAdmin()
    {   
        $this->write_log($this->params);
        $id = $this->params['login_id'];
        $admin = new Admin();
        $admininfo = $admin->getInfo(['id'=>$id],'is_manager');
        if($admininfo['is_manager'] != 1)
        {
            return $this->error_req('非超级管理员不能添加帐号');
        }
        $data = [];
        isset($this->params['username'])?$data['username'] = $this->params['username']:'';
        isset($this->params['password'])?$data['password'] = md5($this->params['password']):'';
        isset($this->params['status'])?$data['status'] = $this->params['status']:'';
        $data['shop_id'] = $this->shop_id;
        $data['app_module'] = 'shop';
        
        if($this->params['id'])
        {
            if($data['username'])
            {
                $info = $admin->getInfo(['app_module'=>'shop','username'=>$data['username']]);
                if($info['id'] != $this->params['id'])
                {
                    return $this->error_req('此名称被占用');
                }
            }
            
            $res = $admin->saveAdmin($data,['id'=>$this->params['id']]);
        }
        else
        {
            $info = $admin->getInfo(['app_module'=>'shop','username'=>$data['username']]);
            if($info)
            {
                return $this->error_req('此名称被占用');
            }
            $data['register_time'] = time();
            $res = $admin->addAdmin($data);
        }
        
        if(!$res)
        {
            return $this->error_req('操作失败');
        }
        return $this->success_req('操作成功');
    }
    
}
