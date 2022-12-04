<?php
namespace app\common\controller;
use app\admin\model\Admin;
use app\admin\model\AuthGroupAccess;
use app\common\controller\Base;
use think\Db;
use think\Request;
/**
 * admin 基类控制器
 */
class AdminBase extends Base{ 
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		parent::_initialize();
		$auth=new \think\Auth();
		$request = Request::instance();
		$m=$request->module();
		$c=$request->controller();
		$a=$request->action();
		$rule_name=$m.'/'.$c.'/'.$a;
        //var_dump($rule_name);die;
        $is_manager = session('user')['is_manager']; //是否为超级管理员


        if ($is_manager != 1){
            $result=$auth->check($rule_name,session('user')['id']);
            if(!$result){
                if(!session('user'))
                {
                    $this->error('未登录','Home/Index/index');
                }
                $this->error('您没有权限访问');
            }
        }
	}
	
	function addlog($log, $name = false)
    {
        if (!$name) {
            session_start();
            $uid = session('user')['id'];
            if ($uid) {
                $user = Db::name('admin')->field('username')->where(array('id' => $uid))->find();
                $data['name'] = $user['username'];
            } else {
                $data['name'] = '';
            }
        } else {
            $data['name'] = $name;
        }
        $data['t'] = time();
        $data['ip'] = $_SERVER["REMOTE_ADDR"];
        $data['log'] = $log;
        Db::name('log')->insert($data);
    }





}

