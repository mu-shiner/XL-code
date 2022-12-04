<?php


namespace app\api\controller;

use app\api\model\UsersConfig;
use app\api\model\Verify;
use app\common\controller\Apibase;
use app\admin\model\Lottery as LotteryModel;
use app\api\model\Users;
use app\api\model\Goods;
use app\api\model\UsersLottery;
use app\api\model\UsersAddress;
use think\Db;

class Lottery extends Apibase
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
     * 抽奖活动
     */
    public function index()
    {
		$list =  \app\api\model\Lottery::where("is_open",1)
			->with("goods")->select();
		$time = time();
		foreach ($list as $k=>$v)
		{
			$list[$k]["user_nums"]=Verify::where("goods_id",$v['goodsid'])->where("users_id",$this->users_id)->where("is_verify",0)->where("(expired_time > {$time} or expired_time = 0)")->count();
			$list[$k]['name'] = Goods::where("id",$v['goodsid'])->value('goods_name');
		}
		return $this->success_req($list);
    }

    /**
     * 用户抽奖
     */
    public function usersLottery()
    {
        $this->write_log($this->params);
        if(!$this->params['lottery_id'])
        {
            return $this->error_req(100, 'id不能为空');
        }
        $model = new LotteryModel();
        $cond = [
                'lottery_id' => $this->params['lottery_id']
            ];

        $info = $model->getInfo($cond);
        $time = time();
        $begin_time = strtotime($info['begin_time']);
        $end_time = strtotime($info['end_time']);
		if($info['is_open'] !=1)
		{
			return $this->error_req(100, '抽奖活动未开启');
		}
        // if($begin_time > $time || $end_time < $time)
        // {
        //     return $this->error_req(100, '不在抽奖时间内');
        // }
        //获取可消耗的盲盒券
		$verify =   Verify::where("goods_id",$info['goodsid'])
			->where("users_id",$this->users_id)
			->where("is_verify",0)
			->where("expired_time > {$time}")
			->order("expired_time asc")
			->find();
        if(!$verify)
        {
			$verify = Verify::where("goods_id",$info['goodsid'])
				->where("users_id",$this->users_id)
				->where("is_verify",0)
				->where("expired_time = 0")
				->find();
		}
        if(!$verify)
        {
            return $this->error_req(100, '无抽奖次数');
        }
        //抽奖奖品
        $list = $model->getGoodsList(['lottery_id'=>$info['lottery_id']]);
        $arr = [];
        foreach ($list as $v)
        {
            for ($i = 0; $i < $v['surplus_num']; $i++)
            {
                $arr[] = $v['goods_id'];
            }
        }
        $prize_num = count($arr);
        if($prize_num <= 0) return $this->error_req(100, '奖品库存不足,请联系管理员');
        $not_num = round($prize_num/2);
        //添加奖品一半数量的谢谢惠顾
        // for ($i = 0; $i < $not_num; $i++)
        // {
        //     $arr[] = 0;
        // }
        shuffle($arr);

        //随机取一个
        $key = array_rand($arr);
        //中的商品id
        $get_goods = $arr[$key];

        $data = [
                'lottery_id' => $this->params['lottery_id'],
                'goods_id' => $get_goods,
                'users_id' => $this->users_id,
                'create_time' => $time
            ];
        Db::startTrans();
        try {
			$goods = new Goods();
            if($get_goods == 0)
            {
                //未中奖
                $data['is_get'] = 0;
            }
            else
            {   
                //已中奖
                $data['is_get'] = 1;
                $goodsinfo = $goods->getInfo(['id'=>$get_goods],'id,goods_name,image_url,goods_class,jifen,goods_price');
                $data['goods_name'] = $goodsinfo['goods_name'];
                $data['goods_img'] = $goodsinfo['image_url'];
                $data['subtitle'] = $goodsinfo['subtitle'];
                $data['jifen'] = $goodsinfo['jifen'];
                $data['goods_price'] = $goodsinfo['goods_price'];

                //商品数量减1
                $model->goodsNumDec(['lottery_id'=>$this->params['lottery_id'],'goods_id'=>$get_goods],'surplus_num');
            }

            $userslottery = new UsersLottery();
            
            //存抽奖记录
            $res = $userslottery->addInfo($data);
         	$verify->is_verify = 3;
         	$verify->save();
         	$xh_goods = $goods->getInfo(['id'=>$info['goodsid']],'id,goods_price');

			$users = new Users();
			$users->UsersNumInc(['users_id'=>$this->users_id],"point_withdraw_amount",1000);
			$configinfo = UsersConfig::where("config_type",2)->find();
			switch ($configinfo['level']){
				case 1:
					$parent_id = Users::where("users_id",$this->users_id)->value("parent_id");
					//增加分佣积分
					$users->UsersNumInc(['users_id'=>$parent_id],'point',round($xh_goods['price']*0.012,5));
					break;
				case 2:
					$parent_id = Users::where("users_id",$this->users_id)->value("parent_id");
					$parent_parent_id = Users::where("users_id",$parent_id)->value("parent_id");
					//增加分佣积分
					$users->UsersNumInc(['users_id'=>$parent_id],'point',round($xh_goods['price']*$configinfo['rate_one']/100,5));
					$users->UsersNumInc(['users_id'=>$parent_parent_id],'point',round($xh_goods['price']*$configinfo['rate_two']/100,5));
					break;
				case 3:
					$parent_id = Users::where("users_id",$this->users_id)->value("parent_id");
					$parent_parent_id = Users::where("users_id",$parent_id)->value("parent_id");
					$parent_parent_parent_id = Users::where("users_id",$parent_id)->value("parent_id");
					//增加分佣积分
					$users->UsersNumInc(['users_id'=>$parent_id],'point',round($xh_goods['price']*$configinfo['rate_one']/100,5));
					$users->UsersNumInc(['users_id'=>$parent_parent_id],'point',round($xh_goods['price']*$configinfo['rate_two']/100,5));
					$users->UsersNumInc(['users_id'=>$parent_parent_parent_id],'point',round($xh_goods['price']*$configinfo['rate_three']/100,5));
					break;
			}
            Db::commit();
			$data['id'] = $res;
            return $this->success_req($data);
        } catch (\Exception $e) {
            Db::rollback();
            return $this->error_req(100, $e->getMessage());
        }
    }

    //抽奖记录
    public function usersLotteryList()
    {
        $model = new UsersLottery();
        $where['users_id'] = $this->users_id;
        isset($this->params['is_get'])?$where['is_get'] = $this->params['is_get']:'';//1中奖2未中奖
        $list = $model->getList($where);
        return $this->success_req($list);
    }


    //用户中奖填写收获地址
    public function usersLotteryAddress()
    {
        $model = new UsersLottery();
        $id = $this->params['id'];
        $address_id = $this->params['address_id'];
        $info = $model->getInfo(['id'=>$id]);
        if(!$info || $info['users_id'] != $this->users_id)
        {
            return $this->error_req(100, 'id错误');
        }
        if($info['status'] != 0)
        {
            return $this->error_req(100, '状态已改变');
        }
        if(!$address_id)
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
        $data['status'] = 1;
        $res = $model->saveInfo($data,['id'=>$id]);
        if(!$res)
        {
            return $this->error_req(100, '操作失败');
        }
        return $this->success_req('操作成功');
    }

    //用户签收
    public function receipt()
    {
        $model = new UsersLottery();
        $id = $this->params['id'];
        $info = $model->getInfo(['id'=>$id]);
        if(!$info || $info['users_id'] != $this->users_id)
        {
            return $this->error_req(100, 'id错误');
        }
        if($info['status'] != 2)
        {
            return $this->error_req(100, '状态已改变');
        }

        $res = $model->saveInfo(['status'=>3,'receipt_time'=>time()],['id'=>$id]);
        if(!$res)
        {
            return $this->error_req(100, '操作失败');
        }

        return $this->success_req("收货成功");
    }
    public function tradePoint()
    {
        $model = new UsersLottery();
        $id = $this->params['id'];
        $info = $model->getInfo(['id'=>$id]);
         if(!$info || $info['users_id'] != $this->users_id)
        {
            return $this->error_req(100, 'id错误');
        }
        if($info['status'] != 0)
        {
            return $this->error_req(100, '状态已改变');
        }
         $res = $model->saveInfo(['status'=>3,'receipt_time'=>time()],['id'=>$id]);
         $jifen = Goods::where("id",$info['goods_id'])->value("jifen");
         if($jifen && $jifen > 0){
              Users::where("users_id",$this->users_id)->setInc("point",$jifen);
         }
         return $this->success_req("兑换成功");
        
    }
     public function apply()
    {
        $model = new UsersLottery();
        $id = $this->params['id'];
        $info = $model->getInfo(['id'=>$id]);
        $status = $this->params['status'];
        if(!$info || $info['users_id'] != $this->users_id)
        {
            return $this->error_req(100, 'id错误');
        }
        if($info['status'] != 0)
        {
            return $this->error_req(100, '状态已改变');
        }
        $res = $model->saveInfo(['status'=>$status],['id'=>$id]);
        return $this->success_req("申请成功");
    }

}
