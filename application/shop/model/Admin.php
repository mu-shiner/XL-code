<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\shop\model;
use think\Model;
use think\Db;

class Admin extends Model
{
    public function getList($where=[], $field='*')
    {   
       
        $list = Db::name('admin')->where($where)->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        
        $info = Db::name('admin')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveAdmin($data, $where)
    {
        $res = Db::name('admin')->where($where)->update($data);
        return $res;
    }
    
    public function addAdmin($data)
    {
        $res = Db::name('admin')->insert($data);
        return $res;
    }
    
    
    
}