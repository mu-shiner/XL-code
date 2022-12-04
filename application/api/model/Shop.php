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

class Shop extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('shop')->where($where)->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $info = Db::name('shop')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveShop($data, $where)
    {
        $res = Db::name('shop')->where($where)->update($data);
        return $res;
    }
    
    public function addShop($data)
    {
        $res = Db::name('shop')->insert($data);
        return $res;
    }
    
    
    
}