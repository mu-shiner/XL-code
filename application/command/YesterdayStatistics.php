<?php

namespace app\command;

use think\console\Command; //必须引入
use think\console\Input; //必须引入
use think\console\Output; //必须引入
use think\Db;
use think\Log;
use think\Cache;
use app\api\model\UsersBill;
use app\api\model\Statistics;


/**
 * 统计用户昨日收入
 * @date: 2022/6/28 10:30
 * @author: 
 * @version: 1.1
 */
class YesterdayStatistics extends Command  //必须继承
{

    protected function configure()
    {
        // 指令配置
        $this->setName('YesterdayStatistics');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        $bill = new UsersBill();
        $where = [
                'bill_data' => ['gt',0],
                'bill_type' => ['in',['balance_money','point','partner_money']],
                'create_time' => ['between',"$beginYesterday,$endYesterday"]
            ];
        
        $list = $bill->getBillList($where);
        
        $model = new Statistics();
        foreach ($list as $v)
        {
            $date = date('Ymd',$v['create_time']);
            $info = $model->getInfo(['date'=>$date,'users_id'=>$v['users_id']]);
            
            if($info)
            {
                $model->saveNumInc(['date'=>$date,'users_id'=>$v['users_id']],$v['bill_type'],$v['bill_data']);
            }
            else
            {   
                $data = ['date'=>$date,'users_id'=>$v['users_id'],$v['bill_type']=>$v['bill_data']];
                $model->insert($data);
            }
        }
        
    }    
   
}