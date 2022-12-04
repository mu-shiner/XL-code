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
    
    public function goodsNumInc($where, $field, $num=1)
    {   
        $res = Db::name('goods')->where($where)->setInc($field,$num);
        return $res;
    }
}