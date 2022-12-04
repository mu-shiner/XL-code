<?php


namespace app\Api\controller;

use app\admin\model\Banner;
use app\common\controller\Apibase;
use think\Cache;
use app\api\model\Users;
use app\api\model\Group;
use app\api\model\UsersBill;
use app\api\model\GroupJoin;
use think\Db;
use think\Model;
use app\api\model\RedisLock;
use app\api\model\TradeMarket;
use app\api\model\Shop;
use app\shop\model\Admin;
use app\admin\model\Version;
use app\api\model\Statistics;

class Index extends  Apibase
{
    public function _initialize(){
        parent::_initialize();

    }
    //首页轮播图
    public function getBanner()
    {
        $list =Banner::where("status",1)->column('img');
        return  $this->success_req($list);
    }
    
    public function getAll()
    {   
        
	    $time = time();
        $beginTime = $time - 180;
       
           
        $where = [
                'is_delete' => 0,
                'status' => 0,
                'create_time' => ['elt',$beginTime],
                'group_id' => 4
            ];
            //查出5分钟还未成功的3-9人的团
            $info = Db::name('group_team')->where($where)->find();
            echo('<pre>');
            var_dump($info);
            $join = new GroupJoin();
                    $list = $join->getList(['team_id'=>$info['team_id']]);
                    var_dump($list);
                    $joininfo = $list[0];
                    var_dump($joininfo);echo('</pre>');die;
        //Db::name('users')->where(['reg_time'=>['lt',1655654400]])->update(['withdraw_type'=>1]);die;
        $begin_time = 1656259200;
        $end_time = 1656864000;
        $users_id = 35;
        $user_list = Db::name('users')->where(['is_delete'=>0,'parent_id'=>$users_id,'reg_time'=>['between',"$begin_time,$end_time"]])->field('users_id,username')->select();
        foreach ($user_list as &$item)
        {
            $join_num = Db::name('group_join')->where(['users_id'=>$item['users_id'],'join_status'=>['lt',3]])->count();
            $item['num'] = $join_num;
        }
        echo('<pre>');
        var_dump($user_list);echo('</pre>');die;
    }
    
    public function add()
    {   
        $list = Db::name('users')->where(['is_delete'=>0,'is_partner'=>1,'users_id'=>['in',[4,8]]])->field('users_id,username,partner_money')->select();
        var_dump($list);die;
        $id = 4;
        $info = Cache::store('default')->get('group_'.$id);
        if(!$info)
        {
            $groupinfo = Db::name('group')->where(['group_id'=>$id,'is_delete'=>0])->find();
            Cache::store('default')->set('group_'.$id,$groupinfo);
        }
        var_dump($info);die;
        
        
        
        $bill = new UsersBill();
        $where = [
                'bill_data' => ['gt',0],
                'bill_type' => ['in',['balance_money','point','partner_money']],
                'create_time' => ['between','1656259200,1656345600']
            ];
        
        $list = $bill->getBillList($where);
        // echo("<pre>");
        // var_dump($list);echo("</pre>");die;
        $model = new Statistics();
        foreach ($list as $v)
        {
            $date = date('Ymd',$v['create_time']);
            $info = $model->getInfo(['date'=>$date,'users_id'=>$v['users_id']]);
            
            if($info)
            {
                $model->saveNumInc(['date'=>$date,'users_id'=>$v['users_id']],$v['bill_type'],$v['bill_data']);
            }
            else
            {   
                $data = ['date'=>$date,'users_id'=>$v['users_id'],$v['bill_type']=>$v['bill_data']];
                $model->insert($data);
            }
        }
        echo(1111);
    }
    
    // public function getAdd()
    // {
    //     $model = new Group();
    //     $team_id = 4194;
    //     $model->groupUsersJoin1($team_id);
    //     echo(111);
    // }
    
    public function getAppVersion()
    {
        $model = new Version();
        $list = $model->getList(['status'=>1]);
        $info = $list[0];
        //$info['apk_url'] = 'http://tc.om'.$info['apk_url'];
        return  $this->success_req($info);
    }
    
    //超过多人入团补救
    public function get()
    {   
        //$arr = [460,283,259,508,373,559,289,439,501,462,451,207,231,509,684,687,457,295,431,577,432,200,385,433,203,205,247,202,375,379,343,285,413,380,204,210,434,216,382,402,292,446,429,456,301,389,620,612,636,410,617,345,614,393,274,747,299,488,666,342];
        //$arr = [202,343,559,283,204,289,432,207,259,799,413,431];
        //$arr = [283,295,431,439,501,375,379,373,380,285,259,207];
        //$arr = [383,511,281,388,40,685,372];
        //$arr = [171,305,471];
        //$arr = [460,684,441,188,323,250,662,539,260,415,347,346,351,360,448,428,481,436];
        $arr = [632,692,445,129,536];
        $users = new Users();
       
        $info = $users->saveData(['withdraw_type'=>0],['users_id'=>['in',$arr]]);
        var_dump($info);die;
        
        $model = new GroupJoin();
        $team_id = 3281;
        $list = $model->getList(['team_id'=>$team_id]);
        $redis = Cache::getHandler();
            
            
        foreach ($list as $v)
        {
            $data = [
                    'users_id' => $v['users_id'],
                    'group_id' => $v['group_id'],
                    'order_no' => $v['order_no']
                ];
            //redis存用户拼团列表
            $redis->hMset('user_'.$v['group_id'].'_'.$v['users_id'], $data);
            //redis存用户拼团id(方便定时任务取redis出价信息)
            $redis->rPush('group_join_list', 'user_'.$v['group_id'].'_'.$v['users_id']);
        }
        $model->savegroupJoin(['is_delete'=>1],['team_id'=>$team_id]);
    }
    
    //手动增加余额
    public function get2()
    {   
        $model = new Users();
        $user_id = '1243';
        $user = $model->getInfo(['users_id'=>$user_id]);
        // if($user['is_virtual'] != 1)
        // {
        //     echo('失败');die;
        // }
        $money = 1500;
        $model->UsersNumInc(['users_id'=>$user_id],'balance_money',$money);
        $arr = [];
        $arr['order_id'] = '';
        $arr['users_id'] = $user_id;
        $arr['bill_type'] = 'balance_money';
        $arr['bill_data'] = $money;
        $arr['from_type'] = 'addManully';
        $arr['type_name'] = '手动增加余额';
        $arr['remark'] = '手动增加余额';
        $arr['current_money'] = $user['balance_money'];
        $arr['users_name'] = $user['username'];
        $arr['create_time'] = time();
        $bill = new UsersBill();
        $bill->addUserBill($arr);die;
    }
    
    //统计市场不同价格的数量
    public function get3()
    {
        $sql = "select `unit_price`,count(*) as c from `tuan_trade_market` where `status`=1  group by `unit_price` having c>1";
        
        $Model = Db::name('trade_market');

        $result = $Model->query($sql);//查询
        $arr = [];
        foreach ($result as $key => $value){
                    $arr[] = $value;

        }
        echo("<pre>");
        var_dump($arr);echo("</pre>");die;
    }
    
    
    public function get1()
    {   
        
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        $model = new TradeMarket();
        $info = $model->getDataSum(['status'=>2,'buy_users_id'=>31,'finish_time'=>['between',"$beginYesterday,$endYesterday"]],'num');
        var_dump($info);die;
        $redis = Cache::getHandler();
        //$list = $redis->handler()->lRange('group_join_list', 0, -1);
        $info = $redis->handler()->hGetAll('user_4_6');
        var_dump($info);die;
        // $model = new UsersBill();
        // $list = $model->getdata();
      
        $sql = "select `order_id`,count(*) as c from `tuan_users_bill` group by `order_id` having c=1";
        
        $Model = Db::name('users_bill');

        $result = $Model->query($sql);//查询
        $arr = [];
        foreach ($result as $key => $value){
            if(strstr($value['order_id'],'OR2022'))
            {   
                $info = Db::name('group_join')->where(['order_no'=>$value['order_id']])->find();
                if(!$info)
                {
                    $arr[] = $value;
                }
                
            }

        }
        var_dump($arr);die;
        
        // $model = new Users();
        // $arr = [];
        // $arr['order_id'] = 'OR2022061511372120001';
        // $arr['users_id'] = '183';
        // $arr['bill_type'] = 'balance_money';
        // $arr['bill_data'] = 1000;
        // $arr['from_type'] = 'partnerPay';
        // $arr['type_name'] = '合伙人奖励';
        // $arr['remark'] = '推荐开通合伙人奖励';
        // $arr['users_name'] = 'gsgb3';
        // $arr['current_money'] = 1294.50;
        // $arr['create_time'] = time();
        // $bill = new UsersBill();
        // //存明细
        // $bill->addUserBill($arr);
        // $model->UsersNumInc(['users_id'=>183],'balance_money',1000);

    }
    
    
    public function index()
    {    
        
    }
    
    public function get_reward($proArr = array())
    {  
        $proSum = array_sum($proArr);
        foreach ($proArr as $key => $productCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $productCur) {
                $result = $productCur;
                break;
            } else {
                $proSum -= $productCur;
            }
        }
        unset($proArr);
        return $result;
    }
    
    
}