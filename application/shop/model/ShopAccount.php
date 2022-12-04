<?php
/**
 * Created by PhpStorm.
 * User: shop_accountistrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\shop\model;
use think\Model;
use think\Db;

class ShopAccount extends Model
{
    public function getList($where=[], $field='*')
    {   
       
        $list = Db::name('shop_account')->where($where)->order('create_time DESC')->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        
        $info = Db::name('shop_account')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveInfo($data, $where)
    {
        $res = Db::name('shop_account')->where($where)->update($data);
        return $res;
    }
    
    public function addInfo($data)
    {
        $res = Db::name('shop_account')->insert($data);
        return $res;
    }
    
    public function addShopWithdraw($data)
    {
        $res = Db::name('shop_withdraw')->insert($data);
        return $res;
    }
    
    public function getShopWithdraw($where=[], $field='*')
    {
        $list = Db::name('shop_withdraw')->where($where)->field($field)->order('create_time DESC')->select();
        return $list;
    }
    
    public function getWithdrawInfo($where=[], $field='*')
    {   
        
        $info = Db::name('shop_withdraw')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveShopWithdraw($data, $where)
    {
        $res = Db::name('shop_withdraw')->where($where)->update($data);
        return $res;
    }
    
    public function getWithdrawCount($where=[])
    {  
        $count = Db::name('shop_withdraw')->where($where)->count();
        return $count;
    }
    
}