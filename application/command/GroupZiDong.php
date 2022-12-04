<?php

namespace app\command;

use think\console\Command; //必须引入
use think\console\Input; //必须引入
use think\console\Output; //必须引入
use think\Db;
use think\Log;
use think\Cache;
use app\api\model\Pay;
use app\api\model\GroupOrder as OrderModel;
use app\api\model\Users;
use app\api\model\Group as GroupModel;
use app\api\model\UsersBill;
use app\api\model\GroupJoin;


/**
 * 拼团未达标人数自动结束
 * @date: 2022/5/7 10:30
 * @author: 
 * @version: 1.1
 */
class GroupZiDong extends Command  //必须继承
{

    protected function configure()
    {
        // 指令配置
        $this->setName('GroupZiDong');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {   
        $h = intval(date("H"));
	    if($h < 9)
	    {
	        exit;
	    }
        // $data = [
        //         'action' => 'GroupZiDong',
        //         'datatime' => date('Y-m-s h:i:s', time())
        //     ];
        // Db::name('jiaoben_log')->insert($data);
        $time = time();
        $beginTime = $time - 180;
        $loop = true;
        do {
           
            $where = [
                    'is_delete' => 0,
                    'status' => 0,
                    'create_time' => ['elt',$beginTime],
                    'join_num' => ['between',"3,9"],
                    'group_id' => 4
                ];
            //查出5分钟还未成功的3-9人的团
            $info = Db::name('group_team')->where($where)->find();
            
            Db::startTrans();
            try {
                if($info)
                {   
                    $join = new GroupJoin();
                    $list = $join->getList(['team_id'=>$info['team_id']]);
                    $joininfo = $list[0];
                    if($joininfo['create_time'] < $beginTime)
                    {
                        $arr = [1225,1226,1227,1228,1229,1230,1231];//机器人用户
                          
                        $num = 10 - $info['join_num'];
                        $arr_list = array_slice($arr,0,$num);
                        $user = new Users();
                        $order = new OrderModel();
                        $model = new GroupModel();
                        $bill = new UsersBill();
                        $time = time();
                        foreach ($arr_list as $v)
                        {   
                            $userinfo = $user->getInfo(['users_id'=>$v],'username,phone,balance_money');
                            $data = [];
                            $data['users_id'] = $v;
                            $data['order_price'] = 300;//订单价格
                            $data['group_id'] = 4;
                            $data['telephone'] = $userinfo['phone'];
                            $data['order_no'] = $this->createno('create_order_no','OR',$v);//订单编号
                    		$data['create_time'] = $time;
                    	    $data['goods_name'] = '门店消费抵扣券（全国通用）';
                    	    $data['goods_id'] = 2;
                    	    
                    	    $order->addGroupOrder($data);
                    	    
                            //处理用户参团
                            $res = $model->groupUsersJoin(4,$v,$data['order_no']);
                            if($res == -1)
                		    {
                		        return $this->error_req(101, '系统繁忙,请稍后');
                		    }
                            $arr = [];
                            $arr['order_id'] = $data['order_no'];
                            $arr['users_id'] = $v;
                            $arr['bill_type'] = 'balance_money';
                            $arr['bill_data'] = -300;
                            $arr['from_type'] = 'balancePay';
                            $arr['type_name'] = '余额支付';
                            $arr['remark'] = '余额支付拼团活动';
                            $arr['current_money'] = $userinfo['balance_money'];
                            $arr['users_name'] = $userinfo['username'];
                            $arr['create_time'] = $time;
                            
                            //存扣款明细
                            $bill->addUserBill($arr);
                            
                            //扣除用户余额
                            $user->UsersNumDec(['users_id'=>$v],'balance_money',300);
                        }
                    }
                    
                }
                Db::commit();
               
            } catch (\Exception $e) {
                Db::rollback();
                echo $e->getMessage();
                
            }

            if (time() - $beginTime >= 100) {//执行1分钟，重启一次
                exit;
            }

            $r = mt_rand(5, 8);
            sleep($r);

        } while ($loop);
    }
    
    /**
     * 创建不重复的编号(抵抗高并发)
     * @param $prefix 前缀
     * @param $unique 唯一值
     */
    public function createno($key, $prefix, $unique)
    {
    
        $time_str = date('YmdHi');
        $max_no = \think\Cache::get($key . $prefix . '_' . $unique . "_" . $time_str);
        if (!isset($max_no) || empty($max_no)) {
            $max_no = 1;
        } else {
            $max_no = $max_no + 1;
        }
        $no = $prefix . $time_str . $unique . sprintf("%04d", $max_no);
        \think\Cache::set($key . $prefix . '_' . $unique . "_" . $time_str, $max_no);
        return $no;
    }
}