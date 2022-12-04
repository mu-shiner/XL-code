<?php


namespace app\admin\model;


use think\Db;
use think\Model;

class Box extends Model {

	public function savebox($data,$where)
	{
		$res = Db::name('box')->where($where)->update($data);
		return $res;
	}

}