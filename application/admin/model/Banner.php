<?php


namespace app\admin\model;


use think\Model;

class Banner extends Model
{
    public function getImgAttr($value,$data)
    {
        return "http://api.blindboxjq.cn".$value;
    }
}