<?php
namespace app\agent\controller;
use app\common\controller\Homebase;
use think\Request;
use think\Db;
use app\api\model\GroupOrder;
use app\api\model\UsersWithdraw;
use app\admin\model\Users;

class Index extends Homebase
{   
    /**
	 * 首页
	 */
	public function index(){
        if(Request::instance()->isPost()){
            // 做一个简单的登录 组合where数组条件 
            $map=input('post.');
            // dump($map);
            // exit();
            $map['password']=md5($map['password']);
            $map['app_module'] = 'agent';
            $data=Db::name('admin')->where($map)->find();
            if (empty($data)) {
                $this->error('账号或密码错误');
            }else{
                $sdata=[
	                'id'=>$data['id'],
	                'username'=>$data['username'],
	                'avatar'=>$data['avatar'],
	                'email'=>$data['email'],
	                'phone'=>$data['phone'],
                    'is_manager' => $data['is_manager']
                ];
                session('user',$sdata);

                $this->success('登录成功、前往管理后台','Agent/Index/home');
            }
        }else{
            return $this->fetch();
        }
	}

    
    public function home()
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
        $order = new GroupOrder();
        $users_withdraw = new UsersWithdraw();
        $users = new Users();
        
        $data['all_order_price'] = $order->getDataSum(['status'=>1],'order_price');
        $data['all_balance_money'] = $users->getDataSum([],'balance_money');
        
        
        $this->assign('meta_title','首页');
        
		$this->assign('config',$config);
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
