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
use app\admin\model\UserUpdatepriceLog;
use think\Db;
use app\admin\model\Users as UsersModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\UsersConfig;
use app\api\model\Verify;
use app\api\model\UsersBill;
use app\api\model\UsersWithdraw;
use app\api\model\GroupOrder;
use app\api\model\UsersOrder;


class Users extends AdminBase {
    /**
     * 初始化
     */
    // public function _initialize()
    // {

    // }
    protected $transError;
    //会员列表
    public function usersList() {
        isset($_GET['username']) && $_GET['username'] != '' ? $where['username|phone'] = $_GET['username'] : '';
        input('is_partner') != null && input('is_partner') != '' ? $where['is_partner'] = input('is_partner') : '';
        $whereTime = [];
        if (input('create_time') != null) {
            switch (input('create_time')) {
                case '1':
                    $whereTime = 'w';
                    break;
                case '2':
                    $time = strtotime(date('Y-m-d')) - 3600 * 24 * 15;
                    $whereTime = [date('Y-m-d', $time), date('Y-m-d')];
                    break;
                case '3':
                    $whereTime = 'm';
                    break;
            }
        }
        $where['is_delete'] = 0;
        // $where['withdraw_type'] = 0;
        // $where['point'] = ['gt',100];
        // $where['point_withdraw_amount'] = ['gt',100];
        if($whereTime){
            $list = Db::name('users')->order('reg_time DESC')->where($where)->whereTime('reg_time', $whereTime)->paginate(20, false, ['query' => request()->param()]);
        }else{
            $list = Db::name('users')->order('reg_time DESC')->where($where)->paginate(20, false, ['query' => request()->param()]);
        }

        $verify = new Verify();
        foreach ($list as $k => &$v) {
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
                    if ($v['partner_pay'] == 1) {
                        $partner .= '(付费)';
                    }
                    break;
                default:
                    $partner = '';
                    break;
            }

            $v['partner'] = $partner;
            $v['status_name'] = $stata_name;
            $v['reg_time'] = date('Y-m-d H:i:s', $v['reg_time']);
            $v['parent_name'] = '';
            if ($v['parent_id']) {
                $info = Db::name('users')->where(['users_id' => $v['parent_id']])->field('username')->find();
                $v['parent_name'] = $info['username'];
            }
            $v['verify_num'] = $verify->getCount(['users_id' => $v['users_id'], 'is_verify' => 0]);
            $list[$k] = $v;
        }
        $this->assign('meta_title', '会员列表');
        $this->assign('list', $list);
        return $this->view->fetch('index');
    }


    //修改会员状态
    public function editUsers() {
        $id = input('id');
        $data = [];
        $model = new UsersModel();
        $info = $model->getInfo(['users_id' => $id]);
        if (!$info) {
            return $this->error('id错误');
        }
        if (input('status') !== null) {
            $data['status'] = input('status');


            if ($data['status'] == 1) {
                $str = '解封';
            } else {
                $str = '拉黑';
            }
        }

        if (input('partner_whitelist') !== null) {
            $data['partner_whitelist'] = input('partner_whitelist');


            if ($data['partner_whitelist'] == 1) {
                $str = '设置白名单';
            } else {
                $str = '取消白名单';
            }
        }
        if (input('reset_pwd') !== null) {
            $data['password'] = 'e10adc3949ba59abbe56e057f20f883e';
            $str = '重置密码';
        }

        if (input('is_partner') !== null) {
            $data['is_partner'] = input('is_partner');

            if ($data['is_partner'] == 1) {
                $data['partner_time'] = time();
                $data['direct_week_num'] = 0;
                $str = '成为合伙人';

            } else {
                // if($info['partner_pay'] == 1)
                // {
                //     return $this->error('付费合伙人不能取消');
                // }
                $data['partner_time'] = '';
                $data['partner_pay'] = 0;
                //$data['partner_money'] = 0;
                $str = '取消合伙人';
            }
        }

        $res = $model->saveUser($data, ['users_id' => $id]);
        if (!$res) {
            return $this->error('修改失败');
        }
        $this->addlog('修改会员状态，userid:' . $id . $str);
        return $this->success('保存成功', 'Users/usersList');
    }

    //删除会员
    public function delUsers() {

        $id = input('id');
        $data = [];

        $model = new usersModel();
        $info = $model->getInfo(['users_id' => $id]);

        $data['is_delete'] = 1;
        //$data['update_time'] = date('Y-m-d H:i:s');
        $res = $model->saveusers($data, ['users_id' => $id]);
        if (!$res) {
            return $this->error('删除失败');
        }
        return $this->success('删除成功', 'Users/UsersList');
    }

    //分佣配置
    public function usersConfigList() {
        $config = new UsersConfig();
        $list = $config->getList();
        foreach ($list as &$v) {
            switch ($v['config_type']) {
                case '1':
                    $v['config_name'] = '拼团成功';
                    break;
                case '2':
                    $v['config_name'] = '抽奖成功';
                default:
                    // code...
                    break;
            }
        }
        $this->assign('meta_title', '分佣配置');
        $this->assign('list', $list);
        return $this->view->fetch('configlist');
    }

    //修改配置页面
    public function configedit() {
        $model = new UsersConfig();
        if (!empty($_POST)) {
            $data = [];
            $config_id = isset($_POST['config_id']) ? $_POST['config_id'] : '';
            $data['rate_one'] = isset($_POST['rate_one']) ? $_POST['rate_one'] : '';//一级分销比例
            $data['rate_two'] = isset($_POST['rate_two']) ? $_POST['rate_two'] : 0;//二级分销比例
            $data['rate_three'] = isset($_POST['rate_three']) ? $_POST['rate_three'] : '';//三级分销比例
            $data['update_time'] = time();
            $res = $model->saveUsersConfig($data, ['config_id' => $config_id]);
            if ($res) {
                return $this->success('保存成功', 'Users/usersConfigList');
            }

        }
        $id = input('id');


        $info = $model->getInfo(['config_id' => $id]);
        if (!$info) {
            return $this->error('id错误');
        }
        switch ($info['config_type']) {
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
        $this->assign('meta_title', '修改配置');
        return $this->view->fetch();
    }

    //提现配置
    public function withdrawConfigList() {
        $config = new UsersConfig();
        $list = $config->getWithdrawList();
        foreach ($list as &$v) {
            switch ($v['config_type']) {
                case '1':
                    $v['config_name'] = '积分';
                    break;
                case '2':
                    $v['config_name'] = '余额';
                    break;
                case '3':
                    $v['config_name'] = '分红';
                    break;
                case '6':
                    $v['config_name'] = '商户';
                    break;
                case '7':
                    $v['config_name'] = '服务中心';
                    break;
                default:
                    // code...
                    break;
            }
        }
        $this->assign('meta_title', '提现配置');
        $this->assign('list', $list);
        return $this->view->fetch('withdrawconfiglist');
    }

    //修改配置页面
    public function withdrawconfigedit() {
        $model = new UsersConfig();
        if (!empty($_POST)) {
            $data = [];
            $config_id = isset($_POST['config_id']) ? $_POST['config_id'] : '';
            $data['withdraw_rate'] = isset($_POST['withdraw_rate']) ? $_POST['withdraw_rate'] : '';//一级分销比例
            $data['withdraw_fixed'] = isset($_POST['withdraw_fixed']) ? $_POST['withdraw_fixed'] : 0;//二级分销比例
            $data['min_price'] = isset($_POST['min_price'])? $_POST['min_price'] : 0;//最小提现金额
            $data['coin_rate'] = isset($_POST['coin_rate'])? $_POST['coin_rate'] : 0;//消耗金币比例
            $data['update_time'] = time();
            $res = $model->saveWithdrawConfig($data, ['id' => $config_id]);
            if ($res) {
                return $this->success('保存成功', 'Users/withdrawConfigList');
            }

        }
        $id = input('id');
        $info = $model->getWithdrawInfo(['id' => $id]);
        if (!$info) {
            return $this->error('id错误');
        }
        switch ($info['config_type']) {
            case '1':
                $info['config_name'] = '积分';
                break;
            case '2':
                $info['config_name'] = '余额';
                break;
            case '3':
                $info['config_name'] = '分红';
                break;
            case '6':
                $info['config_name'] = '商户';
                break;
            case '7':
                $info['config_name'] = '服务中心';
                break;
            default:
                // code...
                break;
        }

        $this->assign('info', $info);
        $this->assign('meta_title', '修改配置');
        return $this->view->fetch();
    }

    //会员提现列表
    public function usersWithdrawList() {
        input('type') != null && input('type') != '' ? $where['type'] = input('type') : '';
        //isset($_GET['type'])&&$_GET['type']!=''?$where['type'] = $_GET['type']:'';
        isset($_GET['withdraw_type']) && $_GET['withdraw_type'] != '' ? $where['withdraw_type'] = $_GET['withdraw_type'] : '';
        isset($_GET['status']) && $_GET['status'] != '' ? $where['status'] = $_GET['status'] : '';
        $users = new UsersModel();
        if (isset($_GET['username']) && $_GET['username'] != '') {
            $info = $users->getInfo(['username' => $_GET['username']], 'users_id');
            if ($info) {
                $where['users_id'] = $info['users_id'];
            } else {
                $where['users_id'] = 0;
            }
        }

        $where['is_delete'] = 0;

        $list = Db::name('users_withdraw')->order('create_time DESC')->where($where)->paginate(20, false, ['query' => request()->param()]);

        foreach ($list as $k => &$v) {
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
            switch ($v['type']) {
                case '1':
                    $type_name = '积分';
                    break;
                case '2':
                    $type_name = '余额';
                    break;
                case '3':
                    $type_name = '分红积分';
                    break;
                default:
                    $type_name = '';
                    break;
            }
            switch ($v['withdraw_type']) {
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
            if ($v['longitude'] && $v['latitude']) {
                $v['jingwei'] = $v['longitude'] . ',' . $v['latitude'];
            } else {
                $v['jingwei'] = '';
            }
            $v['status_name'] = $stata_name;
            $v['type_name'] = $type_name;
            $v['entryType'] = $entryType;
            $v['accountNo'] = $accountNo;
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['fail_time'] = $v['fail_time'] ? date('Y-m-d H:i:s', $v['fail_time']) : '';
            $users_info = $users->getInfo(['users_id' => $v['users_id']], 'username,avatar');
            $v['username'] = $users_info['username'];
            $list[$k] = $v;
        }
        $this->assign('meta_title', '提现列表');
        $this->assign('list', $list);
        return $this->view->fetch('withdrawlist');
    }


    public function editPartnerConfig() {
        $model = new UsersConfig();
        if (!empty($_POST)) {
            $data = [];
            $data['first_num'] = isset($_POST['first_num']) ? $_POST['first_num'] : '0';//达标推荐人数
            $data['price'] = isset($_POST['price']) ? $_POST['price'] : 0;//支付费用
            $data['award'] = isset($_POST['award']) ? $_POST['award'] : 0;//返佣金额
            $data['uptime'] = time();
            $res = $model->savePartnerConfig($data, ['id' => 1]);
            if ($res) {
                return $this->success('保存成功', 'Users/usersList');
            }
        }
        $info = $model->getPartnerConfig(['id' => 1]);
        $this->assign('info', $info);
        $this->assign('meta_title', '修改配置');
        return $this->view->fetch('editpartnerconfig');
    }


    //会员订单列表
    public function usersOrder() {
        input('type') != null && input('type') != '' ? $where['type'] = input('type') : '';
        isset($_GET['pay_type']) && $_GET['pay_type'] != '' ? $where['pay_type'] = $_GET['pay_type'] : '';

        $users = new UsersModel();
        if (isset($_GET['username']) && $_GET['username'] != '') {
            $info = $users->getInfo(['username' => $_GET['username']], 'users_id');
            if ($info) {
                $where['users_id'] = $info['users_id'];
            } else {
                $where['users_id'] = 0;
            }
        }

        $where['is_delete'] = 0;
        $where['status'] = 1;
        $list = Db::name('users_order')->where($where)->paginate(20, false, ['query' => request()->param()]);

        foreach ($list as $k => &$v) {

            switch ($v['type']) {
                case '0':
                    $type_name = '余额充值';
                    break;
                case '1':
                    $type_name = '成为合伙人';
                    break;
                default:
                    $type_name = '';
                    break;
            }
            switch ($v['pay_type']) {
                case '1':
                    $payType = '支付宝';
                    break;
                case '2':
                    $payType = '微信';
                    break;
                default:

                    break;
            }
            if ($v['longitude'] && $v['latitude']) {
                $v['jingwei'] = $v['longitude'] . ',' . $v['latitude'];
            } else {
                $v['jingwei'] = '';
            }

            $v['type_name'] = $type_name;
            $v['payType'] = $payType;
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['pay_time'] = $v['pay_time'] ? date('Y-m-d H:i:s', $v['pay_time']) : '';
            $users_info = $users->getInfo(['users_id' => $v['users_id']], 'username,avatar');
            $v['username'] = $users_info['username'];
            $list[$k] = $v;
        }
        $this->assign('meta_title', '订单列表');
        $this->assign('list', $list);
        return $this->view->fetch('userorder');
    }

    public function PartnerList() {
        $where['is_delete'] = 0;
        $where['is_partner'] = 1;
        $where['partner_withdraw_amount'] = ['gt', 0];
        $list = Db::name('users')->order('reg_time DESC')->where($where)->paginate(20, false, ['query' => request()->param()]);
        $verify = new Verify();
        foreach ($list as $k => &$v) {
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
                    if ($v['partner_pay'] == 1) {
                        $partner .= '(付费)';
                    }
                    break;
                default:
                    $partner = '';
                    break;
            }

            $v['partner'] = $partner;
            $v['status_name'] = $stata_name;
            $v['reg_time'] = date('Y-m-d H:i:s', $v['reg_time']);
            $v['parent_name'] = '';
            if ($v['parent_id']) {
                $info = Db::name('users')->where(['users_id' => $v['parent_id']])->field('username')->find();
                $v['parent_name'] = $info['username'];
            }
            $v['verify_num'] = $verify->getCount(['users_id' => $v['users_id'], 'is_verify' => 0]);
            $list[$k] = $v;
        }
        $this->assign('meta_title', '合伙人列表');
        $this->assign('list', $list);
        return $this->view->fetch('partnerlist');
    }


    public function usersInfo() {

        $users_id = input('id');

        $users = new UsersModel();
        $data = $users->getInfo(['users_id' => $users_id], 'users_id,username,phone,balance_money,point,partner_money');

        if (!$data) {
            return $this->error('id错误');
        }
        $order = new GroupOrder();
        $data['order_price'] = $order->getDataSum(['status' => 1, 'pay_type' => ['neq', 0], 'users_id' => $users_id], 'order_price');
        $user_order = new UsersOrder();
        $data['user_price'] = $user_order->getDataSum(['type' => 0, 'status' => 1, 'users_id' => $users_id], 'order_price');

        $users_withdraw = new UsersWithdraw();
        $data['point_withdra'] = $users_withdraw->getWithdrawSum(['status' => 2, 'type' => 1, 'users_id' => $users_id], 'withdraw_price');
        $data['money_withdra'] = $users_withdraw->getWithdrawSum(['status' => 2, 'type' => 2, 'users_id' => $users_id], 'withdraw_price');
        $data['partner_withdra'] = $users_withdraw->getWithdrawSum(['status' => 2, 'type' => 3, 'users_id' => $users_id], 'withdraw_price');
        $verify = new Verify();
        $data['verify_num'] = $verify->getCount(['is_verify' => 1, 'users_id' => $users_id]);
        $data['guoqi_verify_num'] = $verify->getCount(['is_verify' => 2, 'users_id' => $users_id]);
        $data['duihuan_verify_num'] = $verify->getCount(['is_verify' => 3, 'users_id' => $users_id]);
        $data['sy_verify_num'] = $verify->getCount(['is_verify' => 0, 'users_id' => $users_id]);
        $data['dh_verify_num'] = $verify->getCount(['is_verify' => 0, 'trade_status' => 1, 'users_id' => $users_id]);

        $this->assign('meta_title', '首页');
        $this->assign('data', $data);
        $this->assign('config', $config);
        return $this->view->fetch('usersinfo');
    }
    public function checkWithDraw()
    {
        $result = input("post.");
        $withdraw = UsersWithdraw::where("id",$result['id'])->find();
        if($result['status'] == 2)
        {
            if($withdraw['withdraw_type'] == "alipay"){
                $type = [1=>"积分提现",2=>"余额提现",3=>"合伙人积分提现"];
                if(!$this->trans(bcadd($withdraw['withdraw_price'],-$withdraw['arrival_point'],2),$type[$withdraw['type']],$withdraw['alipay_code'],$withdraw['real_name'])){
                    $this->error($this->transError,'Users/userswithdrawlist');
                }
            }
            $withdraw->status = 2;
            $withdraw->save();
            $this->success("提交成功");
        }else{
            $withdraw->status = -1;
            $withdraw->save();
            $users = \app\admin\model\Users::where("users_id",$withdraw['users_id'])->find();
            switch ($withdraw['type'])
            {
                case 1:
                    $coin_rate = \app\admin\model\WithdrawConfig::where("config_type",1)->value("coin_rate");
                    $users->point+=$withdraw->withdraw_price;
                    $users->point_withdraw_amount += $withdraw->withdraw_price;
                    $coin = intval($withdraw->withdraw_price*$coin_rate/100);
                    $users->coin+=$coin;
                    $users->save();
                    break;
                case 2:
                    $users->balance_money +=  $withdraw->withdraw_price;
                    $users->save();
                    break;
                case 3:
                    $coin_rate = \app\admin\model\WithdrawConfig::where("config_type",1)->value("coin_rate");
                    $users->partner_money+=$withdraw->withdraw_price;
                    $users->partner_withdraw_amount += $withdraw->withdraw_price;
                    $coin = intval($withdraw->withdraw_price*$coin_rate/100);
                    $users->coin+=$coin;
                    $users->save();
                    break;
            }
            $this->success("提交成功");
        }

    }
    public function trans($amount,$title,$account,$realname){
        require __DIR__.'/../alipay-sdk-php-all-master/aop/AopCertClient.php';
        require __DIR__.'/../alipay-sdk-php-all-master/aop/request/AlipayFundTransUniTransferRequest.php';
        /** 初始化 **/
        $aop = new \AopCertClient;

        /** 支付宝网关 **/
        $aop -> gatewayUrl = "https://openapi.alipay.com/gateway.do";

        /** 应用id,如何获取请参考：https://opensupport.alipay.com/support/helpcenter/190/201602493024 **/
        $aop -> appId = "2021003153641074";

        /** 密钥格式为pkcs1，如何获取私钥请参考：https://opensupport.alipay.com/support/helpcenter/207/201602471154?ant_source=antsupport **/
        $aop -> rsaPrivateKey = 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCqL1PuN7We1TjZzAexXAt0C4nUY15FZCkTSI+doiW9uUmNxYXEfCxIVRm9YiAePidzU50sdTvCB/PYtjEliDsblStU5E84c11DIM3CLhGQEpkuB2HB1QCUsuemuO9OFIHPJPlFKvw59vNcRaZdswirGlCL27Ld4WB1LoGBRuHR6utKTl7rCGY63eVYLNCm963v5nGa4Rl0BPzwx4WCtL4ZVG5mB4nKTsuplGBrkhuON7NUYgaaP0BpmqACitKzOcxf7PvwLBZE+Fd3mRvE3IO2CmT5YTj4LJZM7/xNRJ5zsZP4vALzuHT7iXj12V7Gxk9ZcfUBL89GT7qet2brPkpnAgMBAAECggEAfKnUmnC2mxXn4isCC5q4TRZSrYDown3/VL/XbAomCVdcGPzy4x5utcGY7FCf5Gd1MJa0UKfD0XtP1ZSIZczoN2lK55GismBXld/GuZJTjS0ChmQj6P/lwAdZh5h6u8Br4lhcPJ2jS7apSBNBLewC0ouhKwIRgVUh+lTJyvAoZUKwrsuOGO5MqrjLWanfyaPBLcGn8LM/4MCYb1W2vWt+WrJOxMKey2uHwXcceahAhewG5nZeZY6NlERLTnkf9R7FTwmd5/h/9ICUGplMjR+nXkONnMmNmEehmqxpPSbi9wUTGlvUDGOUPNqdUhiwWumBKPlYrzvI74+vauF/89dkQQKBgQD6GgAT/1u2tJXYkKKVwgkvOI6sKbWymHCCZy2/8qFhTXwu3Yv5DVZwW0JKCtiwwuPVh4ndkA5ponsOJrrvoEJr8vPfnkJCkYyLiyJXQeP189w4f0WnSpGicsnQAce2YeT/q5Ee6Zb9cBPS8+M34b8LVFIBO9RIhF8yv5kDzNhPBwKBgQCuMtOtK5wagZNfM8uDne5beHkQY6Upi0OLo65wuJtt3r6+04J5Mdjs9vgBPwNpJqDxsX4FALcQYuojy8I+eQhqzyay7faPviW33fOv8RIzYf9axF+RWTHvDno27imFTkaDSymxsZoER33ZWIDnEkf8jk60XnwUq8jqUXRX8fnxoQKBgF90TvVS7/kioVJfmX4Y6ZJ5PpLc9HkujzpmEOMCwq81eKEWc5bhjU0it4E09JE6QOS9b1P96FJO7jJve8d7Xf5/Yq7FYzqu/HpB5yBwiIXVxgZWJQp9fmoG75mRJF0qrdEa9S9cLgGapiZMaTtp0JWNYCMSZ6opw3/F+quloiU/AoGAVBbM/8cRb+okzcwe7cYLDbS2HCc9zzQewwWca2VyAjOIOG25ie96G8mMJm3Yo6W2A3X+s0OJGyvkgqsVdTrPyV99+tnML89GPd+yhrgEZTFlJtesmmlIJXIDpQiKmoMSnsZlthVZl787DPQgJWs5vLylWYRSuVfDgPkZBGsWkIECgYBvJpjpJQZAjrsaZR+/SJJ1bHp1sOuojjEoSMx3H8sFd7uNa41ag+ZYz7iBq0QSdBadFEo/bvsKXGuLlSTjhS7TI19OxKJF7gvx/RovH+ZhWxirn5w+27rqACUU24D23voeYNt0jxsE9KJ/9vaO4R9a3Gqe94afAtK6Ln4FCo8OOg==';

        /** 应用公钥证书路径，下载后保存位置的绝对路径  **/
        $appCertPath = __DIR__ . "/../alipay-sdk-php-all-master/cert/appCertPublicKey.crt";

        /** 支付宝公钥证书路径，下载后保存位置的绝对路径 **/
        $alipayCertPath = __DIR__ . "/../alipay-sdk-php-all-master/cert/alipayCertPublicKey_RSA2.crt";

        /** 支付宝根证书路径，下载后保存位置的绝对路径 **/
        $rootCertPath = __DIR__ . "/../alipay-sdk-php-all-master/cert/alipayRootCert.crt";

        /** 设置签名类型 **/
        $aop -> signType= "RSA2";

        /** 设置请求格式，固定值json **/
        $aop -> format = "json";

        /** 设置编码格式 **/
        $aop -> charset= "utf-8";

        /** 调用getPublicKey从支付宝公钥证书中提取公钥 **/
        $aop -> alipayrsaPublicKey = $aop -> getPublicKey($alipayCertPath);

        /** 是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内 **/
        $aop -> isCheckAlipayPublicCert = true;

        /** 调用getCertSN获取证书序列号 **/
        $aop -> appCertSN = $aop -> getCertSN($appCertPath);

        /** 调用getRootCertSN获取支付宝根证书序列号 **/
        $aop -> alipayRootCertSN = $aop -> getRootCertSN($rootCertPath);

        /** 实例化具体API对应的request类，类名称和接口名称对应,当前调用接口名称：alipay.fund.trans.uni.transfer(单笔转账接口) **/
        $request = new \AlipayFundTransUniTransferRequest();
        $orderno = date('YmdHis') . mt_rand(1000, 9999);
        /** 设置业务参数，具体接口参数传值以文档说明为准：https://opendocs.alipay.com/apis/api_28/alipay.fund.trans.uni.transfer/ **/
        $request -> setBizContent("{".

            /** 商户端的唯一订单号，对于同一笔转账请求，商户需保证该订单号唯一 **/
            "\"out_biz_no\":\"{$orderno}\",".

            /** 转账金额，TRANS_ACCOUNT_NO_PWD产品取值最低0.1  **/
            "\"trans_amount\":\"$amount\",".

            /** 产品码，单笔无密转账到支付宝账户固定为：TRANS_ACCOUNT_NO_PWD **/
            "\"product_code\":\"TRANS_ACCOUNT_NO_PWD\",".

            /** 场景码，单笔无密转账到支付宝账户固定为：DIRECT_TRANSFER  **/
            "\"biz_scene\":\"DIRECT_TRANSFER\",".

            /** 转账业务的标题，用于在支付宝用户的账单里显示 **/
            "\"order_title\":\"{$title}\",".

            /** 收款方信息 **/
            "\"payee_info\":{".

            /** 参与方的唯一标识,收款支付宝账号或者支付宝吧账号唯一会员ID **/
            "\"identity\":\"{$account}\",".

            /** 参与方的标识类型：ALIPAY_USER_ID 支付宝的会员ID  **/
            "\"identity_type\":\"ALIPAY_LOGON_ID\",".

            /** 参与方真实姓名，如果非空，将校验收款支付宝账号姓名一致性。当identity_type=ALIPAY_LOGON_ID时，本字段必填 **/
            "\"name\":\"{$realname}\"".
            "},".

            /** 业务备注  **/
            "\"remark\":\"{$title}\"".

            "}");

        $result = $aop -> execute($request);

        /** 获取接口调用结果，如果调用失败，可根据返回错误信息到该文档寻找排查方案：https://opensupport.alipay.com/support/helpcenter/114 **/
        $result = json_decode( json_encode($result, JSON_UNESCAPED_UNICODE),true);
        if($result['alipay_fund_trans_uni_transfer_response']['code'] == 10000){
            return true;
        }else{
            $this->transError = $result['alipay_fund_trans_uni_transfer_response']['sub_msg'];
            return false;
        }
    }
       public function editMoney(){
        $post =input("post.");
        try{
            Db::startTrans();
            $user = UsersModel::where("users_id",$post['users_id'])->field("users_id,balance_money")->find();
            $old_price = $user->balance_money;
            $new_price = $post['price'];
            $ip = request()->ip();
            $users_id = $post['users_id'];
            $admin_id = session('user')['id'];
            $update_time = time();
            $user->balance_money = $post['price'];
            $user->save();
            UserUpdatepriceLog::create(compact("old_price","new_price","ip","users_id","admin_id","update_time"));
            Db::commit();
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
        $this->success();
    }
     public function editMoneyLog()
    {
        $list = UserUpdatepriceLog::with(["users"=>function($query){
            $query->field("users_id,username");
        },"admin"=>function($query){
            $query->field("id,username");
        }])->order("id desc")->paginate(20);
        $this->assign("list",$list);
        //dump($list->toArray());die();
        return $this->fetch("updatepricelog");
    }
}