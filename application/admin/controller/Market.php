<?php


namespace app\admin\controller;

use app\admin\model\TradeMarket;
use app\admin\model\MarketSet;
use app\common\controller\Adminbase;
use app\api\model\Users;
use app\api\model\TradeMarket as TradeMarketModel;
use app\api\model\Verify;
use think\Db;
use app\api\model\UsersBill;
class Market extends Adminbase
{
	public function set()
	{
		$set = MarketSet::order("id desc")->find();
		if($this->request->isPost()){
			if($set){
				$set->save($this->request->param());
			}else{
				MarketSet::create($this->request->param());
			}
			return $this->success('保存成功', 'Market/set');
		}
		$this->assign("set",$set);
		return $this->fetch();
	}
	public function marketlist()
    {
        $where = [];
        !empty($_GET['status']) ? $where['status'] = $_GET['status'] : '';
        $list = TradeMarket::where($where)->with("user")->with("goods")->with("receiveUser")->paginate(20);
        $this->assign("list",$list);
        return $this->fetch();
    }
      public function automaticBuy($market_id)
    {
        $users_id = 0;
        $users = new Users();

        $model = new TradeMarketModel();
        $info = $model->getInfo(['market_id'=>$market_id]);
        $price = $info['price'];
        if(!$info)
        {
            $this->error("id 错误",url('marketlist'));
        }
        if($info['status'] != 1)
        {
            $this->error("该通用券状态已变更",url('market_list'));
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
            $arr1['users_name'] = "小可爱";
            $arr1['current_money'] = 0;
            $arr1['create_time'] = $time;
            //存余额明细
            $bill->addUserBill($arr1);

            Db::commit();
            
        } catch (\Exception $e) {
            Db::rollback();
            $this->error("购买失败",url('marketlist'));
        }
        $this->success("购买成功",url('marketlist'));
    }
}