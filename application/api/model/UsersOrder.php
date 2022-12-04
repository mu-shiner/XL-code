<?php
/**
 * 订单表操作
 */

namespace app\api\model;
use think\Model;
use think\Db;

class UsersOrder extends Model
{   
    public function addUsersOrder($data)
    {
        $res = Db::name('users_order')->insert($data);
        return $res;
    }
    
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('users_order')->where($where)->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where, $field='*')
    {   
        $where['is_delete'] = 0;
        $info =  Db::name('users_order')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveData($data,$where)
    {   
        $res = Db::name('users_order')->where($where)->update($data);
        return $res;
    }
    
    
    public function getDataSum($where=[], $field)
    {
        $where['is_delete'] = 0;
        $list = Db::name('users_order')->where($where)->sum($field);
        return $list;
    }
   
    
}