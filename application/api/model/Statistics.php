<?php


namespace app\api\model;
use think\Model;
use think\Db;

class Statistics extends Model
{
    public function getList($where=[], $field='*')
    {   
        $list = Db::name('statistics')->where($where)->field($field)->select();
        return $list;
    }
    
    public function getInfo($where=[], $field='*')
    {   
        
        $info = Db::name('statistics')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveInfo($data, $where)
    {
        $res = Db::name('statistics')->where($where)->update($data);
        return $res;
    }
    
    public function getCount($where=[])
    {  
        $info = Db::name('statistics')->where($where)->count();
        return $info;
    }
    
    public function getDataSum($where=[], $field)
    {
        $info = Db::name('statistics')->where($where)->sum($field);
        return $info;
    }
    
    public function saveNumInc($where, $field, $num=1)
    {   
        $res = Db::name('statistics')->where($where)->setInc($field,$num);
        return $res;
    }
    
    
}