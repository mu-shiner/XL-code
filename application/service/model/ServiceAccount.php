<?php
/**
 * Created by PhpStorm.
 * User: service_accountistrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\service\model;
use think\Model;
use think\Db;

class ServiceAccount extends Model
{
    public function getList($where=[], $field='*')
    {   
       
        $list = Db::name('service_account')->where($where)->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        
        $info = Db::name('service_account')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveInfo($data, $where)
    {
        $res = Db::name('service_account')->where($where)->update($data);
        return $res;
    }
    
    public function addInfo($data)
    {
        $res = Db::name('service_account')->insert($data);
        return $res;
    }
    
    public function addServiceWithdraw($data)
    {
        $res = Db::name('service_withdraw')->insert($data);
        return $res;
    }
    
    public function getServiceWithdraw($where=[], $field='*')
    {
        $list = Db::name('service_withdraw')->where($where)->field($field)->select();
        return $list;
    }
    
    public function getWithdrawInfo($where=[], $field='*')
    {   
        
        $info = Db::name('service_withdraw')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveServiceWithdraw($data, $where)
    {
        $res = Db::name('service_withdraw')->where($where)->update($data);
        return $res;
    }
    
}