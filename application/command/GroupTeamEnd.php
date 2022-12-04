<?php

namespace app\command;

use think\console\Command; //必须引入
use think\console\Input; //必须引入
use think\console\Output; //必须引入
use think\Db;
use think\Log;
use think\Cache;
use app\api\model\Pay;


/**
 * 拼团未达标人数自动结束
 * @date: 2022/5/7 10:30
 * @author: 
 * @version: 1.1
 */
class GroupTeamEnd extends Command  //必须继承
{

    protected function configure()
    {
        // 指令配置
        $this->setName('GroupTeamEnd');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        $data = [
                'action' => 'GroupTeamEnd',
                'datatime' => date('Y-m-s h:i:s', time())
            ];
        Db::name('jiaoben_log')->insert($data);
        $time = time();
        $beginTime = $time;
        $loop = true;
        do {
           
            $where = [
                    'is_delete' => 0,
                    'status' => 0,
                    'end_time' => ['elt',$time]
                ];
            //查出拼团结束的团
            $list = Db::name('group_team')->where($where)->select();
            
            Db::startTrans();
            try {
                if($list)
                {   
                    foreach ($list as $v)
                    {   
                        //活动状态变已结束
                        Db::name('group_team')->where(['team_id '=>$v['team_id']])->update(['status'=>2]);
                        
                        $join_list = Db::name('group_join')->where(['team_id '=>$v['team_id']])->select();
                        $join_arr = [];
                        $usersid_list = [];
                        
                        $groupinfo = Db::name('group')->where(['group_id'=>$v['group_id'],'is_delete'=>0])->find();
                        $url = 'http://tc.om/h5/page_my/myInvolved';
                        $name = $v['team_num'].'人团购';
                        $str = "很遗憾您参与的团购人数不足失败了,团购本金已退回余额";
                        foreach ($join_list as $item)
                        {   
                            $arr = [];
                            $userinfo = Db::name('users')->where(['users_id'=>$item['users_id']])->field('username,balance_money,wx_openid')->find();
                            $usersid_list[] = $item['users_id'];
                            $arr['order_id'] = $v['order_no'];
                            $arr['users_id'] = $item['users_id'];
                            $arr['bill_type'] = 'balance_money';
                            $arr['bill_data'] = $item['price'];
                            $arr['from_type'] = 'groupJoin';
                            $arr['type_name'] = '余额退回';
                            $arr['remark'] = '团购失败退回本金';
                            $arr['current_money'] = $userinfo['balance_money'];
                            $arr['users_name'] = $userinfo['username'];
                            $arr['create_time'] = $time;
                            $join_arr[] = $arr;
                            $price = $item['price'];
                            if($userinfo['wx_openid'])
                            {   
                                $ctime = date('Y-m-d H:i:s',$item['create_time']);
                                $this->fasong($userinfo['wx_openid'],$ctime,$name,$groupinfo['goods_name'],$item['order_no'],$url,$str);
                            }
                        }
                        
                        //活动参与列表状态变更
                        Db::name('group_join')->where(['team_id '=>$v['team_id']])->update(['join_status'=>3,'return_time'=>$time]);
                        //退回用户余额
                        Db::name('users')->whereIn('users_id',$usersid_list)->setInc('balance_money',$price);
                        //记录余额明细
                        Db::name('users_bill')->insertAll($join_arr);
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
    
    // {{first.DATA}}
    // 下单时间：{{keyword1.DATA}}
    // 团购名称：{{keyword2.DATA}}
    // 商品名称：{{keyword3.DATA}}
    // 订单编号：{{keyword4.DATA}}
    // 原因：{{keyword5.DATA}}
    // {{remark.DATA}}
    public function fasong($openid,$ctime,$name,$goodsname,$orderno,$url,$str)
    {   
        $pay = new Pay();
        $data = array(
            "touser"=>$openid,
            "template_id"=>$pay->GroupFailTemplateId,
            "url" => $url,
            "data" => array(
                "first" => array(
                    "value"=>"团购失败",
                ),
                "keyword1" => array(
                    "value"=>$ctime
                ),
                "keyword2" => array(
                    "value"=>$name
                ),
                "keyword3" => array(
                    "value"=>$goodsname
                ),
                "keyword4" => array(
                    "value"=>$orderno
                ),
                "remark" => array(
                    "value"=>$str
                ),
            )
        );
        $res = $pay->template($data);
        return $res;
    }
}