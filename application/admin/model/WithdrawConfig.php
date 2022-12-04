<?php


namespace app\admin\model;


use think\Model;

class WithdrawConfig extends Model {
	public function getConfigTypeAttr($value,$data)
	{
		$data = [1=>"积分",2=>"余额",3=>"合伙人",4=>"店铺余额"];
		return $data[$value] ?? "";
	}
}