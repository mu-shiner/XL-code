<?php

namespace app\shop\controller;

use app\common\controller\Apibase;
use think\Cache;
use app\shop\model\Verify as VerifyModel;
use app\shop\model\Shop;
use app\shop\model\ShopAccount;
use app\api\model\GroupOrder;
use app\api\model\Goods;

class Verify extends Apibase
{   
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $token = $this->checkShopToken();
     
        if(!$this->shop_id)
        {  
            echo $this->error_req(100, '用户未登录');exit;
        }
    }
   /**
     * 核销台
     * @return mixed
     */
    public function verifyCard()
    {   
        $shop = new Shop();
        $shopinfo = $shop->getInfo(['shop_id'=>$this->shop_id],'shop_id,shop_name,check_type');
        $verify_code = isset($this->params['verify_code']) ? $this->params['verify_code'] : '';
        $verify_model = new VerifyModel();
      
        $res = $verify_model->getInfo(["verify_code"=>$verify_code,'check_type'=>$shopinfo['check_type']]);
        if(!$res)
        {
            return $this->error_req(100, '找不到核销码信息!');
        }
        if($res['is_verify'] == 1)
        {
            return $this->error_req(100, '核销码已被使用');
        }
        return $this->success_req($res);
    }
    
    
    /**
     * 核销
     */
    public function verify()
    {   
        $this->write_log($this->params);
        $shop = new Shop();
        $shop_info = $shop->getInfo(['shop_id'=>$this->shop_id],'shop_id,shop_name,shop_status,logo,check_type,settlement_price,account');
        
        $verify_code = isset($this->params['verify_code']) ? $this->params['verify_code'] : '';
        $verify_model = new VerifyModel();
        $verify_info = $verify_model->getInfo(["verify_code"=>$verify_code]);
        if(!$verify_info)
        {
            return $this->error_req(100, '找不到核销码信息!');
        }
        if($verify_info['is_verify'] == 1)
        {
            return $this->error_req(100, '核销码已被使用');
        }
        if($shop_info['check_type'] != $verify_info['check_type'])
        {
            return $this->error_req(100, '核销类型不符合!');
        }
        model('verify')->startTrans();
        try {
            $data = [
                    'is_verify' => 1,
                    'verify_time' => time(),
                    'shop_id' =>$shop_info['shop_id'],
                    'shop_name' =>$shop_info['shop_name'],
                ];
            $res = $verify_model->saveVerify($data,['id'=>$verify_info['id']]);
            if(!$res)
            {
                return $this->error_req(100, '核销失败');
            }
            //店铺增加结算价格
            $shop->ShopNumInc(['shop_id'=>$this->shop_id],'account',$shop_info['settlement_price']);
            $account = new ShopAccount();
            $arr = [    
                    'account_no' => $verify_code,
                    'shop_id' => $this->shop_id,
                    'account_type' => 1, //类型1核销2提现
                    'account_data' => $shop_info['settlement_price'],
                    'type_name' => '核销结算',
                    'create_time' => time(),
                    'current_money' => $shop_info['account']
                ];
            //记录账户明细
            $account->addInfo($arr);
            
            model('verify')->commit();
            return $this->success_req('核销成功');
        } catch (\Exception $e) {
            
            model('verify')->rollback();
            return $this->error_req(100, $e->getMessage());
        }
    }
    
    
     /**
     * 核销记录
     */
    public function verifyList()
    {
        $verify_model = new VerifyModel();
        $list = $verify_model->getList(["shop_id"=>$this->shop_id]);
        $order = new GroupOrder();
        $goods = new Goods();
        foreach ($list as &$v)
        {
            $orderinfo = $order->getInfo(['order_no'=>$v['order_no']],'goods_id');
            $goodsinfo = $goods->getInfo(['id'=>$orderinfo['goods_id']],'goods_name,image_url');
            $v['goods_name'] = $goodsinfo['goods_name'];
            $v['image_url'] = $goodsinfo['image_url'];
        }
        return $this->success_req($list);
    }
    
    
   
    
}
