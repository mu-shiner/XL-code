<?php
namespace app\api\controller;

use app\common\controller\Apibase;
use app\api\model\Users;
use app\api\model\DigitalExchange;
use app\api\model\Verify;
use app\api\model\UsersOrder;
use app\api\model\Goods;
use app\api\model\UsersBill;
use app\api\model\Pay;
use think\Db;

class DigitalMoney extends Apibase
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
     * 可兑换货币列表
     */
    public function getDigitalMoney()
    {   
		$model=new DigitalExchange();
     
        $list = $model->getMoneyList(['status'=>1]);
        foreach ($list as &$v)
        {
            $v['img_url'] = explode(',',$v['img_url']);
        }
		return  $this->success_req($list);
    }

    public function editMoneyAddress(){
        $users_id =  $this->users_id;
        $data['money_address'] = $this->params['money_address'];
        Users::where("users_id",$users_id)->update($data);
        return  $this->success_req('绑定成功');
    }
    //创建兑换
    public function createDigitalExchange()
    {   
       
        $this->write_log($this->params);
        $data['users_id'] = $this->users_id;
        $data['num'] = $this->params['num'];
        $data['money_id'] = $this->params['money_id'];
        Users::where("users_id",$this->users_id)->value("money_address");
        $data['money_address'] = Users::where("users_id",$this->users_id)->value("money_address");
        if(!$data['money_address']) return $this->error_req(100, '请先绑定钱包地址');
        $password = $this->params['password'];
        $users = new Users();
        $userinfo = $users->getInfo(['users_id'=>$data['users_id']],'password');
        if(md5($password) != $userinfo['password'])
        {
            return $this->error_req(100, '输入密码错误');
        }
        $model = new DigitalExchange();
        $money = $model->getMoneyInfo(['money_id'=>$data['money_id']],'money_id,money_name');
        if(!$money)
        {
            return $this->error_req(100, '兑换货币类型错误');
        }
        $data['money_name'] = $money['money_name'];
        $verify=new Verify();
        //有过期时间优先兑换
        $list1 = $verify->getList(['users_id'=>$data['users_id'],'is_verify'=>0,'trade_status'=>0,'expired_time'=>['neq',0]],'id,goods_id');
        $list2 = $verify->getList(['users_id'=>$data['users_id'],'is_verify'=>0,'trade_status'=>0,'expired_time'=>0],'id,goods_id');
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
            return $this->error_req(100, '兑换数量超出可用券数量');
        }
        $num = $data['num'];
        $id_list = [];
        for ($i = 0; $i < $num; $i++)
        {
             $id_list[] = $list[$i]['id'];
             $goods_id = $list[$i]['goods_id'];
        }
        
        $time = time();
        $data['create_time'] = $time;
        $data['verify_list'] = implode(',',$id_list);
        $data['goods_id'] = $goods_id;
        $res = $model->addData($data);
        if(!$res)
        {
            return $this->error_req(100, '系统错误');
        }
        $verify->saveVerify(['trade_status'=>1],['id'=>['in',$id_list]]);
		$users->UsersNumInc(["users_id"=>$this->users_id],"point_withdraw_amount",$num*1000);
        return  $this->success_req('创建成功');
    }
    
   
    
  
    //通用券兑换明细
    public function getExchangeBill()
    {
        
        $this->write_log($this->params);
        $users_id = $this->users_id;
        
        $where['users_id'] = $users_id;
        $model=new DigitalExchange();
        
        $list = $model->getList($where);
        return  $this->success_req($list);
    }
   
}
