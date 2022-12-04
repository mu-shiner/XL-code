<?php
namespace app\api\controller;

use app\common\controller\Apibase;
use app\api\model\GroupOrder as OrderModel;
use app\api\model\Users;
use app\api\model\UsersAddress;
use app\api\model\SeckillJoin;
use app\api\model\Seckill;
use app\api\model\UsersBill;
use app\api\model\UsersWithdraw;
use app\api\model\Statistics;
use think\Db;

class UsersInfo extends Apibase
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
     * 用户信息
     */
    public function index()
    {   
        $this->write_log($this->params);
        $data = [];
        $users_id = $this->users_id;
        
		$users=new Users();
		
		$info = $users->getInfo(['users_id'=>$users_id],'users_id,username,avatar,phone,status,parent_id,invitation_code,invitation_qr_code,balance_money,point,wx_openid,is_partner,partner_money,point_withdraw_amount,partner_withdraw_amount,money_address,coin');
		//生成邀请二维码
		if($info['invitation_code'] && !$info['invitation_qr_code'])
		{   
		    $req = $this->scerweima($users_id,$info['invitation_code']);
            
            $data['invitation_qr_code'] = $req;
            
            $users->saveData($data, ['users_id'=>$users_id]);
            $info['invitation_qr_code'] = $req;
		}
		if(!$info['users_id'])
		{
		    return $this->error_req(100, '用户id错误');
		}
		
		return  $this->success_req($info);
    }
    
   
    
    //获取用户收货地址
    public function getAddress()
    {
        
        $this->write_log($this->params);
        $users_id = $this->users_id;
        
        $model = new UsersAddress();
        $list = $model->getList(['users_id'=>$users_id]);
        
        return  $this->success_req($list);
    }
    
     //获取用户默认收货地址
    public function getDefaultAddress()
    {   
       
        $this->write_log($this->params);
        $users_id = $this->users_id;
       
        $model = new UsersAddress();
        $info = $model->getInfo(['users_id'=>$users_id,'is_default'=>1]);
        
        return  $this->success_req($info);
    }
    
    //保存地址
    public function saveAddress()
    {
       
        $this->write_log($this->params);
        $users_id = $this->users_id;
        
        $address_id = isset($this->params['address_id'])?$this->params['address_id']:0;
        $data['users_id'] = $users_id;
        $data['name'] = $this->params['name'];
        $data['telephone'] = $this->params['telephone'];
        $data['province_name'] = $this->params['province_name'];
        $data['city_name'] = $this->params['city_name'];
        $data['area_name'] = $this->params['area_name'];
        $data['address'] = $this->params['address'];
        $data['is_default'] = isset($this->params['is_default'])?$this->params['is_default']:0;
        
        $model = new UsersAddress();
        //判断用户默认地址
        if($data['is_default'] == 1)
        {
            $model->saveUsersAddress(['is_default'=>0],['users_id'=>$users_id]);
        }
        else
        {
            $info = $model->getInfo(['users_id'=>$users_id,'is_default'=>1]);
            if(!$info)
            {
                $data['is_default'] = 1;
            }
        }
        if($address_id)
        {   
            if($data['is_default'] == 0)
            {
                $info = $model->getInfo(['users_id'=>$users_id,'is_default'=>1]);
                if($info['address_id'] == $address_id)
                {
                    $data['is_default'] = 1;
                }
            }
            
            $model->saveUsersAddress($data,['address_id'=>$address_id]);
        }
        else
        {
            $data['create_time'] = time();
            $model->addUsersAddress($data);
            $infos = $model->getInfo($data);
            $address_id = $infos['address_id'];
        }
        $info['address_id'] = $address_id;
        return  $this->success_req($info);
    }
    //删除地址
    public function delAddress()
    {
        
        $this->write_log($this->params);
        $users_id = $this->users_id;
       
        $model = new UsersAddress();
        $address_id = $this->params['address_id'];
        $info = $model->getInfo(['address_id'=>$address_id]);
        if(!$info || $info['users_id'] != $users_id)
        {
            return $this->error_req(100, '地址id错误');
        }
        if($info['is_default'] == 1)
        {
            $infos = $model->getInfo(['address_id'=>['neq',$address_id],'users_id'=>$users_id]);
            if($infos)
            {   
                //更新默认地址
                $model->saveUsersAddress(['is_default'=>1],['address_id'=>$infos['address_id']]);
            }
        }
        //删除地址
        $model->saveUsersAddress(['is_delete'=>1],['address_id'=>$address_id]);
        
        return $this->success_req('删除成功');
    }
    
  
    //用户签收
    public function receipt()
    {
        
        $this->write_log($this->params);
        $users_id = $this->users_id;
        
        $order_id = $this->params['order_id'];
        $order = new OrderModel();
        $info = $order->getInfo(['order_id'=>$order_id],'users_id,order_status');
        if($info['users_id'] != $users_id)
        {
            return $this->error_req(100, '订单id出错');
        }
        if($info['order_status'] != 2)
        {
            return $this->error_req(100, '订单状态已变更');
        }
        
        $order->saveData(['order_status'=>3,'receipt_time'=>time()],['order_id'=>$order_id]);
        return $this->success_req("收货成功");
    }
    
    
    //用户删除订单
    public function usersOrderDel()
    {
       
        $this->write_log($this->params);
        $users_id = $this->users_id;
       
        $seckill_id = $this->params['seckill_id'];
        $model = new SeckillJoin();
        $info = $model->getInfo(['seckill_id'=>$seckill_id,'users_id'=>$users_id],'join_id');
        if(!$info)
        {
            return $this->error_req(100, '订单id出错');
        }
        //改变活动和订单状态
        $model->saveSeckillJoin(['is_delete'=>1],['join_id'=>$info['join_id']]);
       
        return $this->success_req("删除成功");
    }
    
    
    //积分，余额,合伙人明细
    public function getUsersBill()
    {
        
        $this->write_log($this->params);
        $users_id = $this->users_id;
       
        $type = $this->params['type'];
        switch ($type) {
            case '1':
                $where['bill_type'] = 'point';
                break;
            case '2':
                $where['bill_type'] = 'balance_money';
                break;
            case '3':
                $where['bill_type'] = 'partner_money';
                break;
            default:
                return $this->error_req(100, 'type错误');
                break;
        }
        $where['users_id'] = $users_id;
        
        $model = new UsersBill();
        $list = $model->getList($where);
        return  $this->success_req($list);
    }
    
    //按日期显示合伙人收益
    public function getPartnerBill()
    {   
        $this->write_log($this->params);
        
        $data_list = $this->params['range'];
        
        $where['date'] = ['between',$data_list];
        $where['users_id'] = $this->users_id;
       
        $model = new Statistics();
        $list = $model->getList($where);
        return  $this->success_req($list);
    }
    
    //生成带logo的二维码
    function scerweima($users_id,$invitation_code)
    {
        require_once 'phpqrcode.php';
        
        $pathname = "/upload/qrcode/users/".$users_id.'.png';
        $path = $_SERVER['DOCUMENT_ROOT'].$pathname;
        $dir_name=dirname($path);
        //目录不存在就创建
          if(!file_exists($dir_name))
          {
            $res = mkdir($dir_name,0755,true);
          }
        $url = 'https://www.blindboxjq.cn/#/subpkg/login-register/login-register?inviteCode='.$invitation_code;//邀请码内容
                      
        $errorCorrectionLevel = 'H';    //容错级别  
        $matrixPointSize = 6;           //生成图片大小  
        
        QRcode::png($url,$path , $errorCorrectionLevel, $matrixPointSize, 2);
        
        $logo = 'public/img/logo.png';     //准备好的logo图片   
        $QR = $path;            //已经生成的原始二维码图  
     
        if (file_exists($logo)) {   
            $QR = imagecreatefromstring(file_get_contents($QR));           //目标图象连接资源。
            $logo = imagecreatefromstring(file_get_contents($logo));       //源图象连接资源。
            
            $QR_width = imagesx($QR);            //二维码图片宽度   
            $QR_height = imagesy($QR);            //二维码图片高度   
            $logo_width = imagesx($logo);        //logo图片宽度   
            $logo_height = imagesy($logo);        //logo图片高度   
            $logo_qr_width = $QR_width / 4;       //组合之后logo的宽度(占二维码的1/5)
            $scale = $logo_width/$logo_qr_width;       //logo的宽度缩放比(本身宽度/组合后的宽度)
            $logo_qr_height = $logo_height/$scale;  //组合之后logo的高度
            $from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点
            
            //重新组合图片并调整大小
            /*
             *    imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
             */
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height); 
            imagepng($QR, $path); 
            return $pathname;
        } 
    }
    
    //推广统计
    public function statistics()
    {
        $users_id = $this->users_id;
        
        $model = new Users();
        //一级分销
        $first_list = $model->getList(['parent_id'=>$users_id],'users_id,username,avatar,reg_time');
        if($first_list)
        {   
            $usersid_list = [];
            foreach ($first_list as $v)
            {
                $usersid_list[] = $v['users_id'];
            }
            //二级分销
            $second_list = $model->getList(['parent_id'=>['in',$usersid_list]],'users_id,username,avatar,reg_time');
            $data['first_list'] = $first_list;
            $data['first_count'] = count($first_list);
            // $data['second_list'] = $second_list;
            // $data['second_count'] = count($second_list);
        }
        else
        {
            $data['first_list'] = [];
            $data['first_count'] = 0;
            // $data['second_list'] = [];
            // $data['second_count'] = 0;
        }
        
        return  $this->success_req($data);
    }
    
    
    //推广中心
    public function promote()
    {
        $users_id = $this->users_id;
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        $data = [];
        $bill = new UsersBill();
        $data['yesterday_income'] = $bill->getDataSum(['users_id'=>$users_id,'bill_type'=>'point','from_type'=>'PinTuanFenxiao','create_time'=>['between',"$beginYesterday,$endYesterday"]],'bill_data');
        $withdraw = new UsersWithdraw();
        $data['all_withdraw'] = $withdraw->getWithdrawSum(['users_id'=>$users_id,'type'=>1,'status'=>2],'withdraw_price');
        
        
        return  $this->success_req($data);
    }
    
    //推广收益排行榜
    public function Leaderboard()
    {   
        $begintime = 1660060800;//8月10号
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        $sql = "select `users_id`,sum(`bill_data`) as c from `tuan_users_bill` WHERE `bill_type` = 'point' AND `from_type` IN ('PinTuanFenxiao','recommendAward') AND `create_time`> $begintime AND `create_time`< $endYesterday AND users_id !=1 group by `users_id` ORDER BY c DESC limit 0,20"; 
        
        $Model = Db::name('users_bill');

        $result = $Model->query($sql);//查询
        $arr = [];
        $model = new Users;
        foreach ($result as $key => $value)
        {   
            $info = $model->getInfo(['users_id'=>$value['users_id']],'username,avatar');
            $value['users_name'] = $this->StrSubstrReplace($info['username']);
            $value['avatar'] = $info['avatar'];
            $arr[] = $value;
        }
        // $page = isset($this->params['page'])?$this->params['page']:1;
        // $size = isset($this->params['size'])?$this->params['size']:20;
       
        $list = [
                'count' => count($arr),
                'list' => $arr
            ];
        return  $this->success_req($list);
    }
    
    
    
    //上传并保存头像
    public function saveAvatar()
    {
        //$token = $this->checkToken();
        $this->write_log($this->params);
        $users_id = $this->users_id;
        // if(!$users_id)
        // {
        //     return $this->error_req(100, '用户未登录');
        // }
        $file = $_FILES["file"];
        
        $this->write_log($file);
        // 先判断有没有错
        
        if ($file["error"] == 0)
        {
            if($file['size'] > 1000000)//限制1M
            {
                return $this->error_req(100, "文件大小超出限制");
            }
             // 成功 
            
             // 判断传输的文件是否是图片，类型是否合适
            
             // 获取传输的文件类型
            
            $typeArr = explode("/", $file["type"]);
        
         
           // 如果是图片类型
        
            $imgType = array('jpg', 'png', 'jpeg');
        
            if(!in_array($typeArr[1], $imgType))
            { 
               return $this->error_req(100, "非法文件类型");
            }
            // 图片格式是数组中的一个
        
           // 类型检查无误，保存到文件夹内
        
           // 给图片定一个新名字 (使用时间戳，防止重复)
           $time_str = date('YmdHis');
           $urlname = "/upload/avatar/".$time_str.$users_id.".".$typeArr[1];
           $imgname = $_SERVER['DOCUMENT_ROOT'].$urlname;
           
           
          $dir_name=dirname($imgname);
          //目录不存在就创建
          if(!file_exists($dir_name))
          {
            $res = mkdir($dir_name,0755,true);
          }
        
           // 将上传的文件写入到文件夹中
        
           // 参数1: 图片在服务器缓存的地址
        
           // 参数2: 图片的目的地址（最终保存的位置）
        
           // 最终会有一个布尔返回值
        
           $bol = move_uploaded_file($file["tmp_name"], $imgname);
        
           if($bol)
           {
                $users = new Users();
                $info = $users->getInfo(['users_id'=>$users_id],'avatar');
                //删除原来头像图片文件
                if($info['avatar'] && $info['avatar'] != $urlname)
                {
                    unlink($_SERVER['DOCUMENT_ROOT'].$info['avatar']);
                }
                $users->saveData(['avatar'=>$urlname],['users_id'=>$users_id]);
                return $this->success_req("保存成功");
        
           }
           else
           {
                return $this->error_req(100, "保存失败");
           }
        } else {
        
            // 失败
            return $this->error_req(100, $file["error"]);
         
        
        }
    }
    
    //合伙人配置
    public function getParConfig()
    {   
        $model = new Users();
        $info = $model->getPartnerConfig(['id'=>1],'first_num,price');
        $users_id = $this->users_id;
        $user = $model->getInfo(['users_id'=>$users_id],'is_partner');
        if($user['is_partner'] == 1)
        {
            $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
            $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
            $bill = new UsersBill();
            $where = [
                    'users_id' => $users_id,
                    'bill_type' => 'partner_money',
                    'from_type' => 'partnerDividend'
                ];
            
            $info['all_income'] = $bill->getDataSum($where,'bill_data');
            $where['create_time'] = ['between',"$beginYesterday,$endYesterday"];
            $info['yesterday_income'] = $bill->getDataSum($where,'bill_data');
            
        }
        return  $this->success_req($info);
    }
     public function pointToBalance()
    {
        $users = Users::where("users_id",$this->users_id)->find();
        $total = input("post.total");
        if($users->point < $total){
            $this->error("要转换的积分不够");
        }
        $users->point -= $total;
        $users->balance_money +=$total;
        $users->save();
        return  $this->success_req("转化成功");
    }
    
    
    //成为体验账号设置成白名单（Thdni-202262-添加）
    // public function setWhiteList()
    // {
    //     $this->write_log($this->params);
    //     $users_id = $this->users_id;
    //     $time=time();
    //     if($time>1654176600){
    //         return $this->error_req(100, "已过时间！");
    //     }
    //     $code=$this->params['code'];
    //     if($code!=="202262jx88"){
    //         return $this->error_req(100, "白名单编号错误！");
    //     }
    //     $data['is_virtual'] = 1;
    //     $model = new Users();
    //     $info = $model->saveData($data,['users_id'=>$users_id]);
    //     return  $this->success_req($info);
        
    // }
  
}
