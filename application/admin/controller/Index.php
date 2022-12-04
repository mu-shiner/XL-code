<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use think\Db;
use app\api\model\GroupOrder;
use app\api\model\UsersWithdraw;
use app\admin\model\Users;
use app\api\model\GroupTeam;
use app\api\model\UsersOrder;

class Index extends Adminbase
{
    public function index()
    {
        // $version = Db::query('SELECT VERSION() AS ver');
        // $config  = [
        //     'url'             => $_SERVER['HTTP_HOST'],
        //     'document_root'   => $_SERVER['DOCUMENT_ROOT'],
        //     'server_os'       => PHP_OS,
        //     'server_port'     => $_SERVER['SERVER_PORT'],
        //     'server_soft'     => $_SERVER['SERVER_SOFTWARE'],
        //     'php_version'     => PHP_VERSION,
        //     // 'mysql_version'   => $version[0]['ver'],
        //     'max_upload_size' => ini_get('upload_max_filesize')
        // ];
        $data = [];
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        $users = new Users();
        $user_list = $users->getList(['is_virtual'=>1],'users_id');
        $id_list = [];
        foreach ($user_list as $v)
        {
            $id_list[] = $v['users_id'];
        }
        $order = new GroupOrder();
        $data['all_order_price'] = $order->getDataSum(['status'=>1,'users_id'=>['not in',$id_list]],'order_price');
        $data['yesterday_order_price'] = $order->getDataSum(['status'=>1,'pay_time'=>['between',"$beginYesterday,$endYesterday"],'users_id'=>['not in',$id_list]],'order_price');
        $data['today_order_price'] = $order->getDataSum(['status'=>1,'pay_time'=>['gt',$endYesterday],'users_id'=>['not in',$id_list]],'order_price');
        $data['all_order_money'] = $order->getDataSum(['status'=>1,'pay_type'=>['neq',0],'users_id'=>['not in',$id_list]],'order_price');
        $data['today_order_money'] = $order->getDataSum(['status'=>1,'pay_type'=>['neq',0],'pay_time'=>['gt',$endYesterday],'users_id'=>['not in',$id_list]],'order_price');
        $data['all_balance_money'] = $users->getDataSum(['is_virtual'=>0],'balance_money');
        $data['all_point'] = $users->getDataSum(['is_virtual'=>0],'point');
        $data['all_partner_money'] = $users->getDataSum(['is_virtual'=>0],'partner_money');
        $data['all_users_num'] = $users->getUsersNum();
        $data['today_users_num'] = $users->getUsersNum(['reg_time'=>['gt',$endYesterday]]);
        $data['all_partner_num'] = $users->getUsersNum(['is_partner'=>1]);
        $data['yesterday_users_num'] = $users->getUsersNum(['reg_time'=>['between',"$beginYesterday,$endYesterday"]]);
        $users_withdraw = new UsersWithdraw();
        $data['all_point_withdra'] = $users_withdraw->getWithdrawSum(['status'=>2,'type'=>1,'users_id'=>['not in',$id_list]],'withdraw_price');
        $data['all_money_withdra'] = $users_withdraw->getWithdrawSum(['status'=>2,'type'=>2,'users_id'=>['not in',$id_list]],'withdraw_price');
        $data['today_point_withdra'] = $users_withdraw->getWithdrawSum(['status'=>2,'type'=>1,'users_id'=>['not in',$id_list],'create_time'=>['gt',$endYesterday]],'withdraw_price');
        $data['today_money_withdra'] = $users_withdraw->getWithdrawSum(['status'=>2,'type'=>2,'users_id'=>['not in',$id_list],'create_time'=>['gt',$endYesterday]],'withdraw_price');
        $team = new GroupTeam();
        $data['all_team_num'] = $team->getTeamNum(['status'=>1]);
        $data['shi_team_num'] = $team->getTeamNum(['status'=>1,'goods_class'=>1]);
        $data['xu_team_num'] = $team->getTeamNum(['status'=>1,'goods_class'=>2]);
        $data['yesterday_team_num'] = $team->getTeamNum(['status'=>1,'success_time'=>['between',"$beginYesterday,$endYesterday"]]);
        $data['today_team_num'] = $team->getTeamNum(['status'=>1,'success_time'=>['gt',$endYesterday]]);
        $user_order = new UsersOrder();
        $data['all_user_price'] = $user_order->getDataSum(['type'=>0,'status'=>1,'users_id'=>['not in',$id_list]],'order_price');
        $data['today_user_price'] = $user_order->getDataSum(['type'=>0,'status'=>1,'users_id'=>['not in',$id_list],'pay_time'=>['gt',$endYesterday]],'order_price');
        //var_dump($data);die;
        
        $this->assign('meta_title','首页');
        $this->assign('data',$data);
		$this->assign('config',$config);
		return $this->view->fetch();
    }
    
    public function ceshi()
    {
        return $this->view->fetch();
    }


    /**
     * 退出
     */
    // http://127.0.0.1/public/admin/index/logout
    public function logout(){
        session('user',null);
        $this->success('退出成功、前往登录页面','Home/Index/index');
    }
}
