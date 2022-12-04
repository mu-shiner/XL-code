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

class Group extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('group')->where($where)->field($field)->select();
        
        return $list;
        
    }
    
    public function getInfo($where, $field='*')
    {   
        $where['is_delete'] = 0;
        $info = Db::name('group')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveGroup($data, $where)
    {
        $res = Db::name('group')->where($where)->update($data);
        return $res;
    }
}