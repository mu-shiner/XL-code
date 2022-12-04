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

class UsersLottery extends Model
{
    public function getList($where=[], $field='*')
    {  
      return self::where($where)->with(["goods"=>function($query){
            $query->field("id,jifen");
        }])->field($field)->order('id','DESC')->select();
    }
    
    public function getInfo($where=[], $field='*')
    {  
        $info = Db::name('users_lottery')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveInfo($data, $where)
    {
        $res = Db::name('users_lottery')->where($where)->update($data);
        return $res;
    }
    
    public function addInfo($data)
    {
        $res = Db::name('users_lottery')->insertGetId($data);
        return $res;
    }
    
     public function goods(){
        return $this->belongsTo(Goods::class,"goods_id");
    }
    
}