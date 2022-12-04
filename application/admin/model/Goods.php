<?php


namespace app\admin\model;
use think\Model;
use think\Db;

class Goods extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('goods')->where($where)->field($field)->select();
        return $list;
    }
    
    public function getInfo($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $info = Db::name('goods')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveGoods($data,$where)
    {
        $res = Db::name('goods')->where($where)->update($data);
        return $res;
    }
}