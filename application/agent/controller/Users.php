<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10
 * Time: 22:35
 */

namespace app\admin\controller;

use app\admin\model\Attachment;
use app\common\controller\Adminbase;

use think\Db;
use app\admin\model\Users as UsersModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\UsersConfig;


class Users extends  AdminBase
{
    /**
     * 初始化
     */
    // public function _initialize()
    // {

    // }
    //会员列表
    public function usersList()
    {   
        isset($_GET['username'])?$where['username'] = $_GET['username']:'';
        $where['is_delete'] = 0;
        
        $list = Db::name('users')->where($where)->paginate(20);
       
        foreach ($list as $k => &$v)
        {   
            switch ($v['status']) {
                case 0:
                    $stata_name = '已禁用';
                    break;
                case 1:
                    $stata_name = '正常';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
            switch ($v['is_partner']) {
                case 0:
                    $partner = '否';
                    break;
                case 1:
                    $partner = '是';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
            $v['partner'] = $partner;
            $v['status_name'] = $stata_name;
            $v['reg_time']  = date('Y-m-d H:i:s',$v['reg_time']);
            $list[$k] = $v;
        }
        $this->assign('meta_title','会员列表');
        $this->assign('list', $list);
        return $this->view->fetch('index');
    }

    
    //修改会员状态
    public function editUsers()
    {  
        $id = input('id');
        $data = [];
        input('status') !== null?$data['status'] = input('status'):'';
        input('is_partner') !== null?$data['is_partner'] = input('is_partner'):'';
        
        $model = new UsersModel();
        $info = $model->getInfo(['users_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        
        $res = $model->saveUser($data,['users_id'=>$id]);
        if(!$res)
        {
            return $this->error('修改失败');
        }
        return $this->success('保存成功','Users/usersList');
    }
    
    //删除会员
    public function delUsers()
    {  
        
        $id = input('id');
        $data = [];
       
        $model = new usersModel();
        $info = $model->getInfo(['users_id'=>$id]);
        
        $data['is_delete'] = 1;
        //$data['update_time'] = date('Y-m-d H:i:s');
        $res = $model->saveusers($data,['users_id'=>$id]);
        if(!$res)
        {
            return $this->error('删除失败');
        }
        return $this->success('删除成功','Users/UsersList');
    }
    //分佣配置
    public function usersConfigList()
    {    
       $config = new UsersConfig();
       $list = $config->getList();
       foreach ($list as &$v)
       {
           switch ($v['config_type'])
           {
               case '1':
                   $v['config_name'] = '拼团成功';
                   break;
               
               default:
                   // code...
                   break;
           }
       }
       $this->assign('meta_title','分佣配置');
       $this->assign('list', $list);
       return $this->view->fetch('configlist');
    }
   
    //修改配置页面
    public function configedit()
    {   
        $model = new UsersConfig();
        if (!empty($_POST)){
            $data = [];
            $config_id = isset($_POST['config_id'])?$_POST['config_id']:'';
            $data['rate_one']   = isset($_POST['rate_one'])?$_POST['rate_one']:'';//一级分销比例
            $data['rate_two']   = isset($_POST['rate_two'])?$_POST['rate_two']:0;//二级分销比例
            $data['rate_three'] = isset($_POST['rate_three'])?$_POST['rate_three']:'';//三级分销比例
            $data['update_time'] = time();
            $res = $model->saveUsersConfig($data,['config_id'=>$config_id]);
            if ($res){
                return $this->success('保存成功','Users/usersConfigList');
            }
            
        }
        $id = input('id');
        
        
        $info = $model->getInfo(['config_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        switch ($info['config_type'])
        {
           case '1':
               $info['config_name'] = '拼团成功';
               break;
        //   case '2':
        //       $info['config_name'] = '秒杀出价';
        //       break;
           default:
               // code...
               break;
       }
       
        $this->assign('info', $info);
        $this->assign('meta_title','修改配置');
        return $this->view->fetch();
    }
    
    //提现配置
    public function withdrawConfigList()
    {    
       $config = new UsersConfig();
       $list = $config->getWithdrawList();
       foreach ($list as &$v)
       {
           switch ($v['config_type'])
           {
               case '1':
                   $v['config_name'] = '积分';
                   break;
               case '2':
                   $v['config_name'] = '余额';
                   break;
               default:
                   // code...
                   break;
           }
       }
       $this->assign('meta_title','提现配置');
       $this->assign('list', $list);
       return $this->view->fetch('withdrawconfiglist');
   }
   
    //修改配置页面
    public function withdrawconfigedit()
    {   
        $model = new UsersConfig();
        if (!empty($_POST)){
            $data = [];
            $config_id = isset($_POST['config_id'])?$_POST['config_id']:'';
            $data['withdraw_rate']  = isset($_POST['withdraw_rate'])?$_POST['withdraw_rate']:'';//一级分销比例
            $data['withdraw_fixed'] = isset($_POST['withdraw_fixed'])?$_POST['withdraw_fixed']:0;//二级分销比例
            
            $data['update_time'] = time();
            $res = $model->saveWithdrawConfig($data,['id'=>$config_id]);
            if ($res){
                return $this->success('保存成功','Users/withdrawConfigList');
            }
            
        }
        $id = input('id');
        $info = $model->getWithdrawInfo(['id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        switch ($info['config_type'])
       {
            case '1':
               $info['config_name'] = '积分';
               break;
            case '2':
               $info['config_name'] = '保证金';
               break;
            default:
               // code...
               break;
       }
       
        $this->assign('info', $info);
        $this->assign('meta_title','修改配置');
        return $this->view->fetch();
    }
    
    //会员提现列表
    public function usersWithdrawList()
    {   
        isset($_GET['type'])?$where['type'] = $_GET['type']:'';
        isset($_GET['withdraw_type'])?$where['withdraw_type'] = $_GET['withdraw_type']:'';
        $users = new UsersModel();
        if(isset($_GET['username']))
        {
            $info = $users->getInfo(['username'=>$_GET['username']],'users_id');
            if($info)
            {
                $where['users_id'] = $info['users_id'];
            }
            else
            {
                $where['users_id'] = 0;
            }
        }
      
        $where['is_delete'] = 0;
        
        $list = Db::name('users_withdraw')->where($where)->paginate(20);
        
        foreach ($list as $k => &$v)
        {   
            switch ($v['status']) {
                case -1:
                    $stata_name = '未通过';
                    break;
                case 0:
                    $stata_name = '审核中';
                    break;
                case 1:
                    $stata_name = '待财务审核';
                    break;
                case 2:
                    $stata_name = '审核通过';
                    break;
                default:
                    $stata_name = '';
                    break;
            }
            switch ($v['type'])
            {
                case '1':
                    $type_name = '积分';
                    break;
                case '2':
                    $type_name = '余额';
                    break;
                default:
                    $type_name = '';
                    break;
            }
            switch ($v['withdraw_type'])
            {
                case 'bank':
                    $entryType = '银行卡';
                    $accountNo = $v['bank_code'];
                break;
                case 'alipay':
                    $entryType = '支付宝';
                    $accountNo = $v['alipay_code'];
                    break;
                case 'wx':
                    $entryType = '微信';
                    $accountNo = $v['wechat'];
                    break;
                default:
                    
                    break;
            }
            $v['status_name'] = $stata_name;
            $v['type_name'] = $type_name;
            $v['entryType'] = $entryType;
            $v['accountNo'] = $accountNo;
            $v['create_time']  = date('Y-m-d H:i:s',$v['create_time']);
            $v['fail_time']  = date('Y-m-d H:i:s',$v['fail_time']);
            $users_info = $users->getInfo(['users_id'=>$v['users_id']],'username,avatar');
            $v['username'] = $users_info['username'];
            $list[$k] = $v;
        }
        $this->assign('meta_title','提现列表');
        $this->assign('list', $list);
        return $this->view->fetch('withdrawlist');
    }
    

}