<?php
/**
 * Created by PhpStorm.
 * User: shopistrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\shop\model;
use think\Model;
use think\Db;

class Shop extends Model
{
    public function getList($where=[], $field='*')
    {   
       
        $list = Db::name('shop')->where($where)->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        
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
    
    
    public function ShopNumInc($where, $field, $num=1)
    {   
        $res = Db::name('shop')->where($where)->setInc($field,$num);
        return $res;
    }
    
    public function ShopNumDec($where, $field, $num=1)
    {   
        $res = Db::name('shop')->where($where)->setDec($field,$num);
        return $res;
    }
    
    
}