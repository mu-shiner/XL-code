<?php
namespace app\shop\controller;

use app\common\controller\Apibase;
use app\api\model\Users;
use app\api\model\UsersConfig;
use app\shop\model\ShopAccount as ShopAccountModel;
use app\api\model\Pay;
use app\api\model\RedisLock;
use app\shop\model\Shop;
use app\shop\model\ShopCert;

class ShopAccount extends Apibase
{   
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $token = $this->checkShopToken();
     
        if(!$this->shop_id)
        {  
            echo $this->error_req(100, '用户未登录');exit;
        }
    }
    /**
     * 店铺提现
     */
    public function index()
    {   
        $this->write_log($this->params);
        $shop_id = $this->shop_id;
        $lock = new RedisLock();
        //获取锁（5秒过期）
        $lockinfo = $lock->lock('shop_withdraw_'.$shop_id,5);
        if(!$lockinfo)
        {
            return  $this->error_req(100, '系统繁忙,请稍后');
        }
        
		$shop=new Shop();
		
		$info = $shop->getInfo(['shop_id'=>$shop_id],'shop_id,shop_name,shop_status,logo,account,is_virtual');
	
		if(!$info['shop_id'])
		{
		    return $this->error_req(100, '店铺id错误');
		}
		
		if($info['shop_status'] != 1)
		{
		    return $this->error_req(100, '店铺异常');
		}
		$h = intval(date("H"));
	    if($h < 9 || $h > 20)
	    {
	        return $this->error_req(100, '当前时间不可提现,提现时间为每天9点到21点');
	    }
		$time = time();
		$model = new ShopAccountModel();
		$where = [
    		            'shop_id'=>$shop_id,
    		            'status'=> 2,
    		            'create_time' => ['between',[strtotime(date('Y-m-d',$time)),$time]]
    		        ];
        $withdraw_num = $model->getWithdrawCount($where);
         //每日只能提现1次
	    if($withdraw_num > 0)
	    {
	        return $this->error_req(100, '今日提现次数已用完,每天限提现一次');
	    }
		$data = [];
		$data['shop_id'] = $shop_id;
		$data['withdraw_price'] = $this->params['withdraw_price'];//提现金额
		$data['withdraw_type'] = $this->params['withdraw_type'];//提现方式：bank；alipay；wx;
		isset($this->params['alipay_name'])?$data['alipay_name'] = $this->params['alipay_name']:'';//支付宝姓名
		isset($this->params['bank_name'])?$data['bank_name'] = $this->params['bank_name']:'';//银行账户名称
		isset($this->params['bank_code'])?$data['bank_code'] = $this->params['bank_code']:'';//银行卡号
		isset($this->params['bank_address'])?$data['bank_address'] = $this->params['bank_address']:'';//开户地址
		isset($this->params['alipay_code'])?$data['alipay_code'] = $this->params['alipay_code']:'';//支付宝账号
		isset($this->params['wechat'])?$data['wechat'] = $this->params['wechat']:'';//微信号
		isset($this->params['longitude'])?$data['longitude'] = $this->params['longitude']:'';//经纬度
		isset($this->params['latitude'])?$data['latitude'] = $this->params['latitude']:'';//
		
		
		if($data['withdraw_price'] < 10)
	    {
	        return $this->error_req(100, '提现金额不能低于10');
	    }
	
	
	    if($info['account'] < $data['withdraw_price'])
	    {
	        return $this->error_req(100, '余额不足');
	    }
	    
		$accountNo = '';
		$accountName = '';
		switch ($data['withdraw_type']) {
            case 'bank':
                $entryType = 'BANK_CARD';
                $accountNo = $data['bank_code'];
                $accountName = $data['bank_name'];
                break;
            case 'alipay':
                $entryType = 'ALIPAY_CASH';
                $accountNo = $data['alipay_code'];
                $accountName = $data['alipay_name'];
                break;
            // case 'wx':
            //     $entryType = 'WX_CASH';
            //     $accountNo = $data['wechat'];
            //     break;
            default:
                return $this->error_req(100, '提现方式错误');
                break;
        }
        if(!$accountNo || !$accountName)
        {
            return $this->error_req(100, '提现帐号错误');
        }
		
		$config = new UsersConfig();
		//提现配置
		$con_info = $config->getWithdrawInfo(['config_type'=>6]);
		$data['create_time'] = $time;
		$data['arrival_point'] = round($data['withdraw_price']*$con_info['withdraw_rate']/100,2) + $con_info['withdraw_fixed'];//手续费
		$data['poundage_rate'] = $con_info['withdraw_rate'];//手续费比例
		$data['order_no'] = $this->createno('create_order_no','SW',$data['shop_id']);//订单编号
		$data['poundage'] = round($data['withdraw_price']-$data['arrival_point'],2);//实际到账
		
		
		$pay = new Pay();
		$user_data = [
		        'userId' => $info['shop_id'],
		        'username' => $info['shop_name'],
		    ];
		$param = $pay->getParam($user_data);
        
        if($info['is_virtual'] == 1)
        {
            $data['poundage'] = 0.1;
        }
        $postdata = [
                'mchNo' => $pay->ShopWithdrawMchNo,
                'appId'=> $pay->ShopWithdrawAppId,
                'mchOrderNo' => $data['order_no'],
                'ifCode' => 'alipay',
                'entryType'=> $entryType,
                'amount' => round($data['poundage']*100),
                //'amount' => 10,
                'currency' => 'cny',
                'accountNo'=> $accountNo,
                'accountName'=> $accountName,
                'notifyUrl' => $pay->ShopWithdrawNotifyUrl,
                'transferDesc'=> '店铺'.$info['shop_name'].'提现',
                'reqTime' => $time,
                'extParam' => $param,
                'version' => '1.0',
                'signType' => 'MD5'
            ];
        //提现到银行卡时加开户行
        if($data['withdraw_type'] == 'BANK_CARD')
        {
            $postdata['bankName'] = $data['bank_address'];
        }
        
        $postdata['sign'] = $pay->getSigna($postdata,$pay->ShopWithdrawApiKey);
        
        $postdata = json_encode($postdata,JSON_UNESCAPED_SLASHES);
        
        $url = $pay->ApiBase.'/api/transferOrder';
        $headers = array("Content-type: application/json");
        $res = $pay->post_curls($url,$postdata,$headers);
        $this->write_log($res);
        $res = json_decode($res,true);
        
        if(!$res || $res['code'] != 0)
        {   
            $str = $res['msg']?$res['msg']:'提现失败';
            return $this->error_req(100, $str);
        }
        
        
        $this->write_log($data);
		//保存提现信息
		$model->addShopWithdraw($data);
		//扣除余额
		$shop->ShopNumDec(['shop_id'=>$shop_id], 'account', $data['withdraw_price']);

		//解锁
        $lock->unlock('shop_withdraw_'.$shop_id);
        return  $this->success_req('提现成功');
    }
    
    
    public function getWithdrawConfig()
    {
        $model = new UsersConfig();
        $info = $model->getWithdrawInfo(['config_type'=>6]);
        $shop = new Shop();
        $shopinfo = $shop->getInfo(['shop_id'=>$this->shop_id],'cert_id');
        $cert = new ShopCert();
        $certinfo = $cert->getInfo(['cert_id'=>$shopinfo['cert_id']],'bank_address,bank_account_number,bank_account_name,alipay_code,alipay_name');
        //var_dump($info);die;
        if($certinfo)
        {
            $info = array_merge($info,$certinfo);
        }
        
        return  $this->success_req($info);
    }
    
    //提现明细
    public function withdrawBill()
    {
        
        $this->write_log($this->params);
        $shop_id = $this->shop_id;
        
        $where['shop_id'] = $shop_id;
        
        $model = new ShopAccountModel();
        $list = $model->getShopWithdraw($where);
        return  $this->success_req($list);
    }
    
    //账户明细
    public function accountBill()
    {
        $this->write_log($this->params);
        $shop_id = $this->shop_id;
        
        $where['shop_id'] = $shop_id;
        
        $model = new ShopAccountModel();
        $list = $model->getList($where);
        return  $this->success_req($list);
    }

   
}
