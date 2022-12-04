<?php
namespace app\admin\controller;
use app\common\controller\Adminbase;
use think\Db;
use think\Request;
use app\api\model\GroupOrder;
use app\api\model\UsersWithdraw;
use app\admin\model\Users;
use app\api\model\GroupTeam;
use app\api\model\UsersOrder;
use app\api\model\Verify;
use app\admin\model\Version;
use app\api\model\DigitalExchange;
use app\shop\model\Shop;
/**
 * 
 * 后台权限管理
 */
class Rule extends AdminBase{

//******************权限***********************

    /*权限列表*/
    public function rule_list(){
        $data=Db::name('auth_rule')->getTreeData('tree','id','title');
        $assign=array(
            'data'=>$data
            );
        $this->assign($assign);
        $this->assign('meta_title','权限管理');
        return $this->fetch();
    }

    /**
     * 添加权限
     */
    public function add(){
        $data=input('post.');
        unset($data['id']);
        $result=Db::name('auth_rule')->insert($data);
        if ($result) {
            $this->success('添加成功','Admin/Rule/rule_list');
        }else{
            $this->error('添加失败');
        }
    }

    /**
     * 修改权限
     */
    public function edit(){
        $data=input('post.'); 
        $info=['title'=>$data['title'],'name'=>$data['name']];
        $result=Db::name('auth_rule')->where(["id"=>$data['id']])->update($info);
        // $result=\app\admin\model\Admin::change(["id"=>$data['id']],$info);
        if ($result) {
            $this->success('修改成功','Admin/Rule/rule_list');
        }else{
            $this->error('您没有做任何修改');
        }
    }

    /**
     * 删除权限
     */
    public function delete($id){
        $map=array(
            'id'=>$id
            );
        $result=Db::name('auth_rule')->delete($map);
        if($result){
            $this->success('删除成功','Admin/Rule/rule_list');
        }else{
            $this->error('请先删除子权限');
        }

    }

    /**
     * 角色列表
     */
    public function rule_group(){
        $data=Db::name('auth_group')->select();
        $assign=array(
            'data'=>$data
            );
        $this->assign($assign);
        $this->assign('meta_title','角色列表');
        return $this->fetch();
    }


     /**
     * 添加角色
     */
    public function add_group(){
        $data=input('post.');
        
        unset($data['id']);
        $result=Db::name('auth_group')->insert($data);
        if ($result) {
            $this->success('添加成功','Admin/Rule/rule_group');
        }else{
            $this->error('添加失败');
        }
    }

    /**
     * 修改角色
     */
    public function edit_group(){
        $data=input('post.');
        $result=Db::name('auth_group')->where(["id"=>$data['id']])->update(['title'=>$data['title']]);
        // $result=Db::name('auth_group')->editData($map,$data);
        if ($result) {
            $this->success('修改成功','Admin/Rule/rule_group');
        }else{
            $this->error('您没有做任何修改');
        }
    }

    /**
     * 删除角色
     */
    public function delete_group($id){
        if ($id==1) {
            $this->error('该分组不能被删除');
        }
        $map=array(
            'id'=>$id
            );
        $result=Db::name('auth_group')->where($map)->delete();
        if ($result) {
            $this->success('删除成功','Admin/Rule/rule_group');
        }else{
            $this->error('删除失败');
        }
    }


    /**
     * 分配权限
     */
    public function rule_distribution($id){
        if(Request::instance()->post()){
            $data=input('post.');
            $datas['rules']=implode(',', $data['rule_ids']);
            $result=Db::name('auth_group')->where(['id'=>$data['id']])->update($datas);
            // $result=Db::name('auth_group')->editData($map,$data);
            if ($result) {
                $this->success('操作成功','Admin/Rule/rule_group');
            }else{
                $this->error('操作失败');
            }
        }else{
            $group_data=Db::name('auth_group')->where(array('id'=>$id))->find();
            $group_data['rules']=explode(',', $group_data['rules']);
            // 获取规则数据
            $rule_data=Db::name('auth_rule')->getTreeData('level','id','title');
            $assign=array(
                'group_data'=>$group_data,
                'rule_data'=>$rule_data
                );
            // dump($group_data);
            $this->assign($assign);
            $this->assign('meta_title','分配权限');
            return $this->fetch();
        }
    }
    
    
    public function statistics()
    {
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
        $data['all_balance_money'] = $users->getDataSum(['is_virtual'=>0],'balance_money');//,'reg_time'=>['lt',1655654400]
        $data['all_point'] = $users->getDataSum(['is_virtual'=>0],'point');
        $data['all_partner_money'] = $users->getDataSum(['is_virtual'=>0],'partner_money');
        $data['all_users_num'] = $users->getUsersNum();
        $data['today_users_num'] = $users->getUsersNum(['reg_time'=>['gt',$endYesterday]]);
        $data['all_partner_num'] = $users->getUsersNum(['is_partner'=>1]);
        $data['yesterday_users_num'] = $users->getUsersNum(['reg_time'=>['between',"$beginYesterday,$endYesterday"]]);
        $data['online_num'] = $users->getUsersNum(['last_operate_time'=>['gt',time()-600]]);
        $data['partner_withdra_num'] = $users->getUsersNum(['is_partner'=>1,'direct_users_num'=>0]);
        $data['partner_withdra_money'] = $users->getDataSum(['is_partner'=>1,'direct_users_num'=>0],'partner_withdraw_amount');
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
        $verify = new Verify();
        $data['all_verify_num'] = $verify->getCount(['is_verify'=>1]);
        $data['verify_all_num'] = $verify->getCount(['is_verify'=>0,'users_id'=>['not in',$id_list]]);
        $data['yesterday_verify_num'] = $verify->getCount(['is_verify'=>1,'verify_time'=>['between',"$beginYesterday,$endYesterday"]]);
        $data['trade_verify_num'] = $verify->getCount(['is_verify'=>0,'trade_status'=>1]);
        $data['verify_exchange_num'] = $verify->getCount(['is_verify'=>3,'users_id'=>['not in',$id_list]]);
        $data['all_guoqi_num'] = $verify->getCount(['is_verify'=>2,'users_id'=>['not in',$id_list]]);
        $exchange = new DigitalExchange();
        $data['yesterday_exchange_num'] = $exchange->getDataSum(['status'=>2,'finish_time'=>['between',"$beginYesterday,$endYesterday"]],'num');
        $data['today_exchange_num'] = $exchange->getDataSum(['status'=>2,'finish_time'=>['gt',$endYesterday]],'num');
        //$data['all_verify_num'] = $verify->getCount(['is_verify'=>0,'users_id'=>['in',$id_list]]);
        //var_dump($data);die;
        $shop = new Shop();
        $data['shop_account'] = $shop->getDataSum(['is_delete'=>0],'account');
        $this->assign('meta_title','首页');
        $this->assign('data',$data);
		$this->assign('config',$config);
		return $this->view->fetch();
    }
    
    

}
