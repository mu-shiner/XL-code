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

class GroupTeam extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('group_team')->where($where)->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $info = Db::name('group_team')->where($where)->field($field)->find();
        return $info;
    }
    
    public function savegroupTeam($data, $where)
    {
        $res = Db::name('group_team')->where($where)->update($data);
        return $res;
    }
    
    public function addgroupTeam($data)
    {
        $res = Db::name('group_team')->insert($data);
        return $res;
    }
    
    
    public function getTeamNum($where)
    {
        $res = Db::name('group_team')->where($where)->count();
        return $res;
    }
    
    
}