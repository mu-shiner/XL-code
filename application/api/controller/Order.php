<?php
namespace app\api\controller;

use app\common\controller\Apibase;
use think\Cache;
use app\api\model\GroupOrder as OrderModel;
use app\api\model\Users;
use app\api\model\Group as GroupModel;
use app\api\model\GroupJoin;
use app\api\model\UsersBill;
use app\api\model\UsersAddress;
use app\api\model\Verify;
use app\api\model\UsersOrder;
use app\admin\model\Goods;


class Order extends Apibase
{   
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $token = $this->checkToken();
        if(!$this->users_id)
        {
            echo $this->error_req(100, '用户未登录');exit;
        }
        //更新用户操作时间
        $users=new Users();
        $users->saveData(['last_operate_time'=>time()],['users_id'=>$this->users_id]);
    }
    
    /**
     * 创建拼团订单
     */
    public function OrderCreate()
    {   
        $this->write_log($this->params);
        $data = [];
        $data['users_id'] = $this->users_id;
        
        $data['order_price'] = $this->params['order_price'];//订单价格
        $data['group_id'] = $this->params['group_id'];
		$users=new Users();
		//检测用户状态
		$info = $users->getInfo(['users_id'=>$data['users_id']],'users_id,status,username');
		if(!$info['users_id'])
		{
		    return $this->error_req(100, '用户id错误');
		}
		
		if($info['status'] != 1)
		{
		    return $this->error_req(100, '用户状态异常');
		}
		
		$time = time();
	
        
        $model = new GroupModel();
        //检测团购活动
        $groupinfo = $model->getInfo(['group_id '=>$data['group_id']],'group_id,goods_id,goods_name,price,begin_time,end_time,status,goods_class');
        
        //团购活动状态错误
        if($groupinfo['status'] != 1)
        {
            return $this->error_req(100, '活动状态错误');
        }
        if($groupinfo['end_time'] < $time || $groupinfo['begin_time'] > $time)
        {   
            return $this->error_req(100, '不在活动时间');
        }
        //价格错误
        if($data['order_price'] != $groupinfo['price'])
        {
            return $this->error_req(100, '价格错误');
        }
        
        if($groupinfo['goods_class'] == 1)
	    {   //实物填写收货地址
	        if(!$this->params['address_id'])
	        {
	            return $this->error_req(100, '用户地址不能为空');
	        }
	        $address = new UsersAddress();
	        $address_info = $address->getInfo(['address_id'=>$this->params['address_id']]);
	        if(!$address_info)
	        {
	            return $this->error_req(100, '用户地址填写错误');
	        }
	        $data['receipt_name'] = $address_info['name'];
            $data['telephone'] = $address_info['telephone'];
            $data['complete_address'] = $address_info['province_name'].$address_info['city_name'].$address_info['area_name'].$address_info['address'];
	    }
	    else
	    {   //虚拟产品填写联系方式
	        if(!$this->params['phone'])
	        {
	            return $this->error_req(100, '用户联系电话不能为空');
	        }
	        $data['telephone'] = $this->params['phone'];
	    }
        
        $join = new GroupJoin();
        $join_info = $join->getInfo(['group_id'=>$data['group_id'],'users_id'=>$data['users_id'],'join_status'=>0]);
        if($join_info)
        {
            return $this->error_req(100, '正在拼团中，请勿重复参团');
        }
        
        $redis = Cache::getHandler();
        $redis_info = $redis->handler()->hGetAll('user_'.$data['group_id'].'_'.$data['users_id']);
        if($redis_info)
        {
            return $this->error_req(100, '正在拼团中，请勿重复参团');
        }
        
        //$current_date = date("Ymd");
		$data['order_no'] = $this->createno('create_order_no','OR',$data['users_id']);//订单编号
		$data['create_time'] = $time;
	    $data['goods_name'] = $groupinfo['goods_name'];
	    $data['goods_id'] = $groupinfo['goods_id'];
	    $order = new OrderModel();
	    $res = $order->addGroupOrder($data);
		if(!$res)
		{
		    return $this->error_req(100, '系统错误');
		}
		$this->write_log($data);
		return  $this->success_req($data);
    }
    
    //我的订单
    public function myOrderList()
    {
        $this->write_log($this->params);
        $where['users_id'] = $this->users_id;
        $where['status'] = ['gt', 0];
        //未中产品订单不显示
        $where['ship_type'] = ['neq',0];
        if(isset($this->params['status']))
        {
            switch ($this->params['status'])
            {
                // case '1'://待支付
                //     $where['status'] = 0;
                //     break;
                case '2'://待发货
                    $where['order_status'] = 1;
                    $where['ship_type'] = 1;
                    break;
                case '3'://待收货
                    $where['order_status'] = 2;
                    $where['ship_type'] = 1;
                    break;
                case '4'://已完成
                    $where['order_status'] = 3;
                    break;
                default:
                    break;
            }
        }
        $order = new OrderModel();
        $list = $order->getList($where);
        $goods = new Goods();
        $verify = new Verify();
        foreach ($list as &$v)
        {
            $goodsinfo = $goods->getInfo(['id'=>$v['goods_id']],'goods_name,image_url,goods_class');
            
            $v['image_url'] = $goodsinfo['image_url'];
            $v['goods_class'] = $goodsinfo['goods_class'];
            if($v['ship_type'] == 2)
            {
                $verify_info = $verify->getInfo(['order_no'=>$v['order_no']]);
                if(!$verify_info['verify_code'])
                {   
                    //创建核销码
                    $code = $this->createno('create_verify_no','VF',$v['users_id']);
                    $qrcode = $this->createQRcode($verify_info['id'],$code);
                    $verify->saveVerify(['verify_code'=>$code,'verify_qrcode'=>$qrcode],['id'=>$verify_info['id']]);
                    $verify_info['verify_code'] = $code;
                    $verify_info['verify_qrcode'] = $qrcode;
                }
                $v['verify_info'] = $verify_info;
            }
        }
        return  $this->success_req($list);
    }
    
    
    //生成核销二维码
    function createQRcode($id,$code)
    {
        require_once 'phpqrcode.php';
        
        $pathname = "/upload/qrcode/verify/".time().$id.'.png';
        $path = $_SERVER['DOCUMENT_ROOT'].$pathname;
        $dir_name=dirname($path);
        //目录不存在就创建
          if(!file_exists($dir_name))
          {
            $res = mkdir($dir_name,0755,true);
          }
        $url = "http://tc.om/mshop/page_my/verify?verify_code=".$code;//邀请码内容
                      
        $errorCorrectionLevel = 'H';    //容错级别  
        $matrixPointSize = 6;           //生成图片大小  
        
        QRcode::png($url,$path , $errorCorrectionLevel, $matrixPointSize, 2);
        return $pathname;
        
    }
    
    //创建余额充值订单
    public function createBalanceOrder()
    {
        
        $this->write_log($this->params);
        $data = [];
        $data['users_id'] = $this->users_id;
       
        if($this->params['price'] <= 0)
        {
            return $this->error_req(100, '充值金额不能小于0');
        }
        $data['order_price'] = $this->params['price'];
        $data['order_no'] = $this->createno('create_order_no','OR',$data['users_id']);//订单编号
        $data['create_time'] = time();
        $data['goods_name'] = '余额充值';
        isset($this->params['longitude'])?$data['longitude'] = $this->params['longitude']:'';//经纬度
		isset($this->params['latitude'])?$data['latitude'] = $this->params['latitude']:'';//
	    $model = new UsersOrder();
	    $res = $model->addUsersOrder($data);
	    if(!$res)
	    {
	        return $this->error_req(100, '系统错误');
	    }
	    return  $this->success_req($data);
    }
    
    //我的订单
    public function getOrderInfo()
    {
        $this->write_log($this->params);
        
        $where['users_id'] = $this->users_id;
        $order_no = $this->params['order_no'];
        $order = new OrderModel();
        $info = $order->getInfo(['order_no'=>$order_no]);
        if(!$info)
        {
            return $this->error_req(100, '订单编号错误');
        }
        $goods = new Goods();
        $goodsinfo = $goods->getInfo(['id'=>$info['goods_id']],'goods_name,image_url,goods_class');
        
        $info['image_url'] = $goodsinfo['image_url'];
        $info['goods_class'] = $goodsinfo['goods_class'];
        if($info['ship_type'] == 2)
        {   
            $verify = new Verify();
            $verify_info = $verify->getInfo(['order_no'=>$info['order_no']]);
            if(!$verify_info['verify_code'])
            {   
                //创建核销码
                $code = $this->createno('create_verify_no','VF',$info['users_id']);
                $qrcode = $this->createQRcode($verify_info['id'],$code);
                $verify->saveVerify(['verify_code'=>$code,'verify_qrcode'=>$qrcode],['id'=>$verify_info['id']]);
                $verify_info['verify_code'] = $code;
                $verify_info['verify_qrcode'] = $qrcode;
            }
            $info['verify_info'] = $verify_info;
        }
        
        return  $this->success_req($info);
    }
    
    //创建成为合伙人订单
    public function createPartnerOrder()
    {
        
        $this->write_log($this->params);
        $data = [];
        $data['users_id'] = $this->users_id;
        
        $model = new Users();
        //一级分销
        $first_list = $model->getList(['parent_id'=>$this->users_id],'users_id');
        //配置
        $info = $model->getPartnerConfig(['id'=>1],'first_num,price');
        
        // if(count($first_list) < $info['first_num'])
        // {
        //     return $this->error_req(100, '直推人数未达标');
        // }
        $count = $model->where("is_partner",1)->count();
        if($count >= 100) $this->error('合伙人已满员');
        if($this->params['price'] != $info['price'])
        {
            return $this->error_req(100, '支付金额错误');
        }
        $data['order_price'] = $this->params['price'];
        $data['order_no'] = $this->createno('create_order_no','OR',$data['users_id']);//订单编号
        $data['create_time'] = time();
        $data['goods_name'] = '成为合伙人';
        $data['type'] = 1;
	    $model = new UsersOrder();
	    $res = $model->addUsersOrder($data);
	    if(!$res)
	    {
	        return $this->error_req(100, '系统错误');
	    }
	    return  $this->success_req($data);
    }
    public function getPartnerConfig()
    {
           $model = new Users();
          $info = $model->getPartnerConfig(['id'=>1],'first_num,price');
            return  $this->success_req($info);
    }
}
