<?php

namespace app\shop\controller;

use app\common\controller\Apibase;
use think\Cache;
use app\shop\model\Admin;
use app\shop\model\Shop;
use app\shop\model\ShopCert;
use app\admin\model\Service;
use app\shop\model\Verify as VerifyModel;
use app\shop\model\ShopAccount;
use app\api\model\GroupOrder;
use app\api\model\Goods;
use app\service\model\ServiceAccount;
use app\api\model\Users;
use app\api\model\UsersConfig;

class Index extends Apibase
{   
    /**
     * 核销
     */
    public function QrcodeVerify()
    {   
        $this->write_log($this->params);
        $openid = $this->params['wx_openid'];
        if(!$openid)
        {
            return $this->error_req(100, '登录失效,请重新登录');
        }
        $admin = new Admin();
        $admininfo = $admin->getInfo(['wx_openid'=>$openid,'app_module'=>'shop']);
        if(!$admininfo)
        {
            return $this->error_req(100, '此微信未绑定管理员帐号');
        }
        $shop = new Shop();
        $shop_info = $shop->getInfo(['shop_id'=>$admininfo['shop_id']],'shop_id,shop_name,shop_status,logo,check_type,settlement_price,account,service_id');
        if($shop_info['shop_status'] != 1 || $shop_info['take_down'] == 1)
        {
            return $this->error_req(100, '店铺状态异常');
        }
        $verify_code = isset($this->params['verify_code']) ? $this->params['verify_code'] : '';
        $verify_model = new VerifyModel();
        $verify_info = $verify_model->getInfo(["verify_code"=>$verify_code]);
        if(!$verify_info)
        {
            return $this->error_req(100, '找不到核销码信息!');
        }
        if($verify_info['is_verify'] != 0)
        {
            return $this->error_req(100, '核销码已被使用或已过期');
        }
        if($verify_info['trade_status'] != 0)
        {
            return $this->error_req(100, '核销券兑换中');
        }
        if($shop_info['check_type'] != $verify_info['check_type'])
        {
            return $this->error_req(100, '核销类型不符合!');
        }
        $users = new Users();
        $userinfo = $users->getInfo(['users_id'=>$verify_info['users_id']],'status');
        if($userinfo['status'] != 1)
        {
            return $this->error_req(100, '用户状态异常,禁止使用');
        }
        model('verify')->startTrans();
        try {
            $time = time();
            $data = [
                    'is_verify' => 1,
                    'verify_time' => $time,
                    'shop_id' =>$shop_info['shop_id'],
                    'shop_name' =>$shop_info['shop_name'],
                    'verifier_name' => $admininfo['username']
                ];
            $res = $verify_model->saveVerify($data,['id'=>$verify_info['id']]);
            if(!$res)
            {
                return $this->error_req(100, '核销失败');
            }
            //店铺增加结算价格
            $shop->ShopNumInc(['shop_id'=>$shop_info['shop_id']],'account',$shop_info['settlement_price']);
            $shop->ShopNumInc(['shop_id'=>$shop_info['shop_id']],'all_account',$shop_info['settlement_price']);
            //所属服务中心增加结算余额
            if($shop_info['service_id'])
            {
                $service = new Service();
                $serinfo = $service->getInfo(['service_id'=>$shop_info['service_id']]);
                if($serinfo)
                {   
                    $seraccount = 3;
                    $service->serviceNumInc(['service_id'=>$shop_info['service_id']],'account',$seraccount);
                    $service_account = new ServiceAccount();
                    $serarr = [    
                            'account_no' => $shop_info['shop_id'],
                            'service_id' => $shop_info['service_id'],
                            'account_type' => 1, //类型1核销2提现
                            'account_data' => $seraccount,
                            'type_name' => '店铺核销结算',
                            'create_time' => $time,
                            'current_money' => $serinfo['account']
                        ];
                    //记录服务中心账户明细
                    $service_account->addInfo($serarr);
                }
                
            }
            $account = new ShopAccount();
            $arr = [    
                    'account_no' => $verify_code,
                    'shop_id' => $shop_info['shop_id'],
                    'account_type' => 1, //类型1核销2提现
                    'account_data' => $shop_info['settlement_price'],
                    'type_name' => '核销结算',
                    'create_time' => $time,
                    'current_money' => $shop_info['account']
                ];
            //记录账户明细
            $account->addInfo($arr);
            $goods = new Goods();
            //商品核销数量+1
            $goods->goodsNumInc(['id'=>$verify_info['goods_id']],'verify_num');
            
            $config = new UsersConfig();
            $coninfo = $config->getWithdrawInfo(['config_type'=>1],'withdraw_amount');
            //核销一张券，增加配置的积分提现额度
            if($coninfo['withdraw_amount'])
            {
                $users->UsersNumInc(['users_id'=>$verify_info['users_id']], 'point_withdraw_amount', $coninfo['withdraw_amount']);
            }
            
            model('verify')->commit();
            $token = $this->createShopToken($shop_info[ 'shop_id' ]);
            return $this->success_req([ 'token' => $token, 'shop_id' => $shop_info[ 'shop_id' ], 'id'=>$admininfo['id']]);
            // return $this->success_req('核销成功');
        } catch (\Exception $e) {
            
            model('verify')->rollback();
            return $this->error_req(100, $e->getMessage());
        }
    }
    
    
    
}
