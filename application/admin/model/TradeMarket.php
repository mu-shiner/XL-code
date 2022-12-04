<?php


namespace app\admin\model;


use think\Model;

class TradeMarket extends Model
{
    public function user(){
        return $this->belongsTo(Users::class,"users_id","users_id");
    }
    public function goods()
    {
        return $this->belongsTo(Goods::class,"goods_id");
    }
    public function receiveUser()
    {
        return $this->belongsTo(Users::class,"buy_users_id","users_id");
    }
}