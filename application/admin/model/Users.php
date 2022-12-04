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

class Users extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('users')->where($where)->field($field)->select();
        
        return $list;
        
    }
    
    public function getInfo($where, $field='*')
    {   
        $where['is_delete'] = 0;
        $info = Db::name('users')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveUser($data,$where)
    {
        $res = Db::name('users')->where($where)->update($data);
		return $res;
    }
    
    public function getDataSum($where=[], $field)
    {
        $where['is_delete'] = 0;
        $list = Db::name('users')->where($where)->sum($field);
        return $list;
    }
    
    public function getUsersNum($where=[])
    {   
        $where['is_delete'] = 0;
        $res = Db::name('users')->where($where)->count();
        return $res;
    }
}