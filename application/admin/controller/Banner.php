<?php


namespace app\admin\controller;


use app\common\controller\Adminbase;

class Banner extends Adminbase
{
    public function index()
    {
        $list = \app\admin\model\Banner::paginate(10);
        $this->assign("bannerlist",$list);
        return $this->fetch();
    }
    public function add(){
        if($this->request->isPost())
        {
            $img = $this->request->post("img/s");
            if(!$img) $this->error("提交的轮播图不存在");
            \app\admin\model\Banner::create(['img'=>$img]);
            $this->success("保存成功",url("index"));
        }
        return $this->fetch();
    }
    public function edit()
    {
        if($this->request->isPost())
        {
            $img = $this->request->post("img/s");
            $id =  $this->request->post("id/d");
            $banner = \app\admin\model\Banner::where("id",$id)->find();
            if(!$banner) $this->error("要修改的轮播图不存在");
            $banner->img = $img;
            $banner->save();
            $this->success("保存成功",url("index"));
        }
        $id = input("id");
        $banner = \app\admin\model\Banner::where("id",$id)->find();
        $this->assign("banner",$banner);
        return $this->fetch();
    }
    public function del()
    {
        $id = input("id");
         \app\admin\model\Banner::where("id",$id)->delete();
        $this->success('删除成功', 'Banner/index');
    }
    public function editStatus()
    {
        $id = input("id/s");
        $status = input("status/s");
        \app\admin\model\Banner::where("id",$id)->update(['status'=>$status]);
        $this->success("修改成功",url("index"));
    }
}