<?php


namespace app\admin\controller;


use app\common\controller\Apibase;

class Withdrawconfig  extends Apibase {
	protected $config_type = [1=>"积分",2=>"余额",3=>"合伙人",4=>"店铺余额"];
	public function index()
	{
		$list = \app\admin\model\WithdrawConfig::select();
		$this->assign("list",$list);
		return $this->fetch();
	}
	public function edit()
	{
		if($this->request->isPost())
		{
			$post = $this->request->post();
			$info = \app\admin\model\WithdrawConfig::where("id",$post['id'])->find();
			$info->save($post);
			return $this->success("修改成功",url("index"));
		}
		$info = \app\admin\model\WithdrawConfig::where("id",input("param.id"))->find();
		$this->assign("info",$info);
		return $this->fetch();
	}
}