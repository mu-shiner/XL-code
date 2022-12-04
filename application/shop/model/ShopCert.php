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

class ShopCert extends Model
{
    public function getList($where=[], $field='*')
    {   
       
        $list = Db::name('shop_cert')->where($where)->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        
        $info = Db::name('shop_cert')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveInfo($data, $where)
    {
        $res = Db::name('shop_cert')->where($where)->update($data);
        return $res;
    }
    
    public function addInfo($data)
    {
        $res = Db::name('shop_cert')->insert($data);
        return $res;
    }
    
    
}