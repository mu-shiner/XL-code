<?php
namespace app\api\controller;

use app\api\model\MarketSet;
use app\common\controller\Apibase;
use app\api\model\Users;
use app\api\model\TradeMarket as TradeMarketModel;
use app\api\model\Verify;
use app\api\model\UsersOrder;
use app\api\model\Goods;
use app\api\model\UsersBill;
use app\api\model\Pay;

use think\Db;

class TradeMarket extends Apibase
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
    public function goodsList()
    {
        $users_id = $this->users_id;
        $goodsids = Verify::where("users_id",$users_id)->where(['is_verify'=>0,'trade_status'=>0])->column("goods_id");
        $goodsList = Goods::where("is_prize",0)->where("goods_class",2)->where("id","in",$goodsids)->field("id,goods_name,goods_price")->select();
        return  $this->success_req($goodsList);
    }
    public function userVerifyCount(){
        if(empty($this->params['goods_id'])) $this->params['goods_id']=0;
        $users_id = $this->users_id;
        $time = time();
        $count = Verify::where(['users_id'=>$users_id,'goods_id'=>$this->params['goods_id'],'is_verify'=>0,'trade_status'=>0])
        ->where("(expired_time = 0 or expired_time > {$time})")
        ->count();
        return  $this->success_req(['nums'=>$count]);
    }
    /**
     * 用户通用券帐户
     */
    public function usersAccount()
    {   
        $this->write_log($this->params);
        if(empty($this->params['goods_id'])) $this->params['goods_id']=0; 
        $data = [];
        $users_id = $this->users_id;
       
		$verify=new Verify();
		$data = [
		        'valid_num' => 0,
		        'verify_num' => 0,
		        'trade_num' => 0,
		        'expired_num' => 0
		    ];
		$list = $verify->getList(['users_id'=>$users_id,'goods_id'=>$this->params['goods_id']],'users_id,is_verify,trade_status');
        foreach ($list as $v)
        {   
            switch ($v['is_verify'])
            {
                case '0':
                    if($v['trade_status'] == 0)
                    {
                        $data['valid_num'] ++;
                    }
                    break;
                case '1':
                    $data['verify_num'] ++;
                    break;
                case '2':
                    $data['expired_num'] ++;
                    break;
                case '3':
                    $data['trade_num'] ++;
                    break;
                default:
                    // code...
                    break;
            }
            // if($v['is_verify'] == 2)//已过期
            // {   
            //     $data['expired_num'] ++;
            //     continue;
            // }
            // elseif($v['is_verify'] == 1)//已核销
            // {
            //     $data['verify_num'] ++;
            //     continue;
            // }
            //转让中
            // if($v['trade_status'] == 1)
            // {
            //     $data['trade_num'] ++;
            //     continue;
            // }
            //已兑换
            // elseif($v['is_verify'] == 3)
            // {
            //     $data['trade_num'] ++;
            //     continue;
            // }
            // elseif($v['is_verify'] == 0 && $v['trade_status'] == 0)
            // {
            //     //可用
            //     $data['valid_num'] ++;
            // }
            
        }
        $model = new TradeMarketModel();
        //汇率
        $data['rate'] = $model->getRate();
        
		return  $this->success_req($data);
    }
    
    //创建转让
    public function createTrade()
    {   
       
        $this->write_log($this->params);
        $data['users_id'] = $this->users_id;
        $data['num'] = $this->params['num'];
        $data['price'] = $this->params['price'];
        $data['unit_price'] = $this->params['unit_price'];
        $password = $this->params['password'];
        if(empty($this->params['goods_id'])) $this->error("请选择一种券");
        $users = new Users();
        $userinfo = $users->getInfo(['users_id'=>$data['users_id']],'password');
        if(md5($password) != $userinfo['password'])
        {
            return $this->error_req(100, '输入密码错误');
        }
        // if($data['unit_price'] > 350)
        // {
        //     return $this->error_req(100, '转让单价不能高于350');
        // }
        if(round($data['price']/$data['num'],2) != $data['unit_price'])
        {
            return $this->error_req(100, '价格错误');
        }
        $verify=new Verify();
        //有过期时间优先转让
        $list1 = $verify->getList(['users_id'=>$data['users_id'],'is_verify'=>0,'trade_status'=>0,'expired_time'=>['neq',0],'goods_id'=>$this->params['goods_id']],'id,goods_id');
        $list2 = $verify->getList(['users_id'=>$data['users_id'],'is_verify'=>0,'trade_status'=>0,'expired_time'=>0,'goods_id'=>$this->params['goods_id']],'id,goods_id');
        if(!$list1 && !$list2)
        {
            return $this->error_req(100, '无可用券数量');
        }
        if(!$list1)
        {
            $list = $list2;
        }
        elseif (!$list2)
        {
            $list = $list1;
        }
        else
        {
            $list = $list1 + $list2;
        }
        
        if($data['num'] > count($list))
        {
            return $this->error_req(100, '转让数量超出可用券数量');
        }
        $num = $data['num'];
        $id_list = [];
        for ($i = 0; $i < $num; $i++)
        {
             $id_list[] = $list[$i]['id'];
             $goods_id = $list[$i]['goods_id'];
        }
        $model = new TradeMarketModel();
        $time = time();
        $data['create_time'] = $time;
        $data['end_time'] = $time + 24*60*60;//过期时间24小时
        $data['verify_list'] = implode(',',$id_list);
        $data['goods_id'] = $goods_id;
        $res = $model->addData($data);
        if(!$res)
        {
            return $this->error_req(100, '系统错误');
        }
        $verify->saveVerify(['trade_status'=>1],['id'=>['in',$id_list]]);
        // if($data['unit_price'] <= 260)
        // {
        //     $info = $model->getInfo($data,'market_id');
        //     $this->automaticBuy($info['market_id']);
        // }
        return  $this->success_req('创建成功');
    }
    
    //转让市场
    public function getTradeMarketList()
    {   
        $this->write_log($this->params);
        $model = new TradeMarketModel();
        $order = $this->params['order'];//排序0综合1单价2数量
        $by = isset($this->params['by'])?$this->params['by']:1;//排序方式1正序0倒序
        $page = isset($this->params['page'])?$this->params['page']:1;
        $size = isset($this->params['size'])?$this->params['size']:20;
        switch ($order) {
            case '0':
                $orderby = 'create_time';
                break;
            case '1':
                $orderby = 'unit_price';
                break;
            case '2':
                $orderby = 'num';
                break;
            default:
                $orderby = 'create_time';
                break;
        }
      
        if($by == 1)
        {
            $orderby .= ' ASE';
        }
        else
        {
            $orderby .= ' DESC';
        }
        $where['status'] = 1;
        $users = new Users();
        if(isset($this->params['username']))
        {
            $usersinfo = $users->getInfo(['username'=>$this->params['username']],'users_id');
            if($usersinfo['users_id'])
            {
                $where['users_id'] = $usersinfo['users_id'];
            }
            else
            {
                $where['users_id'] = 0;
            }
        }
        $list = $model->getList($where,'*',$orderby, ($page-1)*$size, $size);
        $goods = new Goods();
        
        foreach ($list as &$v)
        {
            $goods_info = $goods->getInfo(['id'=>$v['goods_id']],'goods_name,image_url');
            $user_info = $users->getInfo(['users_id'=>$v['users_id']],'username,reg_time');
            $v['goods_name'] = $goods_info['goods_name'];
            $v['image_url'] = $goods_info['image_url'];
            $v['username'] = $this->StrSubstrReplace($user_info['username']);
            if($user_info['reg_time'] < 1655654400)
            {
                $v['is_red'] = 1;
            }
            else
            {
                $v['is_red'] = 0;
            }
        }
        $arr = [
                'list' => $list,
                'count' => $model->getAllNum($where)
            ];
        //$list['count'] = $model->getAllNum(['status'=>1]);
        return  $this->success_req($arr);
    }
    
    public function buyMarketBatch()
    {
        $this->write_log($this->params);
        $users_id = $this->users_id;
        $market_idlist = explode(',',$this->params['market_idlist']);
        $price = $this->params['price'];
        $password = $this->params['password'];
        $users = new Users();
        $userinfo = $users->getInfo(['users_id'=>$users_id],'username,password,balance_money,withdraw_type');
        if(md5($password) != $userinfo['password'])
        {
            return $this->error_req(100, '输入密码错误');
        }
        if($userinfo['balance_money'] < $price)
        {
            return $this->error_req(100, '余额不足');
        }
        // if($userinfo['withdraw_type'] == 1)
        // {
        //     return $this->error_req(100, '系统繁忙，转让失败');
        // }
        $model = new TradeMarketModel();
        $arr = [];
        $all_price = 0;
        foreach ($market_idlist as $value)
        {   
            $arr1 = [];
            $info = $model->getInfo(['market_id'=>$value]);
            if(!$info)
            {
                return $this->error_req(100, 'id错误');
            }
            if($info['users_id'] == $users_id)
            {
                return $this->error_req(100, '不能购买自己转让的');
            }
            if($info['status'] != 1)
            {
                return $this->error_req(100, '该通用券状态已变更');
            }
            $arr1['market_id'] = $value;
            $arr1['status'] = $info['status'];
            $arr1['users_id'] = $info['users_id'];
            $arr1['verify_list'] = $info['verify_list'];
            $arr1['price'] = $info['price'];
            $arr1['num'] = $info['num'];
            $arr1['create_time'] = $info['create_time'];
            $all_price += $info['price'];
            $arr[] = $arr1;
        }
        if($all_price != $price)
        {   
            $data = [
                    'all_price' => $all_price,
                    'price' => $price
                ];
            $this->write_log($data);
            return $this->error_req(100, '价格错误');
        }
        
        foreach ($arr as $v)
        {   
            $res = $this->marketChange($v,$users_id);
        }
        return $res;
    }
    
    //购买通用券
    public function buyMarket()
    {
        $this->write_log($this->params);
        $users_id = $this->users_id;
        $market_id = $this->params['market_id'];
        $price = $this->params['price'];
        $password = $this->params['password'];
        $users = new Users();
        $userinfo = $users->getInfo(['users_id'=>$users_id],'username,password,balance_money,withdraw_type');
        if(md5($password) != $userinfo['password'])
        {
            return $this->error_req(100, '输入密码错误');
        }
        if($userinfo['withdraw_type'] == 1)
        {
            return $this->error_req(100, '系统繁忙，转让失败');
        }
        $model = new TradeMarketModel();
        $info = $model->getInfo(['market_id'=>$market_id]);
        if(!$info)
        {
            return $this->error_req(100, 'id错误');
        }
        if($info['status'] != 1)
        {
            return $this->error_req(100, '该通用券状态已变更');
        }
        if($info['price'] != $price)
        {
            return $this->error_req(100, '价格错误');
        }
        if($userinfo['balance_money'] < $price)
        {
            return $this->error_req(100, '余额不足');
        }
        if($info['users_id'] == $users_id)
        {
            return $this->error_req(100, '不能购买自己转让的');
        }
        $id_list = explode(',',$info['verify_list']);
        Db::startTrans();
        try {
            $time = time();
            //改变转让状态
            $model->saveData(['buy_users_id'=>$users_id,'status'=>2,'finish_time'=>$time],['market_id'=>$market_id]);
            $verify = new Verify();
            foreach ($id_list as $v)
            {
                $qrcode = $verify->getInfo(['id'=>$v],'verify_qrcode');
                if(file_exists($_SERVER['DOCUMENT_ROOT'].$qrcode['verify_qrcode']))
                {
                    @unlink($_SERVER['DOCUMENT_ROOT'].$qrcode['verify_qrcode']);
                }
                
            }
            //改变通用券的拥有用户id,改变交易状态,有效期变无限,换核销码和二维码
            $verify->saveVerify(['users_id'=>$users_id,'trade_status'=>0,'expired_time'=>0,'verify_code'=>'','verify_qrcode'=>''],['id'=>['in',$id_list]]);
			$set = MarketSet::order("id desc")->cache("market_set",120)->find();
			$inc = round($price*(1-$set['rate']),2);
            //转让用户增加余额
            $users->UsersNumInc(['users_id'=>$info['users_id']],'balance_money',$inc);
            $user_info = $users->getInfo(['users_id'=>$info['users_id']],'username,balance_money,wx_openid');
            $arr = [];
            $arr['order_id'] = $market_id;
            $arr['users_id'] = $info['users_id'];
            $arr['bill_type'] = 'balance_money';
            $arr['bill_data'] = $inc;
            $arr['from_type'] = 'TradeMarket';
            $arr['type_name'] = $info['num'].'张通用券转让';
            $arr['remark'] = $info['num'].'张通用券转让成功获得余额';
            $arr['users_name'] = $user_info['username'];
            $arr['current_money'] = $user_info['balance_money'];
            $arr['create_time'] = $time;
            $bill = new UsersBill();
            //存余额明细
            $bill->addUserBill($arr);
            if($user_info['wx_openid'])
            {   
                $name = '通用券'.$info['num'].'张';
                $ctime = date('Y-m-d H:i:s',$info['create_time']);
                $url = 'http://tc.om/h5/page_my/couponsTrade';
                $str = '通用券转让成功,扣除10%手续费,获得'.$inc.'元,已存入余额';
                $this->fasong($user_info['wx_openid'],$name,$ctime,$price,$url,$str);
            }
			//增加用户积分
			$users->UsersNumInc(['users_id'=>$info['users_id']],'point',round($price*$set['jifen'],5));
            //购买用户减少余额
            $users->UsersNumDec(['users_id'=>$users_id],'balance_money',$price);
            
            $arr1 = [];
            //变负数
            $price *= -1;
            $arr1['order_id'] = $market_id;
            $arr1['users_id'] = $users_id;
            $arr1['bill_type'] = 'balance_money';
            $arr1['bill_data'] = $price;
            $arr1['from_type'] = 'TradeMarket';
            $arr1['type_name'] = '购买通用券'.$info['num'].'张';
            $arr1['remark'] = '购买'.$info['num'].'张通用券成功扣除余额';
            $arr1['users_name'] = $userinfo['username'];
            $arr1['current_money'] = $userinfo['balance_money'];
            $arr1['create_time'] = $time;
            //存余额明细
            $bill->addUserBill($arr1);
            
            Db::commit();
            return $this->success_req('购买成功');
        } catch (\Exception $e) {
            
            Db::rollback();
            return $this->error_req(100, $e->getMessage());
        }
    }
    
    
    public function marketChange($info,$users_id)
    {
        $model = new TradeMarketModel();
        
        $id_list = explode(',',$info['verify_list']);
        Db::startTrans();
        try {
            $time = time();
            //改变转让状态
            $model->saveData(['buy_users_id'=>$users_id,'status'=>2,'finish_time'=>$time],['market_id'=>$info['market_id']]);
            $verify = new Verify();
            foreach ($id_list as $v)
            {
                $qrcode = $verify->getInfo(['id'=>$v],'verify_qrcode');
                if($qrcode['verify_qrcode'])
                {   
                    if(file_exists($_SERVER['DOCUMENT_ROOT'].$qrcode['verify_qrcode']))
                    {
                        unlink($_SERVER['DOCUMENT_ROOT'].$qrcode['verify_qrcode']);
                    }
                    
                }
            }
            //改变通用券的拥有用户id,改变交易状态,有效期变无限,换核销码和二维码
            $verify->saveVerify(['users_id'=>$users_id,'trade_status'=>0,'expired_time'=>0,'verify_code'=>'','verify_qrcode'=>''],['id'=>['in',$id_list]]);
			$set = MarketSet::order("id desc")->cache("market_set",120)->find();
			$inc = round($info['price']*(1-$set['rate']/100),2);
            //转让用户增加余额
            $users = new Users();
            $users->UsersNumInc(['users_id'=>$info['users_id']],'balance_money',$inc);
            $user_info = $users->getInfo(['users_id'=>$info['users_id']],'username,balance_money,wx_openid');
            $arr = [];
            $arr['order_id'] = $info['market_id'];
            $arr['users_id'] = $info['users_id'];
            $arr['bill_type'] = 'balance_money';
            $arr['bill_data'] = $inc;
            $arr['from_type'] = 'TradeMarket';
            $arr['type_name'] = '通用券转让';
            $arr['remark'] = $info['num'].'张通用券转让成功获得余额';
            $arr['users_name'] = $user_info['username'];
            $arr['current_money'] = $user_info['balance_money'];
            $arr['create_time'] = $time;
            $bill = new UsersBill();
            //存余额明细
            $bill->addUserBill($arr);
            if($user_info['wx_openid'])
            {   
                $name = '通用券'.$info['num'].'张';
                $ctime = date('Y-m-d H:i:s',$info['create_time']);
                $url = 'http://tc.om/h5/page_my/couponsTrade';
                $str = '通用券转让成功,扣除10%手续费,获得'.$inc.'元,已存入余额';
                $this->fasong($user_info['wx_openid'],$name,$ctime,$info['price'],$url,$str);
            }
			//增加用户积分
			$users->UsersNumInc(['users_id'=>$info['users_id']],'point',round($info['price']*$set['jifen'],5));
            //购买用户减少余额
            $users->UsersNumDec(['users_id'=>$users_id],'balance_money',$info['price']);
            $userinfo = $users->getInfo(['users_id'=>$users_id],'username,balance_money');
            $arr1 = [];
            //变负数
            $info['price'] *= -1;
            $arr1['order_id'] = $info['market_id'];
            $arr1['users_id'] = $users_id;
            $arr1['bill_type'] = 'balance_money';
            $arr1['bill_data'] = $info['price'];
            $arr1['from_type'] = 'TradeMarket';
            $arr1['type_name'] = '购买通用券';
            $arr1['remark'] = '购买'.$info['num'].'张通用券成功扣除余额';
            $arr1['users_name'] = $userinfo['username'];
            $arr1['current_money'] = $userinfo['balance_money'];
            $arr1['create_time'] = $time;
            //存余额明细
            $bill->addUserBill($arr1);
            
            Db::commit();
            return $this->success_req('购买成功');
        } catch (\Exception $e) {
            
            Db::rollback();
            return $this->error_req(100, $e->getMessage());
        }
    }
    
    
    // {{first.DATA}}
    // 转让产品：{{keyword1.DATA}}
    // 转让时间：{{keyword2.DATA}}
    // 转让金额：{{keyword3.DATA}}
    // {{remark.DATA}}
    public function fasong($openid,$name,$ctime,$price,$url,$str)
    {   
        $pay = new Pay();
        $data = array(
            "touser"=>$openid,
            "template_id"=>$pay->TradeSuccessTemplateId,
            "url" => $url,
            "data" => array(
                "first" => array(
                    "value"=>"通用券转让成功",
                ),
                "keyword1" => array(
                    "value"=>$name
                ),
                "keyword2" => array(
                    "value"=>$ctime
                ),
                "keyword3" => array(
                    "value"=>$price
                ),
                "remark" => array(
                    "value"=>$str
                ),
            )
        );
        $res = $pay->template($data);
        return $res;
    }

    
    //我的通用券
    public function myVerify()
    {
        $this->write_log($this->params);
        $model = new Verify();
        $where['users_id'] = $this->users_id;
        switch ($this->params['status'])
        {
            case '1'://可用的
                $where['is_verify'] = 0;//0可用1已核销2已过期
                $where['trade_status'] = 0;//0无交易1交易中
                break;
            case '2'://已核销的
                $where['is_verify'] = 1;
                break;
            case '3'://转让中
                $where['is_verify'] = 0;
                $where['trade_status'] = 1;
                break;
            case '4'://过期的
                $where['is_verify'] = 2;
                break;
            default:
                break;
        }
        $list = $model->getList($where);
        $goods = new Goods();
        foreach ($list as &$value)
        {
            switch ($value['is_verify'])
            {
                case '0':
                    if($value['trade_status'] == 0)
                    {
                        $value['status'] = 1;//1可用的2已核销的3转让中4过期的
                    }
                    else
                    {
                        $value['status'] = 3;
                    }
                    break;
                case '1':
                    $value['status'] = 2;
                    break;
                case '2':
                    $value['status'] = 4;
                    break;
                default:
                    // code...
                    break;
            }
            if(!$value['verify_code'])
            {   
                //创建核销码
                $code = $this->createno('create_verify_no','VF',$value['users_id']);
                $qrcode = $this->createQRcode($value['id'],$code);
                $model->saveVerify(['verify_code'=>$code,'verify_qrcode'=>$qrcode],['id'=>$value['id']]);
                $value['verify_code'] = $code;
                $value['verify_qrcode'] = $qrcode;
            }
            $goods_info = $goods->getInfo(['id'=>$value['goods_id']],'goods_name,image_url,goods_price');
            $value['goods_name'] = $goods_info['goods_name'];
            $value['image_url'] = $goods_info['image_url'];
            $value['goods_price'] = $goods_info['goods_price'];
        }
        return $this->success_req($list);
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
    
    //汇率变动
    public function getRateList()
    {
        $model = new TradeMarketModel();
        $list = $model->getRateList();
        return  $this->success_req($list);
    }
    
    //通用券转让明细
    public function getMarketBill()
    {
        
        $this->write_log($this->params);
        $users_id = $this->users_id;
       
        $where['from_type'] = 'TradeMarket';
        $where['users_id'] = $users_id;
        
        $model = new UsersBill();
        $list = $model->getList($where);
        return  $this->success_req($list);
    }
    
    //用户31自动购买低于260以下通用券(包含260)
    public function automaticBuy($market_id)
    {
        $users_id = 31;
        $users = new Users();
        $userinfo = $users->getInfo(['users_id'=>$users_id],'username,password,balance_money');
       
        $model = new TradeMarketModel();
        $info = $model->getInfo(['market_id'=>$market_id]);
        $price = $info['price'];
        if(!$info)
        {
            return $this->error_req(100, 'id错误');
        }
        if($info['status'] != 1)
        {
            return $this->error_req(100, '该通用券状态已变更');
        }
        
        if($userinfo['balance_money'] < $price)
        {
            return $this->error_req(100, '余额不足');
        }
        if($info['users_id'] == $users_id)
        {
            return $this->error_req(100, '不能购买自己转让的');
        }
        $id_list = explode(',',$info['verify_list']);
        Db::startTrans();
        try {
            $time = time();
            //改变转让状态
            $model->saveData(['buy_users_id'=>$users_id,'status'=>2,'finish_time'=>$time],['market_id'=>$market_id]);
            $verify = new Verify();
            foreach ($id_list as $v)
            {
                $qrcode = $verify->getInfo(['id'=>$v],'verify_qrcode');
                if($qrcode['verify_qrcode'])
                {
                    unlink($_SERVER['DOCUMENT_ROOT'].$qrcode['verify_qrcode']);
                }
            }
            //改变通用券的拥有用户id,改变交易状态,有效期变无限,换核销码和二维码
            $verify->saveVerify(['users_id'=>$users_id,'trade_status'=>0,'expired_time'=>0,'verify_code'=>'','verify_qrcode'=>''],['id'=>['in',$id_list]]);
            $set = MarketSet::order("id desc")->cache("market_set",120)->find();
            $inc = round($price*(1-$set['rate']),2);
            //转让用户增加余额
            $users->UsersNumInc(['users_id'=>$info['users_id']],'balance_money',$inc);
            $user_info = $users->getInfo(['users_id'=>$info['users_id']],'username,balance_money,wx_openid');
            $arr = [];
            $arr['order_id'] = $market_id;
            $arr['users_id'] = $info['users_id'];
            $arr['bill_type'] = 'balance_money';
            $arr['bill_data'] = $inc;
            $arr['from_type'] = 'TradeMarket';
            $arr['type_name'] = $info['num'].'张通用券转让';
            $arr['remark'] = $info['num'].'张通用券转让成功获得余额';
            $arr['users_name'] = $user_info['username'];
            $arr['current_money'] = $user_info['balance_money'];
            $arr['create_time'] = $time;
            $bill = new UsersBill();
            //存余额明细
            $bill->addUserBill($arr);
            if($user_info['wx_openid'])
            {   
                $name = '通用券'.$info['num'].'张';
                $ctime = date('Y-m-d H:i:s',$info['create_time']);
                $url = 'http://tc.om/h5/page_my/couponsTrade';
                $rate = $set['rate']*100;
                if($rate !=0){
					$str = "通用券转让成功,扣除{$rate}%手续费,获得'.$inc.'元,已存入余额";
				}else{
					$str = "通用券转让成功,获得'.$inc.'元,已存入余额";
				}

                $this->fasong($user_info['wx_openid'],$name,$ctime,$price,$url,$str);
            }

            //购买用户减少余额
            $users->UsersNumDec(['users_id'=>$users_id],'balance_money',$price);
            //增加用户积分
            $users->UsersNumInc(['users_id'=>$info['users_id']],'point',round($price*$set['jifen'],5));

            $arr1 = [];
            //变负数
            $price *= -1;
            $arr1['order_id'] = $market_id;
            $arr1['users_id'] = $users_id;
            $arr1['bill_type'] = 'balance_money';
            $arr1['bill_data'] = $price;
            $arr1['from_type'] = 'TradeMarket';
            $arr1['type_name'] = '购买通用券'.$info['num'].'张';
            $arr1['remark'] = '购买'.$info['num'].'张通用券成功扣除余额';
            $arr1['users_name'] = $userinfo['username'];
            $arr1['current_money'] = $userinfo['balance_money'];
            $arr1['create_time'] = $time;
            //存余额明细
            $bill->addUserBill($arr1);
            
            Db::commit();
            return $this->success_req('购买成功');
        } catch (\Exception $e) {
            
            Db::rollback();
            return $this->error_req(100, $e->getMessage());
        }
    }
}
