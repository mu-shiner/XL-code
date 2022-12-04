<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\admin\model;
use think\Model;
use think\Db;

class Lottery extends Model
{
    public function getList($where=[], $field='*')
    {   
        $list = Db::name('lottery')->where($where)->field($field)->select();
        
        return $list;
        
    }
    
    public function getInfo($where, $field='*')
    {   
        $info = Db::name('lottery')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveLottery($data, $where)
    {
        $res = Db::name('lottery')->where($where)->update($data);
        return $res;
    }
    
    public function saveLotteryGoods($data, $where)
    {
        $res = Db::name('lottery_goods')->where($where)->update($data);
        return $res;
    }
    
    
    public function getGoodsList($where=[], $field='*')
    {   
        $list = Db::name('lottery_goods')->where($where)->field($field)->select();
        
        return $list;
    }
    
    public function goodsNumDec($where, $field, $num=1)
    {   
        $res = Db::name('lottery_goods')->where($where)->setDec($field,$num);
        return $res;
    }
}