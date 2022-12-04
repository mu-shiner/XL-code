<?php

namespace app\command;

use think\console\Command; //必须引入
use think\console\Input; //必须引入
use think\console\Output; //必须引入
use think\Db;
use think\Log;
use think\Cache;


/**
 * 合伙人提现条件每周更新
 * @date: 2022/6/19 10:30
 * @author: 
 * @version: 1.1
 */
class PartnerWithdrawCondition extends Command  //必须继承
{

    protected function configure()
    {
        // 指令配置
        $this->setName('PartnerWithdrawCondition');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        $begin_time = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y"));
        $end_time = mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y"));
        // $begin_time = 1656259200;
        // $end_time = 1656864000;
        $list = Db::name('users')->where(['is_delete'=>0,'is_partner'=>1])->field('users_id,username,partner_money,direct_week_num,direct_partner_num,partner_whitelist')->select();
        $time = time();
        foreach ($list as $v)
        {   
            $data = [];
            //上周推荐人数
            $user_list = Db::name('users')->where(['is_delete'=>0,'parent_id'=>$v['users_id'],'reg_time'=>['between',"$begin_time,$end_time"]])->field('users_id')->select();
            $user_num = 0;
            foreach ($user_list as $item)
            {
                $join_num = Db::name('group_join')->where(['users_id'=>$item['users_id'],'join_status'=>['lt',3]])->count();
                if($join_num > 0)
                {
                    $user_num ++;
                }
            }
            //var_dump($user_num);
            //上周推荐成为合伙人数
            // $partner_num = Db::name('users')->where(['is_delete'=>0,'parent_id'=>$v['users_id'],'is_partner'=>1,'partner_time'=>['between',"$begin_time,$end_time"]])->count();
            $data['partner_withdraw_amount'] = 0;
            // if($partner_num > 0)
            // {
            //     $data['direct_partner_num'] = $v['direct_partner_num'] - $partner_num;
            //     if($data['direct_partner_num'] < 0)
            //     {
            //         $data['direct_partner_num'] = 0;
            //         $data['partner_withdraw_amount'] = round($v['partner_money'],2);
            //     }
            // }
            //上周总分红
            $money = Db::name('users_bill')->where(['users_id'=>$v['users_id'],'create_time'=>['between',"$begin_time,$end_time"],'bill_data'=>['gt',0],'bill_type' => 'partner_money'])->sum('bill_data');
            
            
            //每周至少新增推荐10人有效成团一次（参团失败不计入）方可提现。
            $data['direct_users_num'] = 10 - $user_num;
            //$data['partner_withdraw_amount'] = 0;
            if($data['direct_users_num'] <= 0 || $v['partner_whitelist'] == 1)
            {
                $data['direct_users_num'] = 0;
                $data['direct_week_num'] = 0;
              
                $data['partner_withdraw_amount'] = round($v['partner_money'],2);
                
            }
            else
            {   
                //未达标者当周分红清零
                $data['partner_money'] = 0;
                //$data['partner_money'] = round($v['partner_money'] - $money,2);
                
                
                // $data['direct_week_num'] = $v['direct_week_num'] + 1;
                // if($data['direct_week_num'] >= 4)
                // {   
                //     //连续4周未达标者取消市场合伙人分红资格（合伙人费用不退）。
                //     $data['is_partner'] = 0;
                // }
                $arr = [];
                //变负数
                $price = $v['partner_money'];
                $price *= -1;
                //$money *= -1;
                $arr['users_id'] = $v['users_id'];
                $arr['bill_type'] = 'partner_money';
                $arr['bill_data'] = $price;
                //$arr['bill_data'] = $money;
                $arr['from_type'] = 'NotCondition';
                $arr['type_name'] = '自动扣除';
                $arr['remark'] = '上周不满足条件自动扣除';
                $arr['users_name'] = $v['username'];
                $arr['current_money'] = $v['partner_money'];
                $arr['create_time'] = $time;
                //存余额明细
                Db::name('users_bill')->insert($arr);
            }
            
            Db::name('users')->where(['users_id'=>$v['users_id']])->update($data);
        }
        
    }    
   
}