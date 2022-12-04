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

class UsersBill extends Model
{
    public function getList($where=[], $field='*', $start=0, $limit=100)
    {   
        
        $list = Db::name('users_bill')->where($where)->field($field)->limit($start, $limit)->order('create_time DESC')->select();
        //echo Db::name('users_bill')->getLastSql();
        
        return $list;
        
    }
    
    public function getBillList($where=[])
    {   
        $list = Db::name('users_bill')->where($where)->select();
        return $list;
    }
    
    
    public function getInfo($where, $field='*')
    {   
        $info = Db::name('users_bill')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveUsersBill($data, $where)
    {
        $res = Db::name('users_bill')->where($where)->update($data);
        return $res;
    }
    
    public function addUserBill($data)
    {
        $res = Db::name('users_bill')->insert($data);
        return $res;
    }
    
    public function getDataSum($where=[], $field)
    {
        $list = Db::name('users_bill')->where($where)->sum($field);
        return $list;
    }
    
    public function getPartnerSumByWeek($users_id)
    {   
        $begin_time = 1656259200;
        $end_time = 1656864000;
        $where = [
                'users_id' => $users_id,
                'create_time' => ['between',"$begin_time,$end_time"],
                'bill_data' => ['gt',0],
                'bill_type' => 'partner_money'
            ];
        $list = Db::name('users_bill')->where($where)->sum('bill_data');
        return $list;
    }
    
  
}