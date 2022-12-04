<?php
namespace app\api\controller;

use app\common\controller\Apibase;
use app\api\model\GroupOrder as OrderModel;
use app\api\model\Users;
use app\api\model\Group as GroupModel;
use app\api\model\GroupJoin;
use app\api\model\Pay;
use app\api\model\UsersBill;
use app\api\model\RedisLock;
use app\api\model\UsersOrder;
use think\Db;
use think\Cache;

class OrderPay extends Apibase
{   
   
    /**
     * 拼团订单支付
     */
    public function groupOrder()
    {   
        $token = $this->checkToken();
        //var_dump($this->params);die;
        $this->write_log($this->params);
        $data = [];
        $users_id = $this->users_id;
        $order_no = $this->params['order_no'];
        $pay_type = $this->params['pay_type'];
        if(!$users_id || !$order_no)
        {
            return $this->error_req(100, '系统参数错误');
        }
        
		$order_model = new OrderModel();
		$order_info = $order_model->getInfo(['order_no'=>$order_no]);
		if(!$order_info)
		{
		    return $this->error_req(100, '订单id错误');
		}
		if($order_info['status'] != 0)
		{
		    return $this->error_req(100, '订单状态已改变');
		}
		
		
// 		$lock = new RedisLock();
//         //获取锁（2秒过期）
//         $lockinfo = $lock->lock('lock_sec_'.$order_info['group_id'],2);
//         if(!$lockinfo)
//         {
//             return  $this->error_req(101, '系统繁忙,请稍后');
//         }
		if($pay_type == 0)
		{   //余额支付
		    $res = $this->balance_pay($order_info);
		    
		}
		else
		{   
		    //线上支付
		    $res = $this->online_pay($order_info,$pay_type);
		}
		//解锁
        //$lock->unlock('lock_sec_'.$order_info['group_id']);
        //存支付方式     
        $order_model->saveData(['pay_type'=>$pay_type],['order_no'=>$order_no]);
        $this->write_log($res);
		return $res;
    }
    
    //线上支付
    public function online_pay($order_info,$pay_type,$type=1)
    {   
        $users=new Users();
	
		$info = $users->getInfo(['users_id'=>$order_info['users_id']],'users_id,username,wx_openid,is_virtual');
        $returnUrl = 'http://tc.om/h5/pages/my/my?token='.$this->params['token'];
		//支付后跳转页面
		$pay = new Pay();
		//支付方式
		switch($pay_type)
		{
		    case '1'://支付宝h5
		        $appId = $pay->AppId;
		        $key = $pay->ApiKey;
		        $wayCode = 'ALI_WAP';
		        break;
		    case '2'://微信公众号
		        $appId = $pay->WxAppId;
		        $key = $pay->WxApiKey;
		        $wayCode = 'WX_JSAPI';
		        break;
		    case '3'://微信小程序
		        $appId = $pay->WxAppletAppId;
		        $key = $pay->WxAppletApiKey;
		        $wayCode = 'WX_LITE';
		        break;
		    default:
		        return $this->error_req(100, '支付方式错误'); 
		        break;
		}
		if($type == 1)
		{
		    $notifyUrl = $pay->GroupNotifyUrl;
		}
		else
		{
		    $notifyUrl = $pay->UsersOrderNotifyUrl;
		}
		
		$user_data = [
		        'userId' => $info['users_id'],
		        'username' => $info['username'],
		    ];
		$param = $pay->getParam($user_data);
        
        if($info['is_virtual'] == 1)
        {
            $order_info['order_price'] = 0.1;
        }
        $postdata = [
                'mchNo' => $pay->MchNo,
                'appId'=> $appId,
                'mchOrderNo' => $order_info['order_no'],
                'wayCode' => $wayCode,
                'amount' => round($order_info['order_price']*100),
                //'amount' => 1,
                'currency' => 'cny',
                'subject' => $order_info['goods_name'],
                'body' => $order_info['goods_name'],
                'notifyUrl' => $notifyUrl,
                'returnUrl' => $returnUrl,
                'reqTime' => time(),
                'extParam' => $param,
                'version' => '1.0',
                'signType' => 'MD5'
            ];
        if($pay_type == 2)
        {   
            $postdata['channelExtra'] = json_encode(['openid'=>$info['wx_openid']]);
        }
        $postdata['sign'] = $pay->getSigna($postdata,$key);
        $this->write_log($postdata);
        $postdata = json_encode($postdata,JSON_UNESCAPED_SLASHES);
        
        $url = $pay->ApiBase.'/api/pay/unifiedOrder';
        $headers = array("Content-type: application/json");
        $res = $pay->post_curls($url,$postdata,$headers);
       
        $res = json_decode($res,true);
        $this->write_log($res);
        if($res['code'] != 0)
        {
            return $this->error_req(100, $res['msg']);
        }
        return $this->success_req($res['data']);
    }
    //余额支付
    public function balance_pay($order_info)
    {   
        Db::startTrans();
        try {
            $users=new Users();
      
            $info = $users->getInfo(['users_id'=>$order_info['users_id']],'users_id,username,balance_money');
            
            if($info['balance_money'] < $order_info['order_price'])
            {
                return $this->error_req(100, '余额不足,请充值');
            }
            
            $redis = Cache::getHandler();
            
            $data = [
                    'users_id' => $order_info['users_id'],
                    'group_id' => $order_info['group_id'],
                    'order_no' => $order_info['order_no']
                ];
            //redis存用户拼团列表
            $redis->hMset('user_'.$order_info['group_id'].'_'.$order_info['users_id'], $data);
            //redis存用户拼团id(方便定时任务取redis出价信息)
            $redis->rPush('group_join_list', 'user_'.$order_info['group_id'].'_'.$order_info['users_id']);
            //$model = new GroupModel();
            //处理用户参团
    //         $res = $model->groupUsersJoin($order_info['group_id'],$order_info['users_id'],$order_info['order_no']);
    //         if($res == -1)
		  //  {
		  //      return $this->error_req(101, '系统繁忙,请稍后');
		  //  }
            $arr = [];
            $price = $order_info['order_price'];
            //变负数
            $order_info['order_price'] *= -1;
            $arr['order_id'] = $order_info['order_no'];
            $arr['users_id'] = $order_info['users_id'];
            $arr['bill_type'] = 'balance_money';
            $arr['bill_data'] = $order_info['order_price'];
            $arr['from_type'] = 'balancePay';
            $arr['type_name'] = '余额支付';
            $arr['remark'] = '余额支付拼团活动';
            $arr['current_money'] = $info['balance_money'];
            $arr['users_name'] = $info['username'];
            $arr['create_time'] = time();
            $bill = new UsersBill();
            //存扣款明细
            $bill->addUserBill($arr);
            
            //扣除用户余额
            $users->UsersNumDec(['users_id'=>$order_info['users_id']],'balance_money',$price);
            $order_model = new OrderModel();
            //改变订单状态
            $order_model->saveData(['status'=>1,'pay_time'=>time()],['order_id'=>$order_info['order_id']]);
            
            Db::commit();
            return $this->success_req($res);
        } catch (\Exception $e) {
            
            Db::rollback();
            return $this->error_req(100, $e->getMessage());
        }
    }
    
    //余额充值订单支付
    public function userOrder()
    {   
        $token = $this->checkToken();
        $this->write_log($this->params);
        $data = [];
        $users_id = $this->users_id;
        $order_no = $this->params['order_no'];
        $pay_type = $this->params['pay_type'];
        if(!$users_id || !$order_no)
        {
            return $this->error_req(100, '系统参数错误');
        }
        
		$order_model = new UsersOrder();
		$order_info = $order_model->getInfo(['order_no'=>$order_no]);
		if(!$order_info)
		{
		    return $this->error_req(100, '订单id错误');
		}
		if($order_info['status'] != 0)
		{
		    return $this->error_req(100, '订单状态已改变');
		}
		  
		//支付
		$res = $this->online_pay($order_info,$pay_type,2);
		return $res;
    }
    
}
