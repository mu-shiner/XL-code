<?php

namespace app\admin\controller;

use app\admin\model\Attachment;
use app\common\controller\Adminbase;

use think\Exception;
use think\Log;
use think\Request;
use think\File;
use think\Db;
use app\admin\model\Version as VersionModel;



class Version extends  AdminBase
{
   
    public function VersionList()
    {
        $model = new VersionModel();
        $list = $model->getList();
        foreach ($list as &$v)
        {   
            switch ($v['status']) {
                case 0:
                    $stata_name = '未发布';
                    break;
                case 1:
                    $stata_name = '已发布';
                    break;
               
                default:
                    $stata_name = '';
                    break;
            }
            $v['stata_name'] = $stata_name;
            $v['force'] = $v['is_force'] == 1?'是':'否';
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
        }
        $this->assign('meta_title','APP版本列表');
        $this->assign('list', $list);
        return $this->view->fetch('index');
    }

    /**
     * 版本添加
     */
    public function VersionAdd()
    {  
        //判断是否表单提交数据
        if (!empty($_POST)){
            $data = [];
            
            $id = isset($_POST['id'])?$_POST['id']:'';
            $data['version_name']  = isset($_POST['version_name'])?$_POST['version_name']:'';
            $data['note']  = isset($_POST['note'])?$_POST['note']:'';
            $data['is_force']  = isset($_POST['is_force'])?$_POST['is_force']:0;
            
            if($_FILES["apk_url"]["error"])
            {   
                return $this->error($_FILES["apk_url"]["erroe"]);
            }
            else
            {   
                //允许通过的文件名后缀
                $allow_extexsion = [
                    'apk'
                ];
                $extension=strtolower(pathinfo($_FILES["apk_url"]["name"],PATHINFO_EXTENSION));//获取文件后缀
                //控制上传的文件类型，大小
                if(in_array($extension, $allow_extexsion))
                {
                    // 注意更改接收字段
                    $file = request()->instance()->file('apk_url');
                    
                    // 移动到框架应用根目录/public/uploads/ 目录下
                    $info = $file->move(ROOT_PATH . '/upload/apk');
                    if($info){
                        $data['apk_url'] = '/upload/apk/' . $info->getSaveName();
                    }else{
                        // 上传失败获取错误信息
                        return $this->error($file->getError());
                    }
                }
                else
                {   
                    return $this->error('文件格式不正确');
                }
            }

            
            $model = new VersionModel();
            if($id)
            {   
                $res = $model->saveVersion($data,['version_id'=>$id]);
                $this->addlog('修改版本id:'.$id);
            }
            else
            {   
                $vinfo = $model->getInfo(['version_name'=>$data['version_name']]);
                if($vinfo)
                {
                    return $this->error('版本号重复');
                }
                $data['create_time'] = time();
                $res = $model->insert($data);
                $this->addlog('新增版本'.$data['Version_name']);
            }
            
            if ($res){
                
                return $this->success('保存成功','Version/VersionList');
            }

        }
        $this->assign('meta_title','版本添加');
        return $this->view->fetch('add');
    }
    //修改版本
    public function editVersion()
    {  
        
        $id = input('id');
        $data = [];
        $data['status'] = input('status');
        $model = new VersionModel();
        $info = $model->getInfo(['version_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        
        $res = $model->saveInfo($data,['version_id'=>$id]);
        if(!$res)
        {
            return $this->error('修改失败');
        }
        $this->addlog('修改版本状态，id:'.$id);
        return $this->success('保存成功','Version/VersionList');
    }
    
    //删除版本
    public function delVersion()
    {  
        $id = input('id');
        $data = [];
       
        $model = new VersionModel();
        $info = $model->getInfo(['version_id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        $data['is_delete'] = 1;
        $res = $model->saveInfo($data,['version_id'=>$id]);
        if(!$res)
        {
            return $this->error('删除失败');
        }
        if(file_exists($_SERVER['DOCUMENT_ROOT'].$info['apk_url']))
        {
            unlink($_SERVER['DOCUMENT_ROOT'].$info['apk_url']);
        }
        $this->addlog('删除版本，id:'.$id);
        return $this->success('删除成功','Version/VersionList');
    }
    
     //修改版本页面
    public function edit()
    {  
        
        $id = input('id');
        $data = [];
       
        $model = new VersionModel();
        $info = $model->getInfo(['id'=>$id]);
        if(!$info)
        {
            return $this->error('id错误');
        }
        $this->assign('meta_title','版本编辑');
        $this->assign('Versioninfo', $info);
        return $this->view->fetch();
    }
    
    
   


}