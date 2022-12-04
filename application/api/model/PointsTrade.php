<?php
/**
 * Created by PhpStorm.
 * User: 罗伟
 * Date: 2022/9/18
 * Time: 16:13
 */

namespace app\api\model;


use think\Model;

class PointsTrade extends Model
{
    public function goods(){
        return $this->belongsTo(Goods::class,"goods_id");
    }
}