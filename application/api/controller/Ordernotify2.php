<?php
namespace app\api\controller;

use app\common\controller\Apibase;
use app\api\model\GroupOrder as OrderModel;
use app\api\model\Users;
use app\api\model\UsersBill;
use app\api\model\Group as GroupModel;
use app\api\model\GroupJoin;
use think\Model;
use app\api\model\UsersWithdraw;
use app\api\model\UsersOrder;
use app\shop\model\ShopAccount;
use app\shop\model\Shop;
use app\service\model\ServiceAccount;
use app\admin\model\Service;
use app\api\model\RedisLock;


class OrderNotify extends Apibase
{   
   
    /**
     * 活动订单支付回调
     */
    public function GroupPayNotify()
    {   
        $data = $this->params;
        $this->write_log($data);
        $out_trade_no = $data['mchOrderNo'];
        if($data['state'] != 2)
        {
            return $this->error_req(100, '支付失败');
        }
        $order = new OrderModel();
        $orderinfo = $order->getInfo(['order_no'=>$out_trade_no]);
        if(!$orderinfo)
        {
            return $this->error_req(100, '订单信息错误');
        }
        if($orderinfo['status'] != 0)
        {
            return $this->error_req(100, '订单状态已变更');
        }
        $time = time();
        $arr = [];
        // $lock = new RedisLock();
        // //获取锁（2秒过期）
        // $lockinfo = $lock->lock('lock_sec_'.$orderinfo['group_id'],2);
        // if(!$lockinfo)
        // {
        //     return  $this->error_req(102, '系统繁忙,请稍后');
        // }
        model('users')->startTrans();
        try {
            $model = new GroupModel();
            //处理用户参团
            $res = $model->groupUsersJoin($orderinfo['group_id'],$orderinfo['users_id'],$orderinfo['order_no']);
            if($res == -1)
		    {
		        return $this->error_req(101, '系统繁忙,请稍后');
		    }
            
            $users = new Users();
            $info = $users->getInfo(['users_id'=>$orderinfo['users_id']],'username');
            $arr = [];
            
            $arr['order_id'] = $orderinfo['order_no'];
            $arr['users_id'] = $orderinfo['users_id'];
            $arr['bill_type'] = 'money';
            $arr['bill_data'] = $orderinfo['order_price'];
            $arr['from_type'] = 'OnlinePay';
            $arr['type_name'] = '线上支付';
            $arr['remark'] = '线上支付拼团活动';
            $arr['users_name'] = $info['username'];
            $arr['create_time'] = time();
            $bill = new UsersBill();
            //存扣款明细
            $bill->addUserBill($arr);
            $order_model = new OrderModel();
            //改变订单状态
            //$order_model->saveData(['status'=>1,'pay_time'=>time()],['order_id'=>$orderinfo['order_id']]);
            
    	    model('users')->commit();
    	    //解锁
            //$lock->unlock('lock_sec_'.$orderinfo['group_id']);
        } catch (\Exception $e) {
            
            model('users')->rollback();
            //$lock->unlock('lock_sec_'.$orderinfo['group_id']);
            return $this->error_req(100, $e->getMessage());
        }
	    unset($userinfo);
	    unset($arr);
	    
		return 'success';
    }
    
    
    /**
     * 用户提现回调
     */
    public function WithdrawNotify()
    {   
        $data = $this->params;
        $this->write_log($data);
        $out_trade_no = $data['mchOrderNo'];
        
        $order = new UsersWithdraw();
        $orderinfo = $order->getInfo(['order_no'=>$out_trade_no]);
        if(!$orderinfo)
        {
            return $this->error_req(100, '提现信息错误');
        }
        if($orderinfo['status'] != 0)
        {
            return $this->error_req(100, '提现状态已变更');
        }
        $users = new Users();
        if($data['state'] != 2)
        {   
            if($data['state'] == 3)
            {
                //更新订单状态
                $up_data = [
                        'status' => -1,
                        'fail_time' => $time,
                        'fail_msg' => $data['errMsg']
                    ];
               
                $order->saveUsersWithdraw($up_data,['id'=>$orderinfo['id']]);
                if($orderinfo['type'] == 1)
        		{
        		    $filed = 'point';
        		}
        		elseif($orderinfo['type'] == 2)
        		{
        		    $filed = 'balance_money';
        		}
        		elseif($orderinfo['type'] == 3)
        		{
        		    $filed = 'partner_money';
        		}
                //返还余额
		        $users->UsersNumInc(['users_id'=>$orderinfo['users_id']], $filed, $orderinfo['withdraw_price']);
            }
            return $this->error_req(100, '提现失败');
        }
        
        $time = time();
        
    
        //更新订单状态
        $up_data = [
                'status' => 2,
                'fail_time' => $time,
                'alipay_trade_no' => $data['channelOrderNo']
            ];
       
        $order->saveUsersWithdraw($up_data,['id'=>$orderinfo['id']]);
        
        
        $userinfo = $users->getInfo(['users_id'=>$orderinfo['users_id']],'users_id,username,point,balance_money,partner_money');
        $arr = [];
        if($orderinfo['type'] == 1)
        {
            $arr['bill_type'] = 'point';
            $arr['remark'] = '用户积分提现';
            $arr['current_money'] = $userinfo['point'];
        }
        elseif($orderinfo['type'] == 2)
        {
            $arr['bill_type'] = 'balance_money';
            $arr['remark'] = '用户余额提现';
            $arr['current_money'] = $userinfo['balance_money'];
        }
        elseif($orderinfo['type'] == 3)
        {
            $arr['bill_type'] = 'partner_money';
            $arr['remark'] = '用户合伙人积分提现';
            $arr['current_money'] = $userinfo['partner_money'];
        }
        //变负数
        $orderinfo['withdraw_price'] *= -1;
        //记录用户交易明细
        $arr['type_name'] = '用户提现';
        $arr['order_id'] = $orderinfo['id'];//关联订单id
        $arr['from_type'] = 'UsersWithdraw';//
	    $arr['users_id'] = $orderinfo['users_id'];//用户id
        $arr['bill_data'] = $orderinfo['withdraw_price'];//变动数据
        $arr['create_time'] = $time;
        $arr['users_name'] = $userinfo['username'];
        
        $userbill = new UsersBill();
        $this->write_log($arr);
	    $userbill->addUserBill($arr);
	    
		return  'success';
    }
    
     /**
     * 充值订单支付回调
     */
    public function UsersOrderPayNotify()
    {   
        $data = $this->params;
        $this->write_log($data);
        $out_trade_no = $data['mchOrderNo'];
        if($data['state'] != 2)
        {
            return $this->error_req(100, '支付失败');
        }
        $order = new UsersOrder();
        $orderinfo = $order->getInfo(['order_no'=>$out_trade_no]);
        if(!$orderinfo)
        {
            return $this->error_req(100, '订单信息错误');
        }
        if($orderinfo['status'] != 0)
        {
            return $this->error_req(100, '订单状态已变更');
        }
        $time = time();
        
        model('users')->startTrans();
        try {
            $model = new Users();
            $info = $model->getInfo(['users_id'=>$orderinfo['users_id']],'username,balance_money,parent_id');
            if($orderinfo['type'] == 1)
            {
                $model->saveData(['is_partner'=>1,'partner_time'=>$time,'partner_pay'=>1],['users_id'=>$orderinfo['users_id']]);
                $bill = new UsersBill();
                $arr1 = [];
                $arr1['order_id'] = $orderinfo['order_no'];
                $arr1['users_id'] = $orderinfo['users_id'];
                $arr1['bill_type'] = 'money';
                $arr1['bill_data'] = $orderinfo['order_price'];
                $arr1['from_type'] = 'OnlinePay';
                $arr1['type_name'] = '开通合伙人';
                $arr1['remark'] = '线上支付开通合伙人';
                $arr1['users_name'] = $info['username'];
                $arr1['current_money'] = $info['balance_money'];
                $arr1['create_time'] = $time;
                //存明细
                $bill->addUserBill($arr1);
                if($info['parent_id'])
                {
                    $con = $model->getPartnerConfig(['id'=>1],'award');
                    if($con['award'])
                    {
                        $parent = $model->getInfo(['users_id'=>$info['parent_id']],'username,balance_money');
                        $arr = [];
                        $arr['order_id'] = $orderinfo['order_no'];
                        $arr['users_id'] = $info['parent_id'];
                        $arr['bill_type'] = 'balance_money';
                        $arr['bill_data'] = $con['award'];
                        $arr['from_type'] = 'partnerPay';
                        $arr['type_name'] = '合伙人奖励';
                        $arr['remark'] = '推荐开通合伙人奖励';
                        $arr['users_name'] = $parent['username'];
                        $arr['current_money'] = $parent['balance_money'];
                        $arr['create_time'] = $time;
                        //存明细
                        $bill->addUserBill($arr);
                        $model->UsersNumInc(['users_id'=>$info['parent_id']],'balance_money',$con['award']);
                    }
                    
                }
            }
            else
            {
                //余额增加
                $model->UsersNumInc(['users_id'=>$orderinfo['users_id']],'balance_money',$orderinfo['order_price']);
                
                $arr = [];
                
                $arr['order_id'] = $orderinfo['order_id'];
                $arr['users_id'] = $orderinfo['users_id'];
                $arr['bill_type'] = 'balance_money';
                $arr['bill_data'] = $orderinfo['order_price'];
                $arr['from_type'] = 'OnlinePay';
                $arr['type_name'] = '线上支付';
                $arr['remark'] = '线上支付余额充值';
                $arr['users_name'] = $info['username'];
                $arr['current_money'] = $info['balance_money'];
                $arr['create_time'] = time();
                $bill = new UsersBill();
                //存扣款明细
                $bill->addUserBill($arr);
            }
            //改变订单状态
            $order->saveData(['status'=>1,'pay_type'=>$orderinfo['pay_type'],'pay_time'=>time()],['order_id'=>$orderinfo['order_id']]);
    	    model('users')->commit();
        } catch (\Exception $e) {
            
            model('users')->rollback();
            return $this->error_req(100, $e->getMessage());
        }
	    
		return 'success';
    }
    
    
    /**
     * 店铺提现回调
     */
    public function ShopWithdrawNotify()
    {   
        $data = $this->params;
        $this->write_log($data);
        $out_trade_no = $data['mchOrderNo'];
        
        $order = new ShopAccount();
        $orderinfo = $order->getWithdrawInfo(['order_no'=>$out_trade_no]);
        if(!$orderinfo)
        {
            return $this->error_req(100, '提现信息错误');
        }
        if($orderinfo['status'] != 0)
        {
            return $this->error_req(100, '提现状态已变更');
        }
        $shop = new Shop();
        if($data['state'] != 2)
        {   
            if($data['state'] == 3)
            {
                //更新订单状态
                $up_data = [
                        'status' => -1,
                        'fail_time' => $time,
                        'fail_msg' => $data['errMsg']
                    ];
               
                $order->saveShopWithdraw($up_data,['id'=>$orderinfo['id']]);
              
                //返还余额
		        $shop->ShopNumInc(['shop_id'=>$orderinfo['shop_id']], 'account', $orderinfo['withdraw_price']);
            }
            return $this->error_req(100, '提现失败');
        }
        
        $time = time();
        
    
        //更新订单状态
        $up_data = [
                'status' => 2,
                'fail_time' => $time,
                'alipay_trade_no' => $data['channelOrderNo']
            ];
       
        $order->saveShopWithdraw($up_data,['id'=>$orderinfo['id']]);
        
        
        $shopinfo = $shop->getInfo(['shop_id'=>$orderinfo['shop_id']],'shop_id,shop_name,account');
        
        //变负数
		$orderinfo['withdraw_price'] *= -1;
		$arr = [
		        'account_no' => $out_trade_no,
		        'shop_id' => $shopinfo['shop_id'],
		        'account_type' => 2,
		        'account_data' => $orderinfo['withdraw_price'],
		        'type_name' => '提现',
		        'create_time' => $time,
		        'current_money' => $shopinfo['account']
		    ];
		//保存余额明细
		$order->addInfo($arr);
        $this->write_log($arr);
	    
		return  'success';
    }
    
    
    /**
     * 服务中心提现回调
     */
    public function ServiceWithdrawNotify()
    {   
        $data = $this->params;
        $this->write_log($data);
        $out_trade_no = $data['mchOrderNo'];
        
        $order = new ServiceAccount();
        $orderinfo = $order->getWithdrawInfo(['order_no'=>$out_trade_no]);
        if(!$orderinfo)
        {
            return $this->error_req(100, '提现信息错误');
        }
        if($orderinfo['status'] != 0)
        {
            return $this->error_req(100, '提现状态已变更');
        }
        $service = new Service();
        if($data['state'] != 2)
        {   
            if($data['state'] == 3)
            {
                //更新订单状态
                $up_data = [
                        'status' => -1,
                        'fail_time' => $time,
                        'fail_msg' => $data['errMsg']
                    ];
               
                $order->saveServiceWithdraw($up_data,['id'=>$orderinfo['id']]);
              
                //返还余额
		        $service->serviceNumInc(['service_id'=>$orderinfo['service_id']], 'account', $orderinfo['withdraw_price']);
            }
            return $this->error_req(100, '提现失败');
        }
        
        $time = time();
        
    
        //更新订单状态
        $up_data = [
                'status' => 2,
                'fail_time' => $time,
                'alipay_trade_no' => $data['channelOrderNo']
            ];
       
        $order->saveServiceWithdraw($up_data,['id'=>$orderinfo['id']]);
        
        
        $serviceinfo = $service->getInfo(['service_id'=>$orderinfo['service_id']],'service_id,service_name,account');
        
        //变负数
		$orderinfo['withdraw_price'] *= -1;
		$arr = [
		        'account_no' => $out_trade_no,
		        'service_id' => $serviceinfo['service_id'],
		        'account_type' => 2,
		        'account_data' => $orderinfo['withdraw_price'],
		        'type_name' => '提现',
		        'create_time' => $time,
		        'current_money' => $serviceinfo['account']
		    ];
		//保存余额明细
		$order->addInfo($arr);
        $this->write_log($arr);
	    
		return  'success';
    }
   
}
