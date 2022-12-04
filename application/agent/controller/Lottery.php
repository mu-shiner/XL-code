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
use app\admin\model\Lottery as LotteryModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Users;
use app\api\model\GroupOrder;
use app\api\model\UsersLottery;

class Lottery extends AdminBase
{
    //抽奖列表
    public function index()
    {   
        
        $list = Db::name('lottery')->paginate(20);
        
        foreach ($list as $k=>$v)
        {   
            switch ($v['is_open']) {
                case 0:
                    $stata_name = '已关闭';
                    break;
                case 1:
                    $stata_name = '已开启';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
            $v['state_name'] = $stata_name;
            $list[$k] = $v;
        }
        $this->assign('meta_title','抽奖列表');
        $this->assign('list', $list);
        return $this->view->fetch('index');
    }

    /**
     * 添加拼团
     */
    public function lotteryAdd()
    {   
        // echo("<pre>");
        // var_dump($_POST);echo("</pre>");die;
        //判断是否表单提交数据
        if (empty($_POST)){return $this->error('数据不能为空');}
            
            $data = [];
            $id = isset($_POST['lottery_id'])?$_POST['lottery_id']:'';
            $data['begin_time'] = isset($_POST['begin_time'])?$_POST['begin_time']:'';//活动开始时间
            $data['end_time']   = isset($_POST['end_time'])?$_POST['end_time']:'';//活动结束时间
            $goods_id  = isset($_POST['goods_id'])?$_POST['goods_id']:'';//商品id
            $goods_num = isset($_POST['goods_num'])?$_POST['goods_num']:'';//商品数量
            
            $goods = new GoodsModel();
            
            $model = new LotteryModel();
            
            $arr = [];
            
            foreach ($goods_id as $k => $v)
            {   
                $goodsinfo = $goods->getInfo(['id'=>$v], 'goods_name,image_url,goods_class');
                $arr[$k]['goods_id'] = $v;
                $arr[$k]['goods_name'] = $goodsinfo['goods_name'];
                $arr[$k]['goods_img'] = $goodsinfo['image_url'];
                $arr[$k]['goods_num'] = $goods_num[$k];
                $arr[$k]['surplus_num'] = $goods_num[$k];
                $arr[$k]['goods_class'] = $goodsinfo['goods_class'];
                $arr[$k]['create_time'] = time();
            }
            // echo("<pre>");
            // var_dump($arr);
            // echo("</pre>");die;
            foreach ($arr as $key => $value)
            {
                $model->saveLotteryGoods($value,['lottery_id'=>$id,'goods_level'=>$key+1]);
                //echo $model->getLastSql();
                // echo("<hr/>");
            }
            
            $res = $model->saveLottery($data,['lottery_id'=>$id]);
            if (!$res){
                return $this->error('保存失败');
            }

            return $this->success('保存成功','Lottery/index');
        
        
        // $this->assign('goodslist', $goodslist);
        // $times = date('Y-m-d H:i:00');
        // $this->assign('times', $times);
        // return $this->view->fetch('add');
    }
    //修改秒杀活动
    public function editLottery()
    {  
        
        $id = input('id');
        $data = [];
        $data['is_open'] = input('is_open');
        $model = new LotteryModel();
        $info = $model->getInfo(['lottery_id'=>$id]);
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
        $res = $model->saveLottery($data,['lottery_id'=>$id]);
        if(!$res)
        {
            return $this->error('修改失败');
        }
        return $this->success('保存成功','Lottery/index');
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
       
        $model = new LotteryModel();
        $info = $model->getInfo(['lottery_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        
        $list = $model->getGoodsList(['lottery_id'=>$id]);
        $this->assign('list', $list);
        $this->assign('info', $info);
        $goods = new GoodsModel();
        $goodslist = $goods->getList(['goods_state'=>1,'is_prize'=>1],'id,goods_name');
        
        $this->assign('goodslist', $goodslist);
        
        $this->assign('meta_title','抽奖编辑');
        return $this->view->fetch();
    }
    
    //发货管理
    public function lotteryShip()
    {   
        //isset($_GET['goodsname'])?$where['goods_name'] = $_GET['goodsname']:'';
        isset($_GET['status'])?$where['status'] = $_GET['status']:'';
        //$where['is_delete'] = 0;
        $where['is_get'] = 1;
        
        $list = Db::name('users_lottery')->where($where)->paginate(20);
        //echo Db::name('group')->getLastSql();die;
        $users = new Users();
        $goods = new GoodsModel();
        foreach ($list as $k=>$v)
        {   
            switch ($v['status']) {
                case 0:
                    $stata_name = '未填写';
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
            // $goodsinfo = $goods->getInfo(['id'=>$v['goods_id']],'image_url,goods_price');
            // $v['goods_price'] = $goodsinfo['goods_price'];
            // $v['goods_img'] = $goodsinfo['image_url'];
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
        $model = new UsersLottery();
        $info = $model->getInfo(['id'=>$id],'status');
        if(!$info)
        {
            return ['code'=>1,'msg'=>'id错误'];
        }
        if($info['status'] != 1)
        {
            return ['code'=>1,'msg'=>'状态错误'];
        }
        $data['status'] = 2;
        $data['ship_time'] = time();
        $res = $model->saveInfo($data,['id'=>$id]);
        if(!$res)
        {
            return ['code'=>1,'msg'=>'保存失败'];
        }
       
        return ['code'=>0,'msg'=>'发货成功'];
    }
    

}