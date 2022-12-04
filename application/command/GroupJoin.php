<?php

namespace app\command;

use think\console\Command; //必须引入
use think\console\Input; //必须引入
use think\console\Output; //必须引入
use think\Db;
use app\api\model\Group;
use think\Cache;
use think\Log;

/**
 * 自动加入团购
 * @date: 
 * @author: 
 * @version: 1.1
 */
class GroupJoin extends Command  //必须继承
{

    protected function configure()
    {
        // 指令配置
        $this->setName('GroupJoin');
        // 设置参数
    }

    protected function execute(Input $input, Output $output)
    {
        // $data = [
        //         'action' => 'GroupJoin',
        //         'datatime' => date('Y-m-s h:i:s', time())
        //     ];
        // Db::name('jiaoben_log')->insert($data);
        $time = time();
        $beginTime = $time;
        $loop = true;
        do {
            $redis = Cache::getHandler();
            //列出用户出价信息id
            $list = $redis->handler()->lRange('group_join_list', 0, -1);
            //Log::write($list);
            Db::startTrans();
            try {
                
                if($list)
                {  
                    foreach ($list as $v)
                    {   
                        $info = $redis->handler()->hGetAll($v);
                        if(!$info)
                        {
                            continue;
                        }
                        Log::write($info);
                        $model = new Group();
                        //处理用户参团
                        $res = $model->groupUsersJoin($info['group_id'],$info['users_id'],$info['order_no']);
                        if($res == -1)
            		    {   
            		        echo '系统繁忙,请稍后';
            		        continue;
            		    }
                        //删除对应的信息id
                        $redis->lRem('group_join_list', $v, 0);
                        //删除对应的拼团信息
                        $redis->del($v);
                    }
                }
                
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                echo $e->getMessage();
            }

            if (time() - $beginTime >= 120) {//执行5分钟，重启一次
                exit;
            }

            $r = mt_rand(1, 2);
            sleep($r);

        } while ($loop);
    }
    
    
   
}