<?php
/**
 * Created by PhpStorm.
 * User: verifyistrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\shop\model;
use think\Model;
use think\Db;

class Verify extends Model
{
    public function getList($where=[], $field='*')
    {   
       
        $list = Db::name('verify')->where($where)->field($field)->order('verify_time DESC')->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        
        $info = Db::name('verify')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveVerify($data, $where)
    {
        $res = Db::name('verify')->where($where)->update($data);
        return $res;
    }
    
    public function addVerify($data)
    {
        $res = Db::name('verify')->insert($data);
        return $res;
    }
    
    
    
}