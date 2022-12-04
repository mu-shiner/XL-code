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

class UsersWithdraw extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('users_withdraw')->where($where)->field($field)->select();
        return $list;
    }
    
    public function getInfo($where)
    {   
        $where['is_delete'] = 0;
        $info = Db::name('users_withdraw')->where($where)->find();
        return $info;
    }
    
    public function saveUsersWithdraw($data,$where)
    {
        $res = Db::name('users_withdraw')->where($where)->update($data);
        return $res;
    }
    
    public function addUsersWithdraw($data)
    {
        $res = Db::name('users_withdraw')->insert($data);
        return $res;
    }
    
    public function getWithdrawSum($where=[],$field)
    {   
        
        $res = Db::name('users_withdraw')->where($where)->sum($field);
        return $res;
    }
    
    public function getCount($where=[])
    {  
        $count = Db::name('users_withdraw')->where($where)->count();
        return $count;
    }
    
}