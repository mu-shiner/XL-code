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

class GroupJoin extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('group_join')->where($where)->field($field)->order('create_time DESC')->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $info = Db::name('group_join')->where($where)->field($field)->find();
        return $info;
    }
    
    public function savegroupJoin($data, $where)
    {
        $res = Db::name('group_join')->where($where)->update($data);
        return $res;
    }
    
    public function addgroupJoin($data)
    {
        $res = Db::name('group_join')->insert($data);
        return $res;
    }
    
    
    
}