<?php

namespace app\api\model;
use think\Model;
use think\Db;

class TradeMarket extends Model
{
    public function getList($where=[], $field='*', $orderby='market_id ASC', $start=0, $limit=100)
    {   
        //$where['is_delete'] = 0;
        $list = Db::name('trade_market')->where($where)->field($field)->limit($start, $limit)->order($orderby)->select();
        return $list;
        
    }
    
    public function getTradeMarketList($where=[], $field='*')
    {   
        $list = Db::name('trade_market')->where($where)->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        //$where['is_delete'] = 0;
        $info = Db::name('trade_market')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveData($data, $where)
    {
        $res = Db::name('trade_market')->where($where)->update($data);
        return $res;
    }
    
    public function addData($data)
    {
        $res = Db::name('trade_market')->insert($data);
        return $res;
    }
    
    
    public function getDataSum($where=[], $field)
    {
        $list = Db::name('trade_market')->where($where)->sum($field);
        return $list;
    }
    
    //汇率（转让金额总和/转让数量）
    public function getRate()
    {
        $all_price = Db::name('trade_market')->where(['status'=>1])->sum('price');
        $all_num = Db::name('trade_market')->where(['status'=>1])->sum('num');
        if($all_price && $all_num)
        {
            $rate = round($all_price/$all_num,2);
        }
        else
        {
            $rate = 300;
        }
        //$rateinfo = Db::name('market_rate')->order('create_time DESC')->find();
        // if($rateinfo['rate'] != $rate)
        // {
        //     Db::name('market_rate')->insert(['rate'=>$rate,'create_time'=>time()]);
        // }
        
        return $rate;
    }
    
    
    public function getRateList($where=[])
    {   
        $list = Db::name('market_rate')->where($where)->limit(0, 20)->order('create_time DESC')->select();
        return $list;
        
    }
    
    
    public function getAllNum($where=[])
    {   
        $res = Db::name('trade_market')->where($where)->count();
        return $res;
    }
    
    
}