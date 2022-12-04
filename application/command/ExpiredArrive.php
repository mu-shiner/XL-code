<?php

namespace app\command;

use think\console\Command; //必须引入
use think\console\Input; //必须引入
use think\console\Output; //必须引入
use think\Db;
use app\api\model\TradeMarket;
use app\api\model\Verify;
use think\Log;
use app\api\model\Pay;


/**
 * 通用券转让时间结束，自动下架
 * @date: 
 * @author: 
 * @version: 1.1
 */
class ExpiredArrive extends Command  //必须继承
{

    protected function configure()
    {
        // 指令配置
        $this->setName('ExpiredArrive');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        $data = [
                'action' => 'ExpiredArrive',
                'datatime' => date('Y-m-s h:i:s', time())
            ];
        Db::name('jiaoben_log')->insert($data);
        $time = time();
        $beginTime = $time;
        $loop = true;
        do {
            $model = new TradeMarket();
            //过期的转让
            $list = $model->getTradeMarketList(['status'=>1,'end_time'=>['lt',time()]]);
            
            $verify = new Verify();
            //过期的券
            $arr = $verify->getList(['is_verify'=>0,'trade_status'=>0,'expired_time'=>['between','1,'.time()]]);
            Db::startTrans();
            try {
                
                if($list)
                {  
                    foreach ($list as $v)
                    {   
                        //状态变失败
                        $model->saveData(['status'=>3],['market_id'=>$v['market_id']]);
                        //echo $model->getLastsql();
                        $id_list = explode(',',$v['verify_list']);
                        //对应核销券状态变无交易
                        $verify->saveVerify(['trade_status'=>0],['id'=>['in',$id_list]]);
                        $userinfo = Db::name('users')->where(['users_id'=>$v['users_id']])->field('wx_openid')->find();
                        if($userinfo['wx_openid'])
                        {   
                            $ctime = date('Y-m-d H:i:s',$v['create_time']);
                            $url = 'http://tc.om/h5/page_my/couponsTrade';
                            $str = '很遗憾,您的通用券转让失败';
                            $this->fasong($userinfo['wx_openid'],$v['market_id'],$v['price'],$ctime,$url,$str);
                        }
                    }
                }
                if($arr)
                {   
                    $id_arr = [];
                    foreach ($arr as $item)
                    {
                        $id_arr[] = $item['id'];
                    }
                    //核销券状态变过期
                    $verify->saveVerify(['is_verify'=>2],['id'=>['in',$id_arr]]);
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                echo $e->getMessage();
            }

            if (time() - $beginTime >= 300) {//执行5分钟，重启一次
                exit;
            }

            $r = mt_rand(5, 10);
            sleep($r);

        } while ($loop);
    }
    
    
    // {{first.DATA}}
    // 订单编号：{{keyword1.DATA}}
    // 转让金额：{{keyword2.DATA}}
    // 登记时间：{{keyword3.DATA}}
    // 失败原因：{{keyword4.DATA}}
    // {{remark.DATA}}
    public function fasong($openid,$orderno,$price,$ctime,$url,$str)
    {   
        $pay = new Pay();
        $data = array(
            "touser"=>$openid,
            "template_id"=>$pay->TradeFailTemplateId,
            "url" => $url,
            "data" => array(
                "first" => array(
                    "value"=>"通用券转让失败",
                ),
                "keyword1" => array(
                    "value"=>$orderno
                ),
                "keyword2" => array(
                    "value"=>$price
                ),
                "keyword3" => array(
                    "value"=>$ctime
                ),
                "keyword4" => array(
                    "value"=>'转让时间已到期'
                ),
                "remark" => array(
                    "value"=>$str
                ),
            )
        );
        $res = $pay->template($data);
        return $res;
    }
    
}