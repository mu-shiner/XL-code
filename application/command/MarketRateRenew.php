<?php

namespace app\command;

use think\console\Command; //必须引入
use think\console\Input; //必须引入
use think\console\Output; //必须引入
use think\Db;
use think\Log;
use think\Cache;


/**
 * 转让市场汇率变动,1小时保存1次
 * @date: 2022/6/17 10:30
 * @author: 
 * @version: 1.1
 */
class MarketRateRenew extends Command  //必须继承
{

    protected function configure()
    {
        // 指令配置
        $this->setName('MarketRateRenew');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        
        $all_price = Db::name('trade_market')->where(['status'=>1])->sum('price');
        $all_num = Db::name('trade_market')->where(['status'=>1])->sum('num');
        if($all_price && $all_num)
        {
            $rate = round($all_price/$all_num,2);
        }
        else
        {
            $rate = 300;
        }
        Db::name('market_rate')->insert(['rate'=>$rate,'create_time'=>time()]);
    }    
   
}