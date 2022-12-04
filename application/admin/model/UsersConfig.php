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

class UsersConfig extends Model
{
    public function getList($where=[])
    {   
        $where['is_delete'] = 0;
        $list = Db::name('users_config')->where($where)->select();
        
        return $list;
        
    }
    
    public function getInfo($where)
    {   
        $where['is_delete'] = 0;
        $info = Db::name('users_config')->where($where)->find();
        return $info;
    }
    
    public function saveUsersConfig($data,$where)
    {
        $res = Db::name('users_config')->where($where)->update($data);
        return $res;
    }
    
    public function getWithdrawList($where=[])
    {   
        $where['is_delete'] = 0;
        $list = Db::name('withdraw_config')->where($where)->select();
        return $list;
        
    }
    
    public function getWithdrawInfo($where)
    {   
        $where['is_delete'] = 0;
        $info = Db::name('withdraw_config')->where($where)->find();
        return $info;
    }
    
    public function saveWithdrawConfig($data,$where)
    {
        $res = Db::name('withdraw_config')->where($where)->update($data);
        return $res;
    }
    
    public function getPartnerConfig($where, $field='*')
    {   
        $info =  Db::name('partner_config')->where($where)->field($field)->find();
        return $info;
    }
    
    public function savePartnerConfig($data,$where)
    {   
        $res = Db::name('partner_config')->where($where)->update($data);
        return $res;
    }
    
}