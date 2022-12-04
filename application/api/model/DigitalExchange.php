<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\api\model;
use think\Model;
use think\Db;

class DigitalExchange extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('digital_exchange')->where($where)->field($field)->select();
        return $list;
    }
    
    public function getInfo($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        
        $info = Db::name('digital_exchange')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveInfo($data,$where)
    {
        $res = Db::name('digital_exchange')->where($where)->update($data);
        return $res;
    }
    
    public function addData($data)
    {
        $res = Db::name('digital_exchange')->insert($data);
        return $res;
    }
    
    public function getDataSum($where=[], $field)
    {
        $sum = Db::name('digital_exchange')->where($where)->sum($field);
        return $sum;
    }
    
    public function getMoneyList($where=[], $field='*')
    {   
        $list = Db::name('digital_money')->where($where)->field($field)->select();
        return $list;
    }
    
    public function getMoneyInfo($where=[], $field='*')
    {   
        $info = Db::name('digital_money')->where($where)->field($field)->find();
        return $info;
    }
    
}