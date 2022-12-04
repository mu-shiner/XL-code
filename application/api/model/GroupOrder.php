<?php
/**
 * 订单表操作
 */

namespace app\api\model;
use think\Model;
use think\Db;

class GroupOrder extends Model
{   
    public function addGroupOrder($data)
    {
        $res = Db::name('group_order')->insert($data);
        return $res;
    }
    
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('group_order')->where($where)->field($field)->order('create_time DESC')->select();
        return $list;
        
    }
    
    public function getInfo($where, $field='*')
    {   
        $where['is_delete'] = 0;
        $info =  Db::name('group_order')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveData($data,$where)
    {   
        $res = Db::name('group_order')->where($where)->update($data);
        return $res;
    }
    
    public function getDataSum($where=[], $field)
    {
        $where['is_delete'] = 0;
        $list = Db::name('group_order')->where($where)->sum($field);
        return $list;
    }
    
    
    public function getDataCount($where=[])
    {
        $where['is_delete'] = 0;
        $list = Db::name('group_order')->where($where)->count();
        return $list;
    }
    
    public function getLevelInfo($where)
    {
        $where['is_delete'] = 0;
        $info =  Db::name('member_level')->where($where)->find();
        return $info;
    }
    
    public function getLevelList($where=[])
    {
        $where['is_delete'] = 0;
        $list = Db::name('member_level')->where($where)->select();
        return $list;
    }
    
    
}