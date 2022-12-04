<?php


namespace app\api\controller;


use app\admin\model\JifenSet;
use app\api\model\JifenDh;
use app\api\model\Users;
use app\api\model\Verify;
use app\common\controller\Apibase;
use think\Db;

class Goodstojf extends Apibase{
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
	public function getVerifyList()
	{
		$time = time();
		$verifyList = Verify::where("is_verify",0)->where("(expired_time > {$time} or expired_time = 0)")
			->field("id,goods_id")
			->where("users_id",$this->users_id)
			->with(["goods"=>function($query){
				$query->where("jifen","neq",0);
			}])
			->paginate(10);
		foreach ($verifyList as $k=>$v)
		{
			$v->visible(["id"]);
			$v->visible(["goods"]);
			$v->getRelation("goods")->visible(["id","goods_name","image_url","jifen"]);
		}
		return  $this->success_req($verifyList);
	}
	public function exchange(){
		$params = $this->params;
		$ids = $params['verify_ids'];
		$time = time();
		$list = Verify::where("is_verify",0)->where("(expired_time > {$time} or expired_time = 0)")
			->field("id,goods_id")
			->where("Verify.id","in",$ids)
			->where("users_id",$this->users_id)
			->with(["goods"=>function($query){
				$query->where("jifen","neq",0);
			}])->select();
		$total = 0;
		$success = [];
		foreach ($list  as $v)
		{
			$total += $v['goods']['jifen'];
			$success[] = $v['id'];
		}
		try {
			Db::startTrans();
			$nums = count($success);
			Users::where("users_id",$this->users_id)->inc("point_withdraw_amount",$nums*1000)->update();
			$jfdays = JifenSet::order("id desc")->value("js_time");
			$time = time()+$jfdays*24*60*60;
			JifenDh::create(["jifen"=>$total,"user_id"=>$this->users_id,"gettime"=>$time]);
			Verify::where("id",'in',$success)->update(['is_verify'=>3]);
			Db::commit();
		}catch (\Exception $e)
		{
			dump($e->getMessage());
			Db::rollback();
			trace($e->getMessage(),"error");
			return $this->error_req(100, "系统繁忙,稍后再试");
		}
		return $this->success_req("兑换成功");
	}
}