<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\admin\model;
use think\Model;
use think\Db;

class Service extends Model
{
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('service')->where($where)->field($field)->select();
        return $list;
        
    }
    
    public function getInfo($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $info = Db::name('service')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveService($data, $where)
    {
        $res = Db::name('service')->where($where)->update($data);
        return $res;
    }
    
    public function addService($data)
    {
        $res = Db::name('service')->insert($data);
        return $res;
    }
    
    public function serviceNumInc($where, $field, $num=1)
    {   
        $res = Db::name('service')->where($where)->setInc($field,$num);
        return $res;
    }
    
    public function serviceNumDec($where, $field, $num=1)
    {   
        $res = Db::name('service')->where($where)->setDec($field,$num);
        return $res;
    }
    
    
    public function setInvitationCode()
    {
        $code = $this->randVar(6,3);
        $info = Db::name('service')->where(['invitation_code'=>$code])->find();
        if(!$info)
        {
            return $code;
        }
        else
        {
            return $this->setInvitationCode();
        }
        
    }
    
    /**
     * 在指定数据范围内实现随机字符组合
     * @param int $length
     * @param int $type
     * @return string
     */
    public function randVar($length = 0, $type = 0)
    {
        $range = array(
            0 => '0123456789',
            1 => 'abcdefghijklmnopqrstuvwxyz',
            2 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            3 => '0123456789abcdefghijklmnopqrstuvwxyz',
            4 => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            5 => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            6 => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            7 => '3456789abcdefghijkmnpqrstuvwxyABCDEFGHJKLMNPQRSTUVWXY',
            8 => '23456789ABCDEFGHJKLMNPQRSTUVWXY',
            9 => '6345829ABDEGHCJKLMNPQRSFTUWXY',
            10 => '23456789',
            11 => '0123456789@a!b#c%de&fg*hi=jk[l]m(no)pq.rstuvwxyz');
        if (false === array_key_exists($type, $range)) {
            $type = 6;
        }
        $character = '';
        $maxLength = strlen($range[$type]) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $character .= $range[$type][mt_rand(0, $maxLength)];
        }
        return $character;
    }
    
    
    
}