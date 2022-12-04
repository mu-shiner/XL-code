<?php

namespace app\shop\controller;

use app\common\controller\Apibase;
use think\Cache;
use app\shop\model\Admin;
use app\shop\model\Shop;
use app\shop\model\ShopCert;
use app\admin\model\Service;

class Login extends Apibase
{   
   
    /**
     * 登录方法
     */
    public function login()
    {   
        $this->write_log($this->params);
        if (empty($this->params[ "username" ])) return $this->error_req(-1, "商家账号不能为空!");
        if (empty($this->params[ "password" ])) return $this->error_req(-1, "密码不可为空!");

        // 登录
        $model = new Admin();
        
        $res = $model->getInfo(['username'=>$this->params[ "username" ],'app_module'=>'shop']);
        
        if(!$res)
        {
            return $this->error_req(100, "帐号不存在!");
        }
        if($res['password'] != md5($this->params[ "password" ]))
        {
            return $this->error_req(100, "密码错误!");
        }
        if($res['status'] != 1)
        {
            return $this->error_req(100, "此帐号已禁用");
        }
        // if($res['shop_id'] == 0)
        // {
        //     return $this->success_req(['shop_id' => 0]);
        // }
        // $shop = new Shop();
        // $shopinfo = $shop->getInfo(['shop_id'=>$res[ 'shop_id' ]],'shop_status');
        if(isset($this->params[ 'wx_openid' ]) && $this->params[ 'wx_openid' ] != '')
        {
            $model->saveAdmin(['wx_openid'=>''],['wx_openid'=>$this->params[ 'wx_openid' ],'app_module'=>'shop']);
            $model->saveAdmin(['wx_openid'=>$this->params[ 'wx_openid' ]],['id'=>$res[ 'id' ]]);
        }
        //生成access_token
        if($res[ 'shop_id' ] == 0)
        {
            return $this->success_req(['shop_id' => $res[ 'shop_id' ], 'id'=>$res['id']]);
        }
        $token = $this->createShopToken($res[ 'shop_id' ]);
        return $this->success_req([ 'token' => $token, 'shop_id' => $res[ 'shop_id' ], 'id'=>$res['id']]);
    }
    
    
    /**
     * 用户名密码注册
     */
    public function register()
    {   
        $this->write_log($this->params);

        if (empty($this->params[ "username" ])) return $this->response($this->error([], "用户名不可为空!"));
        if (empty($this->params[ "password" ])) return $this->response($this->error([], "密码不可为空!"));
        $service_id = 0;
        if (!empty($this->params[ "invitation_code" ]))
        {
            $service = new Service();
            $serinfo = $service->getInfo(['invitation_code'=>$this->params[ "invitation_code" ]]);
            if(!$serinfo)
            {
                return $this->error_req(100, "邀请码错误");
            }
            $service_id = $serinfo['service_id'];
        }
        
        $model = new Admin();
        $res = $model->getInfo(['username'=>$this->params[ "username" ],'app_module'=>'shop']);
        if($res)
        {
            return $this->error_req(100, "该帐号已注册");
        }
        $data[ 'username' ] = $this->params[ 'username' ];
        $data[ 'password' ] =  md5($this->params[ 'password' ]);
        $data[ 'app_module' ] = 'shop';
        $data[ 'shop_id' ] = 0;
        $data[ 'invitation_id' ] = $service_id;
        $data[ 'is_manager' ] = 1;

        $res = $model->addAdmin($data);
        
        $info = $model->getInfo($data);
        //生成access_token
        // if ($res[ 'code' ] >= 0) {
        //     $token = $this->createToken($res[ 'data' ]);
        //     return $this->response($this->success([ 'token' => $token, 'shop_id' => 0 ]));
        // }
        return $this->success_req($info);
    }
    
    //保存店铺公司信息
    public function saveShop()
    {   
        $this->write_log($this->params);
        $id = $this->params[ "id" ];
        $model = new Admin();
        $res = $model->getInfo(['id'=>$id]);
        if(!$res)
        {
            return $this->error_req(100, "id错误");
        }
        if($res['shop_id'] != 0)
        {
            return $this->error_req(100, "请勿重复填写");
        }
        $shop_name = $this->params[ "shop_name" ];
        $company_name = isset($this->params[ 'company_name' ]) ? $this->params[ 'company_name' ] : '';//公司名称
        $company_province = isset($this->params[ 'company_province' ]) ? $this->params[ 'company_province' ] : '';//公司所在省
        $company_city = isset($this->params[ 'company_city' ]) ? $this->params[ 'company_city' ] : '';//公司所在市
        $company_district = isset($this->params[ 'company_district' ]) ? $this->params[ 'company_district' ] : '';//公司所在区/县
        $company_address = isset($this->params[ 'company_address' ]) ? $this->params[ 'company_address' ] : '';//公司所在地址
        $business_licence_number = isset($this->params[ 'business_licence_number' ]) ? $this->params[ 'business_licence_number' ] : '';//统一社会信用码
        $business_licence_number_electronic = isset($this->params[ 'business_licence_number_electronic' ]) ? $this->params[ 'business_licence_number_electronic' ] : '';//营业执照
        //$business_sphere = isset($this->params[ 'business_sphere' ]) ? $this->params[ 'business_sphere' ] : '';//法定经营范围
        $contacts_name = isset($this->params[ 'contacts_name' ]) ? $this->params[ 'contacts_name' ] : '';//法人姓名
        $contacts_mobile = isset($this->params[ 'contacts_mobile' ]) ? $this->params[ 'contacts_mobile' ] : '';//法人手机
        $contacts_card_no = isset($this->params[ 'contacts_card_no' ]) ? $this->params[ 'contacts_card_no' ] : '';//法人身份证号
        //$contacts_card_electronic_front = isset($this->params[ 'contacts_card_electronic_front' ]) ? $this->params[ 'contacts_card_electronic_front' ] : '';//法人身份证正面
        //$contacts_card_electronic_reverse = isset($this->params[ 'contacts_card_electronic_reverse' ]) ? $this->params[ 'contacts_card_electronic_reverse' ] : '';//法人身份证反面
        
        if(!$shop_name || !$company_name || !$company_province || !$company_city || !$company_district || !$company_address || !$business_licence_number || !$business_licence_number_electronic || !$contacts_name || !$contacts_mobile || !$contacts_card_no)
        {
            return $this->error_req(100, "缺少参数");
        }
        
        $shop = new Shop();
        $shopinfo = $shop->getInfo(['shop_name'=>$shop_name],'shop_id');
        if($shopinfo)
        {
            return $this->error_req(100, "店铺名已存在");
        }
        $time = time();
        $data = [
                'shop_name' => $shop_name,
                'username' => $res['username'],
                'create_time' => $time,
                'service_id' => $res['invitation_id']
            ];
        $shop->addShop($data);
        $shopinfo = $shop->getInfo($data,'shop_id');
        
        $shopcert = new ShopCert();
        $data = [
                'shop_id' => $shopinfo['shop_id'],
                'company_name' => $company_name,
                'company_province' => $company_province,
                'company_city' => $company_city,
                'company_district' => $company_district,
                'company_address' => $company_address,
                'business_licence_number' => $business_licence_number,
                'business_licence_number_electronic' => $business_licence_number_electronic,
                //'business_sphere' => $business_sphere,
                'contacts_name' => $contacts_name,
                'contacts_mobile' => $contacts_mobile,
                'contacts_card_no' => $contacts_card_no,
                // 'contacts_card_electronic_front' => $contacts_card_electronic_front,
                // 'contacts_card_electronic_reverse' => $contacts_card_electronic_reverse
            ];
        //存公司信息
        $req = $shopcert->addInfo($data);
        if(!$req)
        {
            return $this->error_req(100, "保存失败");
        }
        $certinfo = $shopcert->getInfo($data,'cert_id');
        //店铺表存信息id
        $shop->saveShop(['cert_id'=>$certinfo['cert_id']],['shop_id'=>$shopinfo['shop_id']]);
        $model->saveAdmin(['shop_id'=>$shopinfo['shop_id']],['id'=>$id]);
        $token = $this->createShopToken($shopinfo['shop_id']);
        return $this->success_req([ 'token' => $token, 'shop_id' => $shopinfo['shop_id'], 'id'=>$id]);
    }
    
    
}
