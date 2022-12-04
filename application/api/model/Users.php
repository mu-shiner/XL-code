<?php
/**
 * 会员表操作
 */

namespace app\api\model;
use think\Model;
use think\Db;
//use app\admin\model\UsersConfig;

class Users extends Model
{   
    
    public function getList($where=[], $field='*')
    {   
        $where['is_delete'] = 0;
        $list = Db::name('users')->where($where)->field($field)->select();
        return $list;
    }
    
    public function getInfo($where, $field='*')
    {   
        $where['is_delete'] = 0;
        $info =  Db::name('users')->where($where)->field($field)->find();
        return $info;
    }
    
    public function saveData($data,$where)
    {   
        $res = Db::name('users')->where($where)->update($data);
        return $res;
    }
    
    public function UsersNumInc($where, $field, $num=1)
    {   
        $res = Db::name('users')->where($where)->setInc($field,$num);
        return $res;
    }
    
    public function UsersNumDec($where, $field, $num=1)
    {   
        $res = Db::name('users')->where($where)->setDec($field,$num);
        return $res;
    }
    
    public function getDataSum($where=[], $field)
    {
        $list = Db::name('users')->where($where)->sum($field);
        return $list;
    }
    
    public function getCountByParentId($where=[])
    {   
        $where['is_delete'] = 0;
        $list = Db::name('users')->whereIn('parent_id',$where)->count();
        return $list;
    }
   
    public function getLotteryNum($where, $field='*')
    {   
        $info =  Db::name('users_lottery_num')->where($where)->field($field)->find();
        return $info;
    }
    
    public function UsersLotteryNumDec($where, $field, $num=1)
    {   
        $res = Db::name('users_lottery_num')->where($where)->setDec($field,$num);
        return $res;
    }
    
    public function UsersLotteryNumInc($where, $field, $num=1)
    {   
        $res = Db::name('users_lottery_num')->where($where)->setInc($field,$num);
        return $res;
    }
    
    
    
    public function getPartnerConfig($where, $field='*')
    {   
        $info =  Db::name('partner_config')->where($where)->field($field)->find();
        return $info;
    }
    
    public function savePartnerConfig($data,$where)
    {   
        $res = Db::name('partner_config')->where($where)->update($data);
        return $res;
    }
   
    public function setInvitationCode()
    {
        $code = $this->randVar(6,3);
        $info = Db::name('users')->where(['invitation_code'=>$code])->field('users_id')->find();
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