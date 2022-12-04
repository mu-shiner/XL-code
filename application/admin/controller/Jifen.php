<?php


namespace app\admin\controller;


use app\admin\model\JifenSet;
use app\common\controller\Adminbase;

class Jifen extends Adminbase{
	public function set()
	{
		$set = JifenSet::order("id desc")->find();
		if($this->request->isPost()){
			if($set){
				$set->save($this->request->param());
			}else{
				JifenSet::create($this->request->param());
			}
			return $this->success('保存成功', 'Jifen/set');
		}
		$this->assign("set",$set);
		return $this->fetch();
	}
}