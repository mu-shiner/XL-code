<?php

namespace app\service\controller;

use app\common\controller\Apibase;
use think\Cache;
use app\shop\model\Admin;
use app\shop\model\Shop;
use app\shop\model\ShopCert;

class Login extends Apibase
{   
   
    /**
     * 登录方法
     */
    public function login()
    {   
        $this->write_log($this->params);
        if (empty($this->params[ "username" ])) return $this->error_req(-1, "账号不能为空!");
        if (empty($this->params[ "password" ])) return $this->error_req(-1, "密码不可为空!");

        // 登录
        $model = new Admin();
        
        $res = $model->getInfo(['username'=>$this->params[ "username" ],'app_module'=>'service']);
        
        if(!$res)
        {
            return $this->error_req(100, "帐号不存在!");
        }
        if($res['password'] != md5($this->params[ "password" ]))
        {
            return $this->error_req(100, "密码错误!");
        }
        // if($res['shop_id'] == 0)
        // {
        //     return $this->success_req(['shop_id' => 0]);
        // }
        // $shop = new Shop();
        // $shopinfo = $shop->getInfo(['shop_id'=>$res[ 'shop_id' ]],'shop_status');
        
        
        $token = $this->createShopToken($res[ 'shop_id' ],1);
        return $this->success_req([ 'token' => $token, 'service_id' => $res[ 'shop_id' ], 'id'=>$res['id']]);
    }
    
    
    
}
