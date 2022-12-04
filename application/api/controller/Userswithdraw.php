<?php
namespace app\api\controller;

use app\common\controller\Apibase;
use app\api\model\Order as OrderModel;
use app\api\model\Users;
use app\api\model\UsersConfig;
use app\api\model\UsersWithdraw as UsersWithdrawModel;
use app\api\model\Pay;
use app\api\model\RedisLock;
use app\api\model\UsersBill;

class UsersWithdraw extends Apibase
{   
    /**
     * 用户提现
     */
    public function index()
    {   
        $token = $this->checkToken();
        $this->write_log($this->params);
        $users_id = $this->users_id;
        if(!$users_id)
        {
            return $this->error_req(100, '用户未登录');
        }
        
        $lock = new RedisLock();
        //获取锁（5秒过期）
        $lockinfo = $lock->lock('users_withdraw_'.$users_id,5);
        if(!$lockinfo)
        {
            return  $this->error_req(100, '系统繁忙,请稍后');
        }
        
		$users=new Users();
		
		$info = $users->getInfo(['users_id'=>$users_id],'users_id,username,avatar,phone,status,balance_money,point,partner_money,is_virtual,point_withdraw_amount,direct_users_num,direct_partner_num,withdraw_type,partner_withdraw_amount,coin');
	
		if(!$info['users_id'])
		{
		    return $this->error_req(100, '用户id错误');
		}
		if($info['status'] != 1)
		{
		    return $this->error_req(100, '用户状态异常');
		}
		$h = intval(date("H"));
	    if($h < 9 || $h > 21)
	    {
	        return $this->error_req(100, '当前时间不可提现');
	    }
	    
		$time = time();
		$data = [];
		$data['users_id'] = $users_id;
		$data['type'] = $this->params['type'];//提现类型1:积分,2:余额，3合伙人积分
		$data['withdraw_price'] = $this->params['withdraw_price'];//提现金额
		$data['withdraw_type'] = $this->params['withdraw_type'];//提现方式：bank；alipay；wx;
		$data['real_name'] = $this->params['real_name'];//真实姓名
		isset($this->params['bank_code'])?$data['bank_code'] = $this->params['bank_code']:'';//银行卡号
		isset($this->params['bank_address'])?$data['bank_address'] = $this->params['bank_address']:'';//开户地址
		isset($this->params['alipay_code'])?$data['alipay_code'] = $this->params['alipay_code']:'';//支付宝账号
		isset($this->params['wechat'])?$data['wechat'] = $this->params['wechat']:'';//微信号
		isset($this->params['longitude'])?$data['longitude'] = $this->params['longitude']:'';//经纬度
		isset($this->params['latitude'])?$data['latitude'] = $this->params['latitude']:'';//
		
		$model = new UsersWithdrawModel();
		
		$bill = new UsersBill();
        $balance_bill = $bill->getDataSum(['bill_type'=>'balance_money','bill_data'=>['gt',1],'users_id'=>$users_id],'bill_data');
        $point_bill = $bill->getDataSum(['bill_type'=>'point','bill_data'=>['gt',1],'users_id'=>$users_id],'bill_data');
		$config = new UsersConfig();
		//提现配置
		$con_info = $config->getWithdrawInfo(['config_type'=>$data['type']]);
		if($data['withdraw_price'] < $con_info['min_price'])
		{
			return $this->error_req(100, "提现金额不能低于{$con_info['min_price']}");
		}
	    ////积分
		if($data['type'] == 1)
		{   
		  //  if($h < 9 || $h > 21)
    // 	    {
    // 	        return $this->error_req(100, '当前时间不可提现');
    // 	    }
            // if($balance_bill < 10 && $point_bill < 10)
            // {
            //     return $this->error_req(100, '帐号异常');
            // }
		    if($info['point'] < $data['withdraw_price'])
		    {
		        return $this->error_req(100, '积分不足');
		    }
            $needcoin = intval($data['withdraw_price']*$con_info['coin_rate']/100);//提现所需金币
		    if($needcoin > $info['coin'])  return $this->error_req(100, '金币不足');
		    if($data['withdraw_price'] > $info['point_withdraw_amount'])
		    {
		        return $this->error_req(100, '提现额度不足');
		    }
		    
		    $where = [
    		            'users_id'=>$data['users_id'],
    		            'status'=> 2,
    		            'create_time' => ['between',[strtotime(date('Y-m-d',$time)),$time]]
    		        ];
    		if($info['withdraw_type'] == 1)
            {
                if($data['withdraw_price'] > 300)
        	    {
        	        return $this->error_req(100, '提现金额不能高于300');
        	    }
        	    //今日已提现额度
        	    $money_sum = $model->getWithdrawSum($where,'withdraw_price');
        	    $amount = round(3000 - $money_sum,2);
    		    if($data['withdraw_price'] > $amount)
    		    {
    		        return $this->error_req(100, '今日提现额度还剩'.$amount);
    		    }
            }
          ////每天只能提现一次暂时取消（Thdni-2022625-注释）houtai
          //  $where['type'] = 1;
          //  //今日已提现次数
		  //  $withdraw_num = $model->getCount($where);
		  //  //每日只能提现1次
		  //  if($withdraw_num > 0)
		  //  {
		  //      return $this->error_req(100, '今日提现次数已用完');
		  //  }
		    
		  //  $where = [
		  //          'users_id'=>$data['users_id'],
		  //          'status'=>2,
		  //          'create_time' => ['between',[strtotime(date('Y-m-d',$time)),$time]]
		  //      ];
		    //今日已提现额度
		  //  $money_sum = $model->getWithdrawSum($where,'withdraw_price');
		  //  //剩余提现额度(每日限额5000)
		  //  $amount = round(5000 - $money_sum,2);
		  //  if($data['withdraw_price'] > $amount)
		  //  {
		  //      return $this->error_req(100, '今日提现额度还剩'.$amount);
		  //  }
		    $filed = 'point';
		}
		
		////余额
		elseif($data['type'] == 2)
		{   
		  //  if($h < 9 || $h > 20)
    // 	    {
    // 	        return $this->error_req(100, '当前时间不可提现');
    // 	    }
            if($balance_bill < 10 && $point_bill < 10)
            {
                return $this->error_req(100, '帐号异常');
            }

		    if($info['balance_money'] < $data['withdraw_price'])
		    {
		        return $this->error_req(100, '余额不足');
		    }
		    $where = [
    		            'users_id'=>$data['users_id'],
    		            'status'=> 2,
    		            'create_time' => ['between',[strtotime(date('Y-m-d',$time)),$time]]
    		        ];
    		if($info['withdraw_type'] == 1)
            {
                if($data['withdraw_price'] > 300)
        	    {
        	        return $this->error_req(100, '提现金额不能高于300');
        	    }
        	    //今日已提现额度
        // 	    $money_sum = $model->getWithdrawSum($where,'withdraw_price');
        // 	    $amount = round(3000 - $money_sum,2);
    		  //  if($data['withdraw_price'] > $amount)
    		  //  {
    		  //      return $this->error_req(100, '今日提现额度还剩'.$amount);
    		  //  }
            }
        
          //每天只能提现一次暂时取消（Thdni-2022625-注释）
          //$where['type'] = 2;
          ////今日已提现次数
		  //  $withdraw_num = $model->getCount($where);
		  //  //每日只能提现1次
		  //  if($withdraw_num > 0)
		  //  {
		  //      return $this->error_req(100, '今日提现次数已用完');
		  //  }
		    
		    $filed = 'balance_money';
		    //解锁
            $lock->unlock('users_withdraw_'.$users_id);
		    return  $this->success_req('');
		}
		////合伙人积分
		elseif($data['type'] == 3)
		{   
		  //  if($h < 9 || $h > 20)
    // 	    {
    // 	        return $this->error_req(100, '当前时间不可提现');
    // 	    }
		  //  if(date("w") != 1)//每周一才能提现
    //         {
    //             return $this->error_req(100, '不在提现时间段,提现时间为周一09点到21点');
    //         }
            if($h < 9 || $h > 21)
    	    {
    	        return $this->error_req(100, '当前时间不可提现,提现时间为周一09点到21点');
    	    }
            // if($info['partner_withdraw_nums'] <= 0)  return $this->error_req(100, '没有提现次数了');

		    if($info['partner_money'] < $data['withdraw_price'])
		    {
		        return $this->error_req(100, '合伙人积分不足');
		    }
		    if( $data['withdraw_price'] >3000) return $this->error_req(100, '每日最多可提现3000元');
    	    if($info['direct_users_num'] > 0)
    	    {
    	        $str = '上周推荐注册还差'.$info['direct_users_num'].'人';
    	        return $this->error_req(100, $str);
    	    }
    	   // else
    	   // {
    	   //     if($data['withdraw_price'] > $info['partner_withdraw_amount'])
    	   //     {
    	   //         return $this->error_req(100, '提现额度不足');
    	   //     }
    	   // }
    	    
    	    
		    $filed = 'partner_money';
		}
		
	
		$accountNo = '';
		switch ($data['withdraw_type']) {
            case 'bank':
                $entryType = 'BANK_CARD';
                $accountNo = $data['bank_code'];
                break;
            case 'alipay':
                $entryType = 'ALIPAY_CASH';
                $accountNo = $data['alipay_code'];
                break;
            // case 'wx':
            //     $entryType = 'WX_CASH';
            //     $accountNo = $data['wechat'];
            //     break;
            default:
                return $this->error_req(100, '提现方式错误');
                break;
        }
        if(!$accountNo)
        {
            return $this->error_req(100, '提现帐号错误');
        }

		$data['create_time'] = $time;
		$data['arrival_point'] = round($data['withdraw_price']*$con_info['withdraw_rate']/100,2) + $con_info['withdraw_fixed'];//手续费
		$data['poundage_rate'] = $con_info['withdraw_rate'];//手续费比例
		$data['order_no'] = $this->createno('create_order_no','WD',$data['users_id']);//订单编号
		$data['poundage'] = round($data['withdraw_price']-$data['arrival_point'],2);//实际到账
		
		
		$pay = new Pay();
		$user_data = [
		        'userId' => $info['users_id'],
		        'username' => $info['username'],
		    ];
		$param = $pay->getParam($user_data);
        
        if($info['is_virtual'] == 1)
        {
            $data['poundage'] = 0.1;
        }
        if($info['withdraw_type'] == 1 && $data['type'] != 3)
        {
            $mchNo = $pay->WithdrawMchNo2;
            $appId = $pay->WithdrawAppId2;
            $Key = $pay->WithdrawApiKey2;
        }
        else
        {
            $mchNo = $pay->WithdrawMchNo;
            $appId = $pay->WithdrawAppId;
            $Key = $pay->WithdrawApiKey;
        }
        $postdata = [
                'mchNo' => $mchNo,
                'appId'=> $appId,
                'mchOrderNo' => $data['order_no'],
                'ifCode' => 'alipay',
                'entryType'=> $entryType,
                'amount' => round($data['poundage']*100),
                //'amount' => 10,
                'currency' => 'cny',
                'accountNo'=> $accountNo,
                'accountName'=> $data['real_name'],
                'notifyUrl' => $pay->WithdrawNotifyUrl,
                'transferDesc'=> '用户'.$info['username'].'提现',
                'reqTime' => $time,
                'extParam' => $param,
                'version' => '1.0',
                'signType' => 'MD5'
            ];
        $this->write_log($postdata);
        //提现到银行卡时加开户行
        if($data['withdraw_type'] == 'BANK_CARD')
        {
            $postdata['bankName'] = $data['bank_address'];
        }
        
//        $postdata['sign'] = $pay->getSigna($postdata,$Key);
//
//        $postdata = json_encode($postdata,JSON_UNESCAPED_SLASHES);
//
//        $url = $pay->ApiBase.'/api/transferOrder';
//        $headers = array("Content-type: application/json");
//        $res = $pay->post_curls($url,$postdata,$headers);
//        $this->write_log($res);
//        $res = json_decode($res,true);
//
//        if(!$res || $res['code'] != 0)
//        {
//            return $this->error_req(100, $res['msg']);
//        }
//
		//保存提现信息
		$model->addUsersWithdraw($data);
		//扣除余额
		$users->UsersNumDec(['users_id'=>$users_id], $filed, $data['withdraw_price']);
		
		if($data['type'] == 1)
		{ 
		    //扣除积分提现额度
		    $users->UsersNumDec(['users_id'=>$users_id], 'point_withdraw_amount', $data['withdraw_price']);
			//扣除金币
		    $users->UsersNumDec(['users_id'=>$users_id], 'coin', $needcoin);
		}
		elseif($data['type'] == 3)
		{
		    //扣除积分提现额度
		    $users->UsersNumDec(['users_id'=>$users_id], 'partner_withdraw_amount', $data['withdraw_price']);
		    //扣除金币
			$users->UsersNumDec(['users_id'=>$users_id], 'coin', $needcoin);
		}
		//解锁
        $lock->unlock('users_withdraw_'.$users_id);
        return  $this->success_req('提现申请成功');
    }
    
    
    public function getWithdrawConfig()
    {
        $model = new UsersConfig();
        $list = $model->getWithdrawList(['config_type'=>['neq',4]]);
        return  $this->success_req($list);
    }
    //提现明细
    public function withdrawBill()
    {
        $token = $this->checkToken();
        $this->write_log($this->params);
        $users_id = $this->users_id;
        if(!$users_id)
        {
            return $this->error_req(100, '用户未登录');
        }
        $where['users_id '] = $users_id;
        $type = $this->params['type'];
        if($type)
        {
            $where['type'] = $type;
        }
        $model = new UsersWithdrawModel();
        $list = $model->getList($where,'withdraw_price,create_time,status,fail_msg');
        return  $this->success_req($list);
    }

   
}
