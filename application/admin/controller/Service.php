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

use think\Exception;
use think\Log;
use think\Request;
use think\File;
use think\Db;
use app\admin\model\Service as ServiceModel;
use app\shop\model\Admin;


class Service extends AdminBase
{
    /**
     * 初始化
     */
    // public function _initialize()
    // {
        
    // }
    //服务中心列表
    public function index()
    {
        //$model = new ServiceModel();
        $where['is_delete'] = 0;
        
        $servicelist = Db::name('service')->where($where)->paginate(20);
        //$Servicelist = $model->getList();
        foreach ($servicelist as $k => &$v)
        {   
            // switch ($v['status']) {
            //     case 0:
            //         $stata_name = '待提交';
            //         break;
            //     case 1:
            //         $stata_name = '已通过';
            //         break;
            //     default:
            //         $stata_name = '';
            //         break;
            // }
            
            // $v['state_name'] = $stata_name;
           
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $servicelist[$k] = $v;
            //$v['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
        }
        $this->assign('meta_title','服务中心列表');
        $this->assign('servicelist', $servicelist);
        return $this->view->fetch('index');
    }

    /**
     * 服务中心添加
     */
    public function serviceAdd()
    {   
        $data = [];
        $id = isset($_POST['id'])?$_POST['id']:'';
        
        isset($_POST['service_name'])?$data['service_name']  = $_POST['service_name']:'';
        isset($_POST['username'])?$data['username'] = $_POST['username']:'';
        isset($_POST['password'])?$password = $_POST['password']:'';
        
        $model = new ServiceModel();
        $admin = new Admin();
        $serinfo = $model->getInfo(['service_name'=>$data['service_name']]);
        $admininfo = $admin->getInfo(['username'=>$data['username'],'app_module'=>'service']);
        $arr['username'] = $data['username'];
        $arr['password'] = md5($password);
        if($id)
        {   
            $info = $model->getInfo(['service_id'=>$id]);
            if(!$serinfo)
            {
                return $this->error('id错误');
            }
            if($serinfo['service_id'] != $id)
            {
                return $this->error('服务中心名称已存在');
            }
            if($admininfo['shop_id'] != $id)
            {
                return $this->error('管理员名称已存在');
            }
            
            $res = $model->saveService($data,['service_id'=>$id]);
            $admin->saveAdmin($arr,['app_module'=>'service','shop_id'=>$id]);
            
        }
        else
        {   
            if($serinfo)
            {
                return $this->error('服务中心名称已存在');
            }
            
            if($admininfo)
            {
                return $this->error('管理员名称已存在');
            }
            $data['create_time'] = time();
            $data['invitation_code'] = $model->setInvitationCode();
            $model->addService($data);
            $info = $model->getInfo($data);
            $arr['shop_id'] = $info['service_id'];
            $arr['app_module'] = 'service';
            $arr['is_manager'] = 1;
            $admin->addAdmin($arr);
        }
        return $this->success('操作成功','Service/index');
        
    }
    //修改服务中心
    public function editService()
    {  
        
        $id = input('id');
        $data = [];
        $data['take_down'] = input('take_down');
        $model = new ServiceModel();
        $info = $model->getInfo(['Service_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        
        $res = $model->saveService($data,['Service_id'=>$id]);
        if(!$res)
        {
            return $this->error('操作失败');
        }
        return $this->success('操作成功','Service/ServiceList');
    }
    
    //删除服务中心
    public function delService()
    {  
        
        $id = input('id');
        $data = [];
       
        $model = new ServiceModel();
        $info = $model->getInfo(['id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        $data['is_delete'] = 1;
        $data['update_time'] = date('Y-m-d H:i:s');
        $res = $model->saveService($data,['id'=>$id]);
        if(!$res)
        {
            return $this->error('删除失败');
        }
        return $this->success('删除成功','Service/ServiceList');
    }
    
     //修改服务中心页面
    public function add()
    {  
        $this->assign('meta_title','添加服务中心');
        return $this->view->fetch();
    }
   
    
    //修改服务中心页面
    public function edit()
    {  
        
        $id = input('id');
        $data = [];
       
        $model = new ServiceModel();
        $info = $model->getInfo(['service_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
       
        $this->assign('meta_title','服务中心编辑');
        $this->assign('info', $info);
        return $this->view->fetch();
    }

    
     //服务中心提现列表
    public function ServiceWithdrawList()
    {   
        isset($_GET['withdraw_type'])&&$_GET['withdraw_type']!=''?$where['withdraw_type'] = $_GET['withdraw_type']:'';
        $service = new ServiceModel();
        if(isset($_GET['servicename']))
        {
            $info = $service->getInfo(['service_name'=>$_GET['servicename']],'service_id');
            if($info)
            {
                $where['service_id'] = $info['service_id'];
            }
            else
            {
                $where['service_id'] = 0;
            }
        }
      
        $where['is_delete'] = 0;
        //var_dump($where);die;
        $list = Db::name('service_withdraw')->where($where)->paginate(20);
        
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
          
            switch ($v['withdraw_type'])
            {
                case 'bank':
                    $entryType = '银行卡';
                    $accountNo = $v['bank_code'];
                    $real_name = $v['bank_name'];
                break;
                case 'alipay':
                    $entryType = '支付宝';
                    $accountNo = $v['alipay_code'];
                    $real_name = $v['alipay_name'];
                    break;
                case 'wx':
                    $entryType = '微信';
                    $accountNo = $v['wechat'];
                    break;
                default:
                    
                    break;
            }
            $v['real_name'] = $real_name;
            $serviceinfo = $service->getInfo(['service_id'=>$v['service_id']],'service_name');
            $v['service_name'] = $serviceinfo['service_name'];
            $v['status_name'] = $stata_name;
            $v['entryType'] = $entryType;
            $v['accountNo'] = $accountNo;
            $v['create_time']  = date('Y-m-d H:i:s',$v['create_time']);
            $v['fail_time']  = date('Y-m-d H:i:s',$v['fail_time']);
            $list[$k] = $v;
        }
        $this->assign('meta_title','提现列表');
        $this->assign('list', $list);
        return $this->view->fetch('withdrawlist');
    }
}