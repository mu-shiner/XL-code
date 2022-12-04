<?php


namespace app\command;


use app\api\model\Users;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class JifenDh extends Command {
	protected function configure()
	{
		// 指令配置
		$this->setName('积分兑换到账');
		// 设置参数
	}

	protected function execute(Input $input, Output $output)
	{
		$list = \app\api\model\JifenDh::where("isget",0)->where("gettime",">",time())->select();
		try{
			Db::startTrans();
			foreach ($list as $v){
				Users::where("users_id",$v['user_id'])->inc("point",$v['jifen'])->update();
			}
			\app\api\model\JifenDh::where("isget",0)->where("gettime",">",time())->update(['isget'=>1]);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			echo $e->getMessage();die();
		}
		echo "执行成功";

	}
}