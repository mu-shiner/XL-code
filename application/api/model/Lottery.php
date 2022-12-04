<?php


namespace app\api\model;


use think\Model;

class Lottery extends Model {
	public function goods()
	{
		return $this->hasMany(LotteryGoods::class,"lottery_id","lottery_id");
	}
	public function verify()
	{
		return $this->hasMany(Verify::class,"goods_id","goodsid");
	}
}