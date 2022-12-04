<?php


namespace app\admin\model;
use think\Model;
use think\Db;

class Version extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('version')->where($where)->field($field)->order('create_time DESC')->select();
        return $list;
    }
    
    public function getInfo($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $info = Db::name('version')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveInfo($data,$where)
    {
        $res = Db::name('version')->where($where)->update($data);
        return $res;
    }
}