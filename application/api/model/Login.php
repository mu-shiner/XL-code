<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 23:09
 */

namespace app\api\model;
use think\Model;
use think\Db;

class Login extends Model
{
     /**
     * 用户登录
     * @param $data
     * @return array
     * @throws DbException
     */
    public function login($data)
    {
        $info = model("users")->getInfo(
                [
                    'username' => $data['username'],
                    'password' => md5($data['password']),
                    'is_delete' => 0
                ],
             'users_id, username, phone, status, last_login_time, bdtcID'
        );
        if (empty($info)) {
            return $this->error_req('-1', '帐号或密码错误');
        } elseif ($info['status'] == 0) {
            return $this->error_req('-1', '帐号已被禁用');
        } else {
            //更新登录时间
            model("users")->update([
                'last_login_ip' => request()->ip(),
                'last_login_time' => time()
            ], ['users_id'=> $info['users_id']]);
            //用户第三方信息刷新
            //$this->refreshAuth($info['member_id'], $data);
            return $this->success_req($info);
        }
    }
    
     /**
     * 验证用户名
     */
    public function usernameExist($data)
    {
        $info = model("users")->getInfo(
                [
                    'username' => $data,
                    'is_delete' => 0
                ],
             'users_id'
        );
        
        return $info['users_id']?1:0;
        
    }
    
    
    /**
     * 用户名密码注册(必传username， password),之前检测重复性
     * @param $data
     * @return array|mixed
     */
    public function usernameRegister($data)
    {   
        
        $data_reg = [
            'username' => $data[ 'username' ],
            'phone' => isset($data[ 'phone' ]) ? $data[ 'phone' ] : '',
            'password' => md5($data[ 'password' ]),
            'wx_openid' => isset($data[ 'wx_openid' ]) ? $data[ 'wx_openid' ] : '',
            'weapp_openid' => isset($data[ 'weapp_openid' ]) ? $data[ 'weapp_openid' ] : '',
            'avatar' => isset($data[ 'avatar' ]) ? $data[ 'avatar' ] : '',//头像
            'reg_time' => time(),
            'last_login_time' => time(),
            'last_login_ip' => request()->ip(),
            'status' => 1,
            'parent_id' => $data[ 'parent_id' ],//邀请人id
            'invitation_code' => $data[ 'code' ],//生成的邀请码
            // 'point_withdraw_amount' => 300 //新注册用户加300积分提现额度
        ];
        $res = model("users")->save($data_reg);
        if ($res) {
            //添加统计
            // $stat = new Stat();
            // $stat->addShopStat([ 'member_count' => 1, 'site_id' => 0 ]);
            $info = model("users")->getInfo(['username'=>$data[ 'username' ]],'users_id');
            $data = $info['users_id'];
           
            return $this->success_req($data);
        } else {
            return $this->success_req(100,'注册失败');
        }
    }
    
    
    
   
    
    
     /**
     * 检测手机号存在性存在返回1
     * @param $mobile
     * @return int
     */
    public function mobileExist($mobile)
    {
        $users_info = model("users")->getInfo([ 'phone'=> $mobile,'is_delete'=> 0 ], 'users_id');
        if (!empty($users_info)) {
            return 1;
        } else {
            return 0;
        }
    }
    
    
    /**
     * 返回值函数
     * @param int $code
     * @param string $message
     * @param string $data
     * @return array
     */
    public function success_req($data = '', $code = 200, $message = 'success')
    {  
        $arr = [
            'code' => $code,
            'meg' => $message,
            'data' => $data
        ];
        return json_encode($arr);
    }
    
      /**
     * 错误返回值函数
     * @param int $code
     * @param string $message
     * @param string $data
     * @return array
     */
    public function error_req($code = -1, $message = '', $data = '')
    {
        $arr = [
            'code' => $code,
            'meg' => $message,
            'data' => $data
        ];
        
        return json_encode($arr);
    }
    
  
}