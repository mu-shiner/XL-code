<?php


namespace app\api\controller;


use app\api\model\Goods;
use app\api\model\PointsTrade;
use app\api\model\Users;
use app\api\model\UsersAddress;
use app\common\controller\Apibase;
use think\Db;

class Pointsmarket extends Apibase
{
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $token = $this->checkToken();
        if(!$this->users_id)
        {
            echo $this->error_req(100, '用户未登录');exit;
        }
        //更新用户操作时间
        $users=new Users();
        $users->saveData(['last_operate_time'=>time()],['users_id'=>$this->users_id]);
    }
    public function pointsGoodsList()
    {
        $order = $this->params['order'] ?: 0;//排序0综合1单价2数量
        $by = isset($this->params['by'])?$this->params['by']:1;//排序方式1正序0倒序
        switch ($order) {
            case '0':
                $orderby = 'id';
                break;
            case '1':
                $orderby = 'jifen';
                break;
            case '2':
                $orderby = 'verify_num';
                break;
            default:
                $orderby = 'id';
                break;
        }
        if($by == 1)
        {
            $orderby .= ' ASE';
        }
        else
        {
            $orderby .= ' DESC';
        }
        $list = Goods::where("is_prize",2)->where("goods_state",1)->order($orderby)->field("id,goods_name,image_url,jifen,coin")->paginate(10);
        return  $this->success_req($list);
    }
    public function goodsDetail(){
        $id = $this->params['goods_id'];
        $goods = Goods::where("id",$id)->where("goods_state",1)->find();
        if(!empty( $goods->specs)) $goods->specs = json_decode($goods->specs,true);
        return  $this->success_req($goods);
    }
    public function  trade()
    {
        $id = $this->params['goods_id'];
        $spec = $this->params['spec'] ?:'';
        $goods = Goods::where("id",$id)->where("goods_state",1)->find();
        if(!$goods)  return $this->error_req(100, '商品未上架');
        if($goods->stock <1) return $this->error_req(100, '库存不足');
        $users = Users::where("users_id",$this->users_id)->find();
        if($users['point']<$goods['jifen']) return $this->error_req(100, '积分不足以兑换该商品');
        try{
            Db::startTrans();
            if($goods['goods_class'] == 1){
                $status = 1;
            }else{
                $status = 0;
            }
            $po = PointsTrade::create(['users_id'=>$this->users_id,"goods_id"=>$id,"points"=>$goods['jifen'],'coin'=>$goods['coin'],'create_time'=>time(),'spec'=>$spec,'status'=>$status,
                "goods_name"=>$goods['goods_name'],"goods_img"=>$goods['image_url']
            ]);
            $users->point-= $goods['jifen'];
            $users->coin+= $goods['coin'];
            $users->save();
            $goods->verify_num+=1;
            $goods->stock -=1;
            $goods->save();
            Db::commit();
        }catch (\Exception $e){
           Db::rollback();
            return $this->error_req(100, '系统繁忙,请稍后重试');
        }
        return  $this->success_req(['id'=>$po['id']]);
    }
    public function tradeLog(){
        $list = PointsTrade::where("users_id",$this->users_id)->with("goods")->order("id desc")->paginate(10);
        return  $this->success_req($list);
    }
    public function usersPoinstsAddress()
    {
        $id = $this->params['id'];
        $address_id = $this->params['address_id'];
        $info = PointsTrade::where("id",$id)->find();
        if(!$info || $info['users_id'] != $this->users_id)
        {
            return $this->error_req(100, 'id错误');
        }
        if($info['status'] == 0)
        {
            return $this->error_req(100, '虚拟物品无须发货');
        }
        if($info['status'] != 1)
        {
            return $this->error_req(100, '状态已改变');
        }
        if(!$address_id)
        {
            return $this->error_req(100, '用户地址不能为空');
        }
        $address = new UsersAddress();
        $address_info = $address->getInfo(['address_id'=>$this->params['address_id']]);
        if(!$address_info)
        {
            return $this->error_req(100, '用户地址填写错误');
        }
        $data['receipt_name'] = $address_info['name'];
        $data['telephone'] = $address_info['telephone'];
        $data['complete_address'] = $address_info['province_name'].$address_info['city_name'].$address_info['area_name'].$address_info['address'];
        $info->save($data);
        return $this->success_req('操作成功');
    }
}