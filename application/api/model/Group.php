<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\api\model;
use think\Model;
use think\Db;
use think\Cache;

class Group extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        
        $list = Db::name('group')->where($where)->field($field)->select();
        //echo Db::name('group')->getLastSql();
        
        return $list;
        
    }
    
    public function getInfo($where, $field='*')
    {   
        $where['is_delete'] = 0;
        $info = Db::name('group')->where($where)->field($field)->find();
        return $info;
    }
    
    public function savegroup($data, $where)
    {
        $res = Db::name('group')->where($where)->update($data);
        return $res;
    }
    
    public function groupNumInc($where, $field, $num=1)
    {
        $res = Db::name('group')->where($where)->setInc($field,$num);
        return $res;
    }
    
    
    /**
     * 用户支付后加入拼团活动
     * @param int $group_id 活动id
     * @param int $users_id 用户id
     * @
     */
    public function groupUsersJoin($group_id,$users_id,$order_no)
    {   
        $lock = new RedisLock();
        //获取锁（5秒过期）
        $lockinfo = $lock->lock('lock_sec_'.$group_id,5);
        if(!$lockinfo)
        {
            return -1;
        }
        //查询是否开团
        //$team = Db::name('group_team')->where(['group_id'=>$group_id,'status'=>0,'is_delete'=>0])->field('team_id,team_num,join_num,get_num')->find();
        $team = Db::name('group_team')->where(['group_id'=>$group_id,'status'=>0,'is_delete'=>0,'join_num'=>['lt',10]])->field('team_id,team_num,join_num,get_num')->find();
        
        $groupinfo = Db::name('group')->where(['group_id'=>$group_id,'is_delete'=>0])->find();
        $time = time();
        $is_end = 0;
        if($team)
        {   //入团人数加1
            Db::name('group_team')->where('team_id',$team['team_id'])->setInc('join_num',1);
            $team_id = $team['team_id'];
            //成团
            if($team['join_num'] + 1 == $team['team_num'])
            {
                $is_end = 1;
            }
        }
        else
        {   
            $team_arr = [
                    'group_id' => $group_id,
                    'team_num' => $groupinfo['team_num'],
                    'get_num' => $groupinfo['get_num'],
                    'join_num' => 1,
                    'create_time' => $time,
                    'end_time' => $time + 24*3600,
                    'goods_class' => $groupinfo['goods_class'],
                ];
            //创建团
            Db::name('group_team')->insert($team_arr);
            $teaminfo = Db::name('group_team')->where($team_arr)->field('team_id,team_num,join_num')->find();
            $team_id = $teaminfo['team_id'];
        }
        $join_arr = [
                'team_id' => $team_id,
                'users_id' => $users_id,
                'group_id' => $group_id,
                'price' => $groupinfo['price'],
                'create_time' => $time,
                'order_no' => $order_no
            ];
        //创建参团信息
        Db::name('group_join')->insert($join_arr);
        
        //改变订单状态     
        Db::name('group_order')->where(['order_no'=>$order_no])->update(['status'=>1,'pay_time'=>$time]);
        //已成团
        if($is_end == 1)
        {   
            //改变团状态
            Db::name('group_team')->where(['team_id'=>$team['team_id']])->update(['status'=>1,'success_time'=>$time]);
            $join_list = Db::name('group_join')->where(['team_id '=>$team['team_id']])->select();
            //随机打乱数组顺序
            shuffle($join_list);
            $get_num = $team['get_num'];//中奖人数
            $get_idlist = [];//中产品人id
            $no_idlist = [];//未中产品人id
            //拼团奖励配置
            $group_config = Db::name('group_config')->where(['type'=>1])->find();
            $goods = Db::name('goods')->where(['id'=>$groupinfo['goods_id']])->field('check_type')->find();
            $all_arr = [];
            $verify = [];
            $date_time = date('Ymd', $time);
            //var_dump($join_list);die;
            $url = 'http://tc.om/h5/page_my/myInvolved';
            $parent_id_list = [];
            foreach ($join_list as $v)
            {   
                $user = Db::name('users')->where(['users_id'=>$v['users_id']])->field('users_id,username,parent_id,point,balance_money,wx_openid,is_virtual')->find();
                $parent_id_list[] = $user['parent_id']."";
                //if($get_num <= 0 || $user['is_virtual'] == 1)
                if($get_num <= 0)
                {//中红包人员   
                    $no_idlist[] = $v['users_id'];
                    $arr = [];
                    $arr['order_id'] = $v['order_no'];
                    $arr['users_id'] = $v['users_id'];
                    $arr['bill_type'] = 'balance_money';
                    $arr['bill_data'] = $v['price'];
                    $arr['from_type'] = 'groupJoin';
                    $arr['type_name'] = '团购退本金';
                    $arr['remark'] = '团购成功中红包退回本金';
                    $arr['current_money'] = $user['balance_money'];
                    $arr['users_name'] = $user['username'];
                    $arr['create_time'] = $time;
                    $all_arr[] = $arr;
                    
                    $arr1 = [];
                    
                    // $arr1['order_id'] = $v['order_no'];
                    // $arr1['users_id'] = $v['users_id'];
                    // $arr1['bill_type'] = 'balance_money';
                    // $arr1['bill_data'] = $v['price']*$group_config['rate']/100;
                    // $arr1['from_type'] = 'groupJoin';
                    // $arr1['type_name'] = '团购红包';
                    // $arr1['remark'] = '团购成功中红包';
                    // $arr1['current_money'] = $user['balance_money'] + $v['price'];
                    // $arr1['users_name'] = $user['username'];
                    // $arr1['create_time'] = $time;
                    
                    $arr1['order_id'] = $v['order_no'];
                    $arr1['users_id'] = $v['users_id'];
                    $arr1['bill_type'] = 'point';
                    $arr1['bill_data'] = $v['price']*$group_config['rate']/100;
                    $arr1['from_type'] = 'groupJoin';
                    $arr1['type_name'] = '团购红包';
                    $arr1['remark'] = '团购成功中红包';
                    $arr1['current_money'] = $user['point'];
                    $arr1['users_name'] = $user['username'];
                    $arr1['create_time'] = $time;
                    
                    $all_arr[] = $arr1;
                    //增加金额
                    //$num_money = $v['price'] + $arr1['bill_data'];
                    $num_money = $v['price'];
                    $num_point = $arr1['bill_data'];
                    Db::name('group_order')->where(['order_no'=>$v['order_no']])->update(['order_status'=>3]);
                    if($user['wx_openid'])
                    {   
                        $str = "恭喜您获得".$arr1['bill_data']."元红包,已放入积分,参团本金已退回余额";
                        $this->fasong($user['wx_openid'],$v['order_no'],$groupinfo['goods_name'],$url,$str);
                    }
                }
                else
                {   //中产品
                    $get_idlist[] = $v['users_id'];
                    $get_num --;
                    if($groupinfo['goods_class'] == 1)
                    {//实物
                        //订单状态改需要发货
                        Db::name('group_order')->where(['order_no'=>$v['order_no']])->update(['ship_type'=>1,'order_status'=>1]);
                    }
                    else
                    {//虚拟产品
                        
                        $verify_arr = [
                                'verify_code' => '',//核销编码,
                                'create_time' => $time,
                                'check_type' => $goods['check_type'],
                                'order_no' => $v['order_no'],
                                'users_id' => $v['users_id'],
                                'expired_time' => $time + 30*24*60*60,//有效期1个月
                                'goods_id' => $groupinfo['goods_id']
                            ];
                        $verify[] = $verify_arr;
                        Db::name('group_order')->where(['order_no'=>$v['order_no']])->update(['ship_type'=>2,'order_status'=>3]);
                        //中虚拟物品，用户抽奖次数+1
                        $lottery_num = Db::name('users_lottery_num')->where(['date_time'=>$date_time,'users_id'=>$v['users_id']])->find();
                        if($lottery_num)
                        {   
                            $num_arr = [
                                    'get_num' => $lottery_num['get_num'] + 1,
                                    'surplus_num' => $lottery_num['surplus_num'] + 1,
                                ];
                            Db::name('users_lottery_num')->where(['id'=>$lottery_num['id']])->update($num_arr);
                            //Db::name('users_lottery_num')->where(['id'=>$lottery_num['id']])->update(['get_num'=>'get_num'+1,'surplus_num'=>'surplus_num'+1]);
                        }
                        else
                        {
                            $num_arr = [
                                    'date_time' => $date_time,
                                    'users_id' => $v['users_id'],
                                    'get_num' => 1,
                                    'surplus_num' => 1
                                ];
                            Db::name('users_lottery_num')->insert($num_arr);
                        }
                        if($user['wx_openid'])
                        {   
                            $str = "恭喜您获得".$groupinfo['goods_name'];
                            $this->fasong($user['wx_openid'],$v['order_no'],$groupinfo['goods_name'],$url,$str);
                        }
                    }
                }
                //分佣
                $this->UsersDistribution($v['users_id'],$v['price'],1,$team['team_id']);
                
            }

            //合伙人增加体现次数
            $paterner_parent = Users::where("users_id","in",$parent_id_list)->where("is_partner",1)->field("users_id,partner_sale_nums,partner_withdraw_nums")->select();
            $countList = array_count_values($parent_id_list);
            foreach ($paterner_parent as $v){
                $oldNums = $v->partner_sale_nums;
                $v->partner_sale_nums += $countList[$v['users_id']];
                if($oldNums < 500 &&  $v->partner_sale_nums >=500 ) $v->partner_withdraw_nums += 1;
                if($oldNums < 1200 &&  $v->partner_sale_nums >=1200 ) $v->partner_withdraw_nums += 1;
                if($oldNums < 3000 &&  $v->partner_sale_nums >=3000 ) $v->partner_withdraw_nums += 2;
                $v->save();
            }
            //商品销量增加
            Db::name('goods')->where(['id'=>$groupinfo['goods_id']])->setInc('sales',$team['get_num']);
            //合伙人分红
            if(!empty($groupinfo['dividend_price']))
            {
                $this->UsersPartnerDividend($groupinfo['dividend_price'],$group_id);
            }
            
            //活动参与列表状态变更
            Db::name('group_join')->where(['team_id '=>$team['team_id']])->whereIn('users_id',$no_idlist)->update(['join_status'=>2,'return_time'=>$time]);
            Db::name('group_join')->where(['team_id '=>$team['team_id']])->whereIn('users_id',$get_idlist)->update(['join_status'=>1,'return_time'=>$time]);
            
            //用户余额增加
            Db::name('users')->whereIn('users_id',$no_idlist)->setInc('balance_money',$num_money);
            
            //用户积分增加
            Db::name('users')->whereIn('users_id',$no_idlist)->setInc('point',$num_point);
            
            //记录余额明细
            Db::name('users_bill')->insertAll($all_arr);
            //虚拟产品添加核销码
            if(count($verify) > 0)
            {
                Db::name('verify')->insertAll($verify);
            }
        }
        $lock->unlock('lock_sec_'.$group_id);
        return $is_end;
    }
    
     /**
     * 分佣奖励
     * @param int $usersid 用户id
     * @param double $price 订单价格
     * @param int $type 配置类型1充会员2出价手续费
     * @param int $order_id 订单id
     * @
     */
    public function UsersDistribution($usersid, $price, $type, $order_id='')
    {   
        //配置比例
        $configinfo = Db::name('users_config')->where(['config_type'=>$type])->find();
        $userinfo = Db::name('users')->where(['users_id'=>$usersid])->field('parent_id')->find();
        if(!$userinfo['parent_id'])
        {
            return 1;
        }
        $this->UsersFenxiao($userinfo['parent_id'], 1, $price, $configinfo, $order_id);
    }
    
    
    /**
     * 分佣奖励
     * @param int $usersid 用户id
     * @param int $grade 用户处在分佣第几级
     * @param double $price 订单价格
     * @param array $configinfo 配置信息
     * @param int $order_id 订单id
     * @
     */
    public function UsersFenxiao($usersid, $grade, $price, $configinfo, $order_id='')
    {
        $userinfo = Db::name('users')->where(['users_id'=>$usersid])->field('parent_id,username,point')->find();
        if(!$userinfo) return false;
        $data = [];
        switch ($grade) {
            case 1:
                $rate = $configinfo['rate_one'];
                $data['remark'] = '一级分销奖励';
                break;
            case 2:
                $rate = $configinfo['rate_two'];
                $data['remark'] = '二级分销奖励';
                break;
            case 3:
                $rate = $configinfo['rate_three'];
                $data['remark'] = '三级分销奖励';
                break;
            default:
                return 0;
                break;
        }
        //用户积分增加
        $money = round($price*$rate/100,5);
        Db::name('users')->where(['users_id'=>$usersid])->setInc('point',$money);
        //记录账户明细
        $data['order_id'] = $order_id;
        $data['users_id'] = $usersid;
        $data['bill_type'] = 'point';//账户类型  积分
        $data['bill_data'] = $money;
        $data['users_name'] = $userinfo['username'];
        $data['current_money'] = $userinfo['point'];
        $data['create_time'] = time();
        $data['from_type'] = 'PinTuanFenxiao';//会员费分销
        $data['type_name'] = '团购成功分佣';
        
        
        Db::name('users_bill')->insert($data);
        if($userinfo['parent_id'] && $grade < $configinfo['level'])
        {   
            $grade++;
            $this->UsersFenxiao($userinfo['parent_id'], $grade, $price, $configinfo, $order_id);
        }
    }
    
    /**
     * 合伙人分红
     * @param double $price 分红金额
     * @param int $group_id 拼团Id
     * @
     */
    public function UsersPartnerDividend($price,$group_id)
    {
        $list = Db::name('users')->where(['is_partner '=>1,'status'=>1])->field('users_id,username,partner_money')->select();
        if(count($list) == 0)
        {
            return;
        }
        //每个人分的金额
        $money = round($price/count($list),4);
        $time = time();
        $usersid_list = [];
        $join_arr = [];
        foreach ($list as $v)
        {   
            $arr = [];
            $usersid_list[] = $v['users_id'];
            $arr['order_id'] = $group_id;
            $arr['users_id'] = $v['users_id'];
            $arr['bill_type'] = 'partner_money';
            $arr['bill_data'] = $money;
            $arr['from_type'] = 'partnerDividend';
            $arr['type_name'] = '合伙人分红';
            $arr['remark'] = '团购成功合伙人分红';
            $arr['current_money'] = $v['partner_money'];
            $arr['users_name'] = $v['username'];
            $arr['create_time'] = $time;
            $join_arr[] = $arr;
        }
        //加上用户余额
        Db::name('users')->whereIn('users_id',$usersid_list)->setInc('partner_money',$money);
        //记录余额明细
        Db::name('users_bill')->insertAll($join_arr);
    }
    //推送公众号消息
    // {{first.DATA}}
    // 订单编号：{{keyword1.DATA}}
    // 团购商品：{{keyword2.DATA}}
    // {{remark.DATA}}
    public function fasong($openid,$order_no,$goodsname,$url,$str)
    {   
        $pay = new Pay();
        $data = array(
            "touser"=>$openid,
            "template_id"=>$pay->GroupSuccessTemplateId,
            "url" => $url,
            "data" => array(
                "first" => array(
                    "value"=>"团购成功",
                ),
                "keyword1" => array(
                    "value"=>$order_no
                ),
                "keyword2" => array(
                    "value"=>$goodsname
                ),
                "remark" => array(
                    "value"=>$str
                ),
            )
        );
        $res = $pay->template($data);
        return $res;
    }
    
    
    public function groupUsersJoin1($team_id)
    {   
        
        //查询是否开团
        //$team = Db::name('group_team')->where(['group_id'=>$group_id,'status'=>0,'is_delete'=>0])->field('team_id,team_num,join_num,get_num')->find();
        $team = Db::name('group_team')->where(['team_id'=>$team_id])->field('team_id,group_id,team_num,join_num,get_num')->find();
        $group_id = $team['group_id'];
        $groupinfo = Db::name('group')->where(['group_id'=>$group_id,'is_delete'=>0])->find();
        $time = time();
         
        //改变团状态
        Db::name('group_team')->where(['team_id'=>$team['team_id']])->update(['status'=>1,'success_time'=>$time]);
        $join_list = Db::name('group_join')->where(['team_id '=>$team['team_id']])->select();
        //随机打乱数组顺序
        shuffle($join_list);
        $get_num = $team['get_num'];//中奖人数
        $get_idlist = [];//中产品人id
        $no_idlist = [];//未中产品人id
        //拼团奖励配置
        $group_config = Db::name('group_config')->where(['type'=>1])->find();
        $goods = Db::name('goods')->where(['id'=>$groupinfo['goods_id']])->field('check_type')->find();
        $all_arr = [];
        $verify = [];
        $date_time = date('Ymd', $time);
        //var_dump($join_list);die;
        $url = 'http://tc.om/h5/page_my/myInvolved';
        foreach ($join_list as $v)
        {   
            $user = Db::name('users')->where(['users_id'=>$v['users_id']])->field('users_id,username,parent_id,point,balance_money,wx_openid,is_virtual')->find();
            
            if($get_num <= 0 || $user['is_virtual'] == 1)
            {//中红包人员   
                $no_idlist[] = $v['users_id'];
                $arr = [];
                $arr['order_id'] = $v['order_no'];
                $arr['users_id'] = $v['users_id'];
                $arr['bill_type'] = 'balance_money';
                $arr['bill_data'] = $v['price'];
                $arr['from_type'] = 'groupJoin';
                $arr['type_name'] = '团购退本金';
                $arr['remark'] = '团购成功中红包退回本金';
                $arr['current_money'] = $user['balance_money'];
                $arr['users_name'] = $user['username'];
                $arr['create_time'] = $time;
                $all_arr[] = $arr;
                
                $arr1 = [];
                
                // $arr1['order_id'] = $v['order_no'];
                // $arr1['users_id'] = $v['users_id'];
                // $arr1['bill_type'] = 'balance_money';
                // $arr1['bill_data'] = $v['price']*$group_config['rate']/100;
                // $arr1['from_type'] = 'groupJoin';
                // $arr1['type_name'] = '团购红包';
                // $arr1['remark'] = '团购成功中红包';
                // $arr1['current_money'] = $user['balance_money'] + $v['price'];
                // $arr1['users_name'] = $user['username'];
                // $arr1['create_time'] = $time;
                
                $arr1['order_id'] = $v['order_no'];
                $arr1['users_id'] = $v['users_id'];
                $arr1['bill_type'] = 'point';
                $arr1['bill_data'] = $v['price']*$group_config['rate']/100;
                $arr1['from_type'] = 'groupJoin';
                $arr1['type_name'] = '团购红包';
                $arr1['remark'] = '团购成功中红包';
                $arr1['current_money'] = $user['point'];
                $arr1['users_name'] = $user['username'];
                $arr1['create_time'] = $time;
                
                $all_arr[] = $arr1;
                //增加金额
                //$num_money = $v['price'] + $arr1['bill_data'];
                $num_money = $v['price'];
                $num_point = $arr1['bill_data'];
                Db::name('group_order')->where(['order_no'=>$v['order_no']])->update(['order_status'=>3]);
                if($user['wx_openid'])
                {   
                    $str = "恭喜您获得".$arr1['bill_data']."元红包,已放入积分,参团本金已退回余额";
                    $this->fasong($user['wx_openid'],$v['order_no'],$groupinfo['goods_name'],$url,$str);
                }
            }
            else
            {   //中产品
                $get_idlist[] = $v['users_id'];
                $get_num --;
                if($groupinfo['goods_class'] == 1)
                {//实物
                    //订单状态改需要发货
                    Db::name('group_order')->where(['order_no'=>$v['order_no']])->update(['ship_type'=>1,'order_status'=>1]);
                }
                else
                {//虚拟产品
                    
                    $verify_arr = [
                            'verify_code' => '',//核销编码,
                            'create_time' => $time,
                            'check_type' => $goods['check_type'],
                            'order_no' => $v['order_no'],
                            'users_id' => $v['users_id'],
                            'expired_time' => $time + 30*24*60*60,//有效期1个月
                            'goods_id' => $groupinfo['goods_id']
                        ];
                    $verify[] = $verify_arr;
                    Db::name('group_order')->where(['order_no'=>$v['order_no']])->update(['ship_type'=>2,'order_status'=>3]);
                    //中虚拟物品，用户抽奖次数+1
                    $lottery_num = Db::name('users_lottery_num')->where(['date_time'=>$date_time,'users_id'=>$v['users_id']])->find();
                    if($lottery_num)
                    {   
                        $num_arr = [
                                'get_num' => $lottery_num['get_num'] + 1,
                                'surplus_num' => $lottery_num['surplus_num'] + 1,
                            ];
                        Db::name('users_lottery_num')->where(['id'=>$lottery_num['id']])->update($num_arr);
                        //Db::name('users_lottery_num')->where(['id'=>$lottery_num['id']])->update(['get_num'=>'get_num'+1,'surplus_num'=>'surplus_num'+1]);
                    }
                    else
                    {
                        $num_arr = [
                                'date_time' => $date_time,
                                'users_id' => $v['users_id'],
                                'get_num' => 1,
                                'surplus_num' => 1
                            ];
                        Db::name('users_lottery_num')->insert($num_arr);
                    }
                    if($user['wx_openid'])
                    {   
                        $str = "恭喜您获得".$groupinfo['goods_name'];
                        $this->fasong($user['wx_openid'],$v['order_no'],$groupinfo['goods_name'],$url,$str);
                    }
                }
            }
            //分佣
            $this->UsersDistribution($v['users_id'],$v['price'],1,$team['team_id']);
            
        }
        //商品销量增加
        Db::name('goods')->where(['id'=>$groupinfo['goods_id']])->setInc('sales',$team['get_num']);
        //合伙人分红
        if(!empty($groupinfo['dividend_price']))
        {
            $this->UsersPartnerDividend($groupinfo['dividend_price'],$group_id);
        }
            
        //活动参与列表状态变更
        Db::name('group_join')->where(['team_id '=>$team['team_id']])->whereIn('users_id',$no_idlist)->update(['join_status'=>2,'return_time'=>$time]);
        Db::name('group_join')->where(['team_id '=>$team['team_id']])->whereIn('users_id',$get_idlist)->update(['join_status'=>1,'return_time'=>$time]);
        
        //用户余额增加
        Db::name('users')->whereIn('users_id',$no_idlist)->setInc('balance_money',$num_money);
        
        //用户积分增加
        Db::name('users')->whereIn('users_id',$no_idlist)->setInc('point',$num_point);
        
        //记录余额明细
        Db::name('users_bill')->insertAll($all_arr);
        //虚拟产品添加核销码
        if(count($verify) > 0)
        {
            Db::name('verify')->insertAll($verify);
        }
        
       
        
    }
    
}