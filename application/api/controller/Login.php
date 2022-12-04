<?php

namespace app\api\controller;

use app\common\controller\Apibase;
use app\api\model\Login as LoginModel;
use aliyun_dysms_sdk\api_demo\SmsDemo;
use think\Cache;
use app\api\model\Users;
use think\Validate;

class Login extends Apibase
{   
   
    /**
     * 登录方法
     */
    public function Login()
    {   
        $this->write_log($this->params);
        // 登录
        $login = new LoginModel();
        if (empty($this->params[ "password" ])) {
            return $this->error_req(-1, "密码不可为空!");
        }

        $res = $login->login($this->params);
        $res = json_decode($res, true);
        
        //生成access_token
        if ($res[ 'code' ] == 200) {
            $token = $this->createToken($res[ 'data' ][ 'users_id' ]);
            $users = new Users();
            if(isset($this->params[ 'wx_openid' ]) && $this->params[ 'wx_openid' ] != '')
            {
                $users->saveData(['wx_openid'=>''],['wx_openid'=>$this->params[ 'wx_openid' ]]);
                $users->saveData(['wx_openid'=>$this->params[ 'wx_openid' ]],['users_id'=>$res[ 'data' ][ 'users_id' ]]);
            }
            if(isset($this->params[ 'weapp_openid' ]) && $this->params[ 'weapp_openid' ] != '')
            {
                $users->saveData(['weapp_openid'=>''],['weapp_openid'=>$this->params[ 'weapp_openid' ]]);
                $users->saveData(['weapp_openid'=>$this->params[ 'weapp_openid' ]],['users_id'=>$res[ 'data' ][ 'users_id' ]]);
            }
            
            // $post_data = [];
            // $post_data['phone'] = $this->params[ 'username' ];
            // $post_data['password'] = $this->params[ 'password' ];
            // $url = 'http://user.100dtc.com/api/Index/bdtc_login';
            // $data = $this->PostCurls($url,$post_data);
            // $data = json_decode($data,true);
            // $this->write_log($data);
            // if($data['err'] == 0)
            // {
            //      $users->saveData(['bdtcID'=>$data['user_info']['bdtcID']],['users_id'=>$res[ 'data' ][ 'users_id' ]]);
            // }
            return $this->success_req([ 'token' => $token ]);
        }
       
        return json_encode($res);
    }
    
    /**
     * 注册
     */
    public function Register1()
    {   
        $this->write_log($this->params);
        $login = new LoginModel();
        $exist = $login->usernameExist($this->params[ 'username' ]);
        if ($exist) {
            return $this->error_req(100, "用户名已存在");
        } else {
            if(!$this->params['invitation_code'])
            {
                $this->params['parent_id'] = 12;//测试999
            }
            else
            {
                $info = $users->getInfo(['invitation_code'=>$this->params['invitation_code']],'users_id');
                if(!$info)
                {
                    return $this->error_req(100, "邀请码错误");
                }
                $this->params['parent_id'] = $info['users_id'];
            }
            $users = new Users();
           
            $this->params['code'] = $users->setInvitationCode();//生成邀请码
            
            $res = $login->usernameRegister($this->params);
            $res = json_decode($res, true);
            //生成access_token
            if ($res[ 'code' ] >= 0) {
                $getcode = 'http://user.100dtc.com/api/Index/getcode';
                $post_data = [];
                $post_data['code'] = $this->PostCurls($getcode,[]);
                $post_data['phone'] = $this->params[ 'username' ];
                $post_data['password'] = $this->params[ 'password' ];
                $url = 'http://user.100dtc.com/api/Index/bdtc_registered';
                $this->PostCurls($url,$post_data);
                return $this->success_req('注册成功');
            }
            return json_encode($res);
        }
    }
    
    
     /**
     * 注册
     */
    public function Register()
    {   
        $this->write_log($this->params);
        $login = new LoginModel();
        $exist = $login->usernameExist($this->params[ 'username' ]);
        $mobile = $this->params[ 'phone' ];//手机号
        $check = '/^(1(([23456789][0-9])|(47)))\d{8}$/';
        if (!preg_match($check, $mobile)) {
            return $this->error_req(100, "手机号格式错误");
        }
        $code = $this->params['code'];
        // $req = $this->mobileVerify($mobile,$code);
        // if(!$req)
        // {
        //     return $this->error_req(100, '验证码错误');
        // }
        if ($exist) {
            return $this->error_req(100, "用户名已存在");
        } else {
            
            $users = new Users();
            if(!$this->params['invitation_code'])
            {
                $this->params['parent_id'] = 12;//测试999
            }
            else
            {
                $info = $users->getInfo(['invitation_code'=>$this->params['invitation_code']],'users_id');
                if(!$info)
                {
                    return $this->error_req(100, "邀请码错误");
                }
                $this->params['parent_id'] = $info['users_id'];
            }
            $this->params['code'] = $users->setInvitationCode();//生成邀请码
            
            
            $res = $login->usernameRegister($this->params);
            $res = json_decode($res, true);
            //生成access_token
            // if ($res[ 'code' ] >= 0) {
            //     $getcode = 'http://user.100dtc.com/api/Index/getcode';
            //     $post_data = [];
            //     $post_data['code'] = $this->PostCurls($getcode,[]);
            //     $post_data['phone'] = $this->params[ 'username' ];
            //     $post_data['password'] = $this->params[ 'password' ];
            //     $url = 'http://user.100dtc.com/api/Index/bdtc_registered';
            //     $re = $this->PostCurls($url,$post_data);
            //     $this->write_log($re);
            //     // if($re['err'] == 0)
            //     // {
            //     //      $users->saveData(['bdtcID'=>$re['user_info']['bdtcID']],['users_id'=>$res[ 'data' ][ 'users_id' ]]);
            //     // }
            //     return $this->success_req('注册成功');
            // }
            return json_encode($res);
        }
    }
    
    
   
    /**
     * 短信验证码
     * @return false|string
     * @throws Exception
     */
    public function mobileCode()
    {   
        return $this->error_req("短信功能维护中...");
        $this->write_log($this->params);
        $mobile = $this->params[ 'mobile' ];//手机号
        $register = new LoginModel();
        //找回密码不需要验证手机号重复
        if($this->params['is_pwd'] != 1)
        {  
            $check = '/^(1(([23456789][0-9])|(47)))\d{8}$/';
            if (!preg_match($check, $mobile)) {
                return $this->error_req(100, "手机号格式错误");
            }

            // $exist = $register->mobileExist($mobile);
            // if ($exist) {
            //     return $this->error_req(100, "手机号已绑定");
            // }
        }
        else
        {
            $users = new Users();
            
            $info = $users->getInfo(['username'=>$this->params['username']],'users_id,phone');
            if(!$info || $info['phone'] != $mobile)
            {
                return $this->error_req(100, "手机号不正确");
            }
        }
       
        $code = str_pad(random_int(1, 9999), 6, 0, STR_PAD_LEFT);// 生成6位随机数，左侧补0
        //$code = '123456';// 生成6位随机数，左侧补0
        $this->write_log($code);
        $sms = new SmsDemo();
        $sms->sendSms($mobile,$code);
        //将验证码存入缓存
        $key = 'register_mobile_code_' . $mobile;
        $redis = Cache::getHandler();
        $redis->set($key, $code, 600);
        return $this->success_req("发送成功");
    }
    
    //绑定手机号
    public function bindMobile()
    {
        $token = $this->checkToken();
        $this->write_log($this->params);
        $users_id = $this->users_id;
        if(!$users_id)
        {
            return $this->error_req(100, '用户未登录');
        }
        $mobile = $this->params[ 'mobile' ];//手机号
        $code = $this->params['code'];
        $req = $this->mobileVerify($mobile,$code);
        if(!$req)
        {
            return $this->error_req(100, '验证码错误');
        }
        $users = new Users();
        
        $users->saveData(['phone'=>$mobile],['users_id'=>$users_id]);
        return $this->success_req("绑定成功");
    }
    
    
    //通过手机修改密码
    public function RevisePwdByMobile()
    {   
        $this->write_log($this->params);
        $username = $this->params['username'];
        $mobile = $this->params[ 'mobile' ];//手机号
        $code = $this->params['code'];
        $new_password = $this->params['new_password'];
        $again_password = $this->params['again_password'];
        if(!$new_password)
        {
            return $this->error_req(100, '新密码不能为空');
        }
        if($new_password != $again_password)
        {
            return $this->error_req(100, '2次密码不一致');
        }
        // $req = $this->mobileVerify($mobile,$code);
        // if(!$req)
        // {
        //     return $this->error_req(100, '验证码错误');
        // }
        
        $users = new Users();
        $info = $users->getInfo(['username'=>$username],'users_id,phone');
        if(!$info || $info['phone'] != $mobile)
        {
            return $this->error_req(100, "该帐号绑定手机号错误");
        }
        $users->saveData(['password'=>md5($new_password)],['users_id'=>$info['users_id']]);
        return $this->success_req('修改成功');
    }
    
    
    //通过旧密码修改密码
    public function RevisePwdByPwd()
    {   
        $token = $this->checkToken();
        $this->write_log($this->params);
        $data = [];
        $users_id = $this->users_id;
        if(!$users_id)
        {
            return $this->error_req(100, '用户未登录');
        }
        $old_password = $this->params['old_password'];
        $new_password = $this->params['new_password'];
        $again_password = $this->params['again_password'];
        if(!$new_password)
        {
            return $this->error_req(100, '新密码不能为空');
        }
        if($new_password != $again_password)
        {
            return $this->error_req(100, '2次密码不一致');
        }
        $users = new Users();
        $info = $users->getInfo(['users_id'=>$users_id],'password');
        if(!$info)
        {
            return $this->error_req(100, "用户信息出错");
        }
        if($info['password'] != md5($old_password))
        {
            return $this->error_req(100, '密码错误');
        }
        
        $users->saveData(['password'=>md5($new_password)],['users_id'=>$users_id]);
        return $this->success_req('修改成功');
    }
    
    
    
    /**
     * 短信验证码验证
     * @return false|string
     * @throws Exception
     */
    public function mobileVerify($mobile,$code)
    {
        $redis = Cache::getHandler();
        $key = 'register_mobile_code_' . $mobile;
        $info = $redis->get($key);
        //var_dump($info);die;
        if($info && $info == $code)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }
   
    //绑定拼团帐号
    public function bindPinTuan()
    {
        $token = $this->checkToken();
        $this->write_log($this->params);
        $users_id = $this->users_id;
        if(!$users_id)
        {
            return $this->error_req(100, '用户未登录');
        }
        $users = new Users();
        //判断该帐号是否已绑定
        $userinfo = $users->getInfo(['users_id'=>$users_id],'pt_member_id,member_level,member_expire_time');
        if($userinfo['pt_member_id'])
        {
            return $this->error_req(100, '已绑定拼团帐号,请勿重复绑定');
        }
        if($userinfo['member_level'] != 1 || $userinfo['member_expire_time'] < time())
		{
		    return $this->error_req(100, '非会员或会员过期无法绑定帐号');
		}
        $username = $this->params['username'];//手机号
        $password = $this->params['password'];
       
        if(!$username || !$password)
        {
            return $this->error_req(100, '帐号和密码不能为空');
        }
        //拼团接口
        $url = 'https://100dtc.com/api/Login/login_seckill';
        
        $post = [
                'username' => $username,
                'password' => $password
            ];
        $req = $this->PostCurls($url,$post);
        $req = json_decode($req,true);
        
        if($req['code'] != 0)
        {
            return $this->error_req(100, $req['message']);
        }
        $member_id = $req['data']['member_id'];
        $pt_username = $req['data']['username'];
        //判断是否已被绑定
        $info = $users->getInfo(['pt_member_id'=>$member_id],'users_id');
        if($info)
        {
            return $this->error_req(100, '该拼团帐号已被绑定');
        }
        $data = [
                'pt_member_id'=> $member_id,
                'pt_username' => $pt_username,
                'bind_time' => time()
            ];
        $res = $users->saveData($data,['users_id'=>$users_id]);
        if(!$res)
        {
            return $this->error_req(100, '绑定失败');
        }
        return $this->success_req("绑定成功");
    }
    
  
    
  
   
    
}
