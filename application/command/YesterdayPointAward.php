<?php

namespace app\command;

use think\console\Command; //必须引入
use think\console\Input; //必须引入
use think\console\Output; //必须引入
use think\Db;
use think\Log;
use think\Cache;


/**
 * 统计用户昨日奖励
 * @date: 2022/8/10 10:30
 * @author: 
 * @version: 1.1
 */

//每天直接推荐新人有效参团满3人奖励5积分/人，满5人奖励8积分/人，满8人奖励10积分/人，满10人奖励15积分/人。奖励积分由每天晚上12点统计并发放至积分账户。
 
class YesterdayPointAward extends Command  //必须继承
{

    protected function configure()
    {
        // 指令配置
        $this->setName('YesterdayPointAward');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        
        $list = Db::name('users')->where(['is_delete'=>0,'reg_time'=>['between',"$beginYesterday,$endYesterday"]])->field('users_id,parent_id')->select();
        
        if($list)
        {   
            $arr = [];
            foreach ($list as $v)
            {
                $join_num = Db::name('group_join')->where(['users_id'=>$v['users_id'],'join_status'=>['lt',3]])->count();
                if($join_num > 0)
                {
                    $arr[$v['parent_id']] ++;
                }
            }
            var_dump($arr);
            if(!empty($arr))
            {   
                $time = time();
                $join_arr = [];
                foreach ($arr as $i=>$item)
                {
                    if($item < 3)
                    {
                        continue;
                    }
                    $money = 0;
                    if(3<=$item && $item<5)
                    {
                        $money = 5 * $item;
                    }
                    elseif (5<=$item && $item<8)
                    {
                        $money = 8 * $item;
                    }
                    elseif (8<=$item && $item<10)
                    {
                        $money = 10 * $item;
                    }
                    elseif (10<=$item)
                    {
                        $money = 15 * $item;
                    }
                    $data = [];
                    $userinfo = Db::name('users')->where(['users_id'=>$i])->field('username,point')->find();
                    $data['users_id'] = $i;
                    $data['bill_type'] = 'point';
                    $data['bill_data'] = $money;
                    $data['from_type'] = 'recommendAward';
                    $data['type_name'] = '推荐奖励';
                    $data['remark'] = '推荐新人达标奖励积分';
                    $data['current_money'] = $userinfo['point'];
                    $data['users_name'] = $userinfo['username'];
                    $data['create_time'] = $time;
                    $join_arr[] = $data;
                    //奖励积分
                    Db::name('users')->where(['users_id'=>$i])->setInc('point',$money);
                }
                
                if(!empty($join_arr))
                { 
                    //记录明细
                    Db::name('users_bill')->insertAll($join_arr);
                }
            }
        }
        
        
    }    
   
}