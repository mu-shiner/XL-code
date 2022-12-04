<?php
/**
 * Created by PhpStorm.
 * User: 罗伟
 * Date: 2022/10/2
 * Time: 18:12
 */

namespace app\admin\model;


use think\Model;

class UserUpdatepriceLog extends Model
{
    public function admin()
    {
        return $this->belongsTo(Admin::class,"admin_id");
    }
    public function users()
    {
        return $this->belongsTo(Users::class,"users_id","users_id");
    }
}