<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/5
 * Time: 22:35
 */

namespace app\admin\controller;

use app\admin\model\Attachment;
use app\common\controller\Adminbase;
use think\Db;
use app\admin\model\Group as GroupModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Users;
use app\api\model\GroupOrder;
use app\api\model\Shop;

class Group extends AdminBase
{   
    
    
    //拼团列表
    public function groupList()
    {   
        isset($_GET['goodsname'])?$where['goods_name'] = $_GET['goodsname']:'';
        isset($_GET['status'])?$where['status'] = $_GET['status']:'';
        $where['is_delete'] = 0;
        
        $list = Db::name('group')->where($where)->paginate(20);
        
        foreach ($list as $k=>$v)
        {   
            switch ($v['status']) {
                case 0:
                    $stata_name = '已下架';
                    break;
                case 1:
                    $stata_name = '已上架';
                    break;
                case 2:
                    $stata_name = '已结束';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
            $v['state_name'] = $stata_name;
            $v['begin_time']  = date('Y-m-d H:i:s',$v['begin_time']);
            $v['end_time']    = date('Y-m-d H:i:s',$v['end_time']);
            $list[$k] = $v;
        }
        $this->assign('meta_title','拼团列表');
        $this->assign('list', $list);
        return $this->view->fetch('index');
    }

    /**
     * 添加拼团
     */
    public function groupAdd()
    {   
        // echo("<pre>");
        // var_dump($_POST);echo("</pre>");die;
        //判断是否表单提交数据
        if (!empty($_POST)){
            $data = [];
            $id = isset($_POST['group_id'])?$_POST['group_id']:'';
            $data['goods_id']   = isset($_POST['goods_id'])?$_POST['goods_id']:'';//商品id
            $data['begin_time'] = isset($_POST['begin_time'])?strtotime($_POST['begin_time']):'';//活动开始时间
            $data['end_time']   = isset($_POST['end_time'])?strtotime($_POST['end_time']):'';//活动结束时间
            $data['price']      = isset($_POST['price'])?$_POST['price']:0;//价格
            $data['team_num']      = isset($_POST['team_num'])?$_POST['team_num']:0;//价格
            $data['get_num']      = isset($_POST['get_num'])?$_POST['get_num']:0;//价格
            $data['dividend_price'] = isset($_POST['dividend_price'])?$_POST['dividend_price']:0;//市场分红价格
          
            if($data['end_time'] <= $data['begin_time'])
            {
                return $this->error('开始时间不能超过结束时间');
            }
            $goods = new GoodsModel();
            $goodsinfo = $goods->getInfo(['id'=>$data['goods_id']], 'goods_name,image_url,goods_class');
            if(!$data['goods_id'])
            {
                return $this->error('商品信息错误');
            }
            $data['goods_name'] = $goodsinfo['goods_name'];
            $data['goods_img'] = $goodsinfo['image_url'];
            $data['goods_class'] = $goodsinfo['goods_class'];
            $model = new groupModel();
            if($id)
            {   
                $res = $model->savegroup($data,['group_id'=>$id]);
            }
            else
            {   
                $data['create_time'] = time();
                $res = $model->insert($data);
            }
            
            if ($res){
                return $this->success('保存成功','group/groupList');
            }

        }
        $goods = new GoodsModel();
        $goodslist = $goods->getList(['goods_state'=>1,'is_prize'=>0],'id,goods_name');
        
        $this->assign('goodslist', $goodslist);
        $times = date('Y-m-d H:i:00');
        $this->assign('times', $times);
        $this->assign('meta_title','添加拼团');
        return $this->view->fetch('add');
    }
    //修改秒杀活动
    public function editGroup()
    {  
        
        $id = input('id');
        $data = [];
        $data['status'] = input('status');
        $model = new groupModel();
        $info = $model->getInfo(['group_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        // if($info['status'] == 1)
        // {   
        //     $time = time();
        //     if($info['signup_begin_time'] < $time || info['group_begin_time'] < $time)
        //     {
        //         return $this->error('已开始的活动不能修改');
        //     }
            
        // }
        //$data['update_time'] = date('Y-m-d H:i:s');
        $res = $model->savegroup($data,['group_id'=>$id]);
        if(!$res)
        {
            return $this->error('修改失败');
        }
        return $this->success('保存成功','group/groupList');
    }
    
    //删除秒杀活动
    public function delGroup()
    {  
        
        $id = input('id');
        $data = [];
       
        $model = new groupModel();
        $info = $model->getInfo(['group_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        if($info['status'] != 0)
        {
            return $this->error('已上架活动不能删除');
        }
        $data['is_delete'] = 1;
        //$data['update_time'] = date('Y-m-d H:i:s');
        $res = $model->savegroup($data,['group_id'=>$id]);
        if(!$res)
        {
            return $this->error('删除失败');
        }
        return $this->success('删除成功','group/groupList');
    }
    
     //修改活动页面
    public function edit()
    {  
        
        $id = input('id');
        $data = [];
       
        $model = new groupModel();
        $info = $model->getInfo(['group_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        $info['begin_time']  = date('Y-m-d H:i:s',$info['begin_time']);
        $info['end_time']    = date('Y-m-d H:i:s',$info['end_time']);
        if(input('is_copy') && input('is_copy') == 1)
        {
            $info['group_id'] = '';
        }
        $this->assign('info', $info);
        $goods = new GoodsModel();
        $goodslist = $goods->getList(['goods_state'=>1,'is_prize'=>0],'id,goods_name');
        $this->assign('meta_title','修改拼团');
        $this->assign('goodslist', $goodslist);
        return $this->view->fetch();
    }
    
    //发货管理
    public function groupShip()
    {   
        isset($_GET['goodsname'])?$where['goods_name'] = $_GET['goodsname']:'';
        isset($_GET['status'])?$where['order_status'] = $_GET['status']:'';
        $where['is_delete'] = 0;
        $where['ship_type'] = 1;
        
        $list = Db::name('group_order')->where($where)->paginate(20);
        //echo Db::name('group')->getLastSql();die;
        $users = new Users();
        $goods = new GoodsModel();
        foreach ($list as $k=>$v)
        {   
            switch ($v['order_status']) {
                case 0:
                    $stata_name = '未支付';
                    break;
                case 1:
                    $stata_name = '待发货';
                    break;
                case 2:
                    $stata_name = '已发货';
                    break;
                case 3:
                    $stata_name = '已签收';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
            
            $usersinfo = $users->getInfo(['users_id'=>$v['users_id']],'username');
            $v['username'] = $usersinfo['username'];
            $v['state_name'] = $stata_name;
            $goodsinfo = $goods->getInfo(['id'=>$v['goods_id']],'image_url,goods_price');
            $v['goods_price'] = $goodsinfo['goods_price'];
            $v['goods_img'] = $goodsinfo['image_url'];
            $v['create_time']  = $v['create_time']?date('Y-m-d H:i:s',$v['create_time']):'';
            $v['ship_time']  = $v['ship_time']?date('Y-m-d H:i:s',$v['ship_time']):'';
            $v['receipt_time'] = $v['receipt_time']?date('Y-m-d H:i:s',$v['receipt_time']):'';
            $list[$k] = $v;
        }
        $this->assign('meta_title','发货列表');
        $this->assign('list', $list);
        return $this->view->fetch('ship');
    }
    
    
     //发货
    public function delivery()
    {  
        
        $id = input('id');
        $data['logistics_company'] = input('logisticsCompany');
        $data['logistics_no'] = input('logisticsNo');
        
        if(!$id || !$data['logistics_company'] || !$data['logistics_no'])
        {
            return ['code'=>1,'msg'=>'缺少参数'];
        }
        $model = new GroupOrder();
        $info = $model->getInfo(['order_id'=>$id],'order_id');
        if(!$info)
        {
            return ['code'=>1,'msg'=>'id错误'];
        }
        $data['order_status'] = 2;
        $data['ship_time'] = time();
        $res = $model->saveData($data,['order_id'=>$id]);
        if(!$res)
        {
            return ['code'=>1,'msg'=>'保存失败'];
        }
       
        return ['code'=>0,'msg'=>'发货成功'];
    }
    
    
    //核销码列表
    public function verify()
    {   
        $where = [];
        isset($_GET['goodsname'])?$where['goods_name'] = $_GET['goodsname']:'';
        isset($_GET['status'])?$where['is_verify '] = $_GET['status']:'';
        
        $list = Db::name('verify')->where($where)->paginate(20);
        //echo Db::name('group')->getLastSql();die;
        $users = new Users();
        $goods = new GoodsModel();
        $shop = new Shop();
        $order = new GroupOrder();
        foreach ($list as $k=>$v)
        {   
            switch ($v['is_verify']) {
                case 0:
                    $stata_name = '未核销';
                    break;
                case 1:
                    $stata_name = '已核销';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
            
            $usersinfo = $users->getInfo(['users_id'=>$v['users_id']],'username');
            $v['username'] = $usersinfo['username'];
            $v['state_name'] = $stata_name;
            $orderinfo = $order->getInfo(['order_no'=>$v['order_no']],'goods_id,order_price');
            $v['order_price'] = $orderinfo['order_price'];
            $goodsinfo = $goods->getInfo(['id'=>$orderinfo['goods_id']],'goods_name,image_url,goods_price');
            $v['goods_name'] = $goodsinfo['goods_name'];
            $v['goods_price'] = $goodsinfo['goods_price'];
            $v['goods_img'] = $goodsinfo['image_url'];
            $v['create_time']  = $v['create_time']?date('Y-m-d H:i:s',$v['create_time']):'';
            $v['verify_time']  = $v['verify_time']?date('Y-m-d H:i:s',$v['verify_time']):'';
            if($v["shop_id"])
            {
                $shopinfo = $shop->getInfo(['shop_id'=>$v['shop_id']],'shop_name');
                $v['shop_name'] = $shopinfo['shop_name'];
            }else
            {
                $v['shop_name'] = '';
            }
            
            $list[$k] = $v;
        }
        $this->assign('meta_title','核销码列表');
        $this->assign('list', $list);
        return $this->view->fetch();
    }
    

}