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

class UsersAddress extends Model
{
    public function getList($where=[])
    {   
        $where['is_delete'] = 0;
        $list = Db::name('users_address')->where($where)->select();
        return $list;
    }
    
    public function getInfo($where)
    {   
        $where['is_delete'] = 0;
        $info = Db::name('users_address')->where($where)->find();
        return $info;
    }
    
    public function saveUsersAddress($data,$where)
    {
        $res = Db::name('users_address')->where($where)->update($data);
        return $res;
    }
    
    public function addUsersAddress($data)
    {
        $res = Db::name('users_address')->insert($data);
        return $res;
    }
    
    
   
}