<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/5/5
 * Time: 22:35
 */

namespace app\admin\controller;

use app\admin\model\Attachment;
use app\common\controller\Adminbase;
use think\Db;
use app\admin\model\Group as GroupModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Users;
use app\api\model\GroupOrder;
use app\api\model\Shop;
use app\api\model\GroupTeam;
use app\api\model\GroupJoin;
use app\api\model\DigitalExchange;
use app\api\model\Verify;

class Group extends AdminBase {


	//拼团列表
	public function groupList() {
		isset($_GET['goodsname']) ? $where['goods_name'] = $_GET['goodsname'] : '';
		isset($_GET['status']) ? $where['status'] = $_GET['status'] : '';
		$where['is_delete'] = 0;

		$list = Db::name('group')->where($where)->paginate(20, false, ['query' => request()->param()]);

		foreach ($list as $k => $v) {
			switch ($v['status']) {
				case 0:
					$stata_name = '已下架';
					break;
				case 1:
					$stata_name = '已上架';
					break;
				case 2:
					$stata_name = '已结束';
					break;
				default:
					$stata_name = '';
					break;
			}
			$v['state_name'] = $stata_name;
			$v['begin_time'] = date('Y-m-d H:i:s', $v['begin_time']);
			$v['end_time'] = date('Y-m-d H:i:s', $v['end_time']);
			$list[$k] = $v;
		}
		$this->assign('meta_title', '拼团列表');
		$this->assign('list', $list);
		return $this->view->fetch('index');
	}

	/**
	 * 添加拼团
	 */
	public function groupAdd() {
		// echo("<pre>");
		// var_dump($_POST);echo("</pre>");die;
		//判断是否表单提交数据
		if (!empty($_POST)) {
			$data = [];
			$id = isset($_POST['group_id']) ? $_POST['group_id'] : '';
			$data['goods_id'] = isset($_POST['goods_id']) ? $_POST['goods_id'] : '';//商品id
			$data['begin_time'] = isset($_POST['begin_time']) ? strtotime($_POST['begin_time']) : '';//活动开始时间
			$data['end_time'] = isset($_POST['end_time']) ? strtotime($_POST['end_time']) : '';//活动结束时间
			$data['price'] = isset($_POST['price']) ? $_POST['price'] : 0;//价格
			$data['team_num'] = isset($_POST['team_num']) ? $_POST['team_num'] : 0;//价格
			$data['get_num'] = isset($_POST['get_num']) ? $_POST['get_num'] : 0;//价格
			$data['dividend_price'] = isset($_POST['dividend_price']) ? $_POST['dividend_price'] : 0;//市场分红价格

			if ($data['end_time'] <= $data['begin_time']) {
				return $this->error('开始时间不能超过结束时间');
			}
			$goods = new GoodsModel();
			$goodsinfo = $goods->getInfo(['id' => $data['goods_id']], 'goods_name,image_url,goods_class');
			if (!$data['goods_id']) {
				return $this->error('商品信息错误');
			}
			$data['goods_name'] = $goodsinfo['goods_name'];
			$data['goods_img'] = $goodsinfo['image_url'];
			$data['goods_class'] = $goodsinfo['goods_class'];
			$model = new groupModel();
			if ($id) {
				$res = $model->savegroup($data, ['group_id' => $id]);
			} else {
				$data['create_time'] = time();
				$res = $model->insert($data);
			}

			if ($res) {
				return $this->success('保存成功', 'group/groupList');
			}

		}
		$goods = new GoodsModel();
		$goodslist = $goods->getList(['goods_state' => 1, 'is_prize' => 0], 'id,goods_name');

		$this->assign('goodslist', $goodslist);
		$times = date('Y-m-d H:i:00');
		$this->assign('times', $times);
		$this->assign('meta_title', '添加拼团');
		return $this->view->fetch('add');
	}

	//修改拼团活动
	public function editGroup() {

		$id = input('id');
		$data = [];
		$data['status'] = input('status');
		$model = new groupModel();
		$info = $model->getInfo(['group_id' => $id]);
		if (!$info) {
			return $this->error('id错误');
		}
		// if($info['status'] == 1)
		// {
		//     $time = time();
		//     if($info['signup_begin_time'] < $time || info['group_begin_time'] < $time)
		//     {
		//         return $this->error('已开始的活动不能修改');
		//     }

		// }
		//$data['update_time'] = date('Y-m-d H:i:s');
		$res = $model->savegroup($data, ['group_id' => $id]);
		if (!$res) {
			return $this->error('修改失败');
		}
		return $this->success('保存成功', 'group/groupList');
	}

	//删除拼团活动
	public function delGroup() {

		$id = input('id');
		$data = [];

		$model = new groupModel();
		$info = $model->getInfo(['group_id' => $id]);
		if (!$info) {
			return $this->error('id错误');
		}
		if ($info['status'] != 0) {
			return $this->error('已上架活动不能删除');
		}
		$data['is_delete'] = 1;
		//$data['update_time'] = date('Y-m-d H:i:s');
		$res = $model->savegroup($data, ['group_id' => $id]);
		if (!$res) {
			return $this->error('删除失败');
		}
		return $this->success('删除成功', 'group/groupList');
	}

	//修改活动页面
	public function edit() {

		$id = input('id');
		$data = [];

		$model = new groupModel();
		$info = $model->getInfo(['group_id' => $id]);
		if (!$info) {
			return $this->error('id错误');
		}
		$info['begin_time'] = date('Y-m-d H:i:s', $info['begin_time']);
		$info['end_time'] = date('Y-m-d H:i:s', $info['end_time']);
		if (input('is_copy') && input('is_copy') == 1) {
			$info['group_id'] = '';
		}
		$this->assign('info', $info);
		$goods = new GoodsModel();
		$goodslist = $goods->getList(['goods_state' => 1, 'is_prize' => 0], 'id,goods_name');
		$this->assign('meta_title', '修改拼团');
		$this->assign('goodslist', $goodslist);
		return $this->view->fetch();
	}

	//发货管理
	public function groupShip() {
		isset($_GET['goodsname']) ? $where['goods_name'] = $_GET['goodsname'] : '';
		isset($_GET['status']) ? $where['order_status'] = $_GET['status'] : '';
		$where['is_delete'] = 0;
		$where['ship_type'] = 1;

		$list = Db::name('group_order')->where($where)->paginate(20);
		//echo Db::name('group')->getLastSql();die;
		$users = new Users();
		$goods = new GoodsModel();
		foreach ($list as $k => $v) {
			switch ($v['order_status']) {
				case 0:
					$stata_name = '未支付';
					break;
				case 1:
					$stata_name = '待发货';
					break;
				case 2:
					$stata_name = '已发货';
					break;
				case 3:
					$stata_name = '已签收';
					break;
				default:
					$stata_name = '';
					break;
			}

			$usersinfo = $users->getInfo(['users_id' => $v['users_id']], 'username');
			$v['username'] = $usersinfo['username'];
			$v['state_name'] = $stata_name;
			$goodsinfo = $goods->getInfo(['id' => $v['goods_id']], 'image_url,goods_price');
			$v['goods_price'] = $goodsinfo['goods_price'];
			$v['goods_img'] = $goodsinfo['image_url'];
			$v['create_time'] = $v['create_time'] ? date('Y-m-d H:i:s', $v['create_time']) : '';
			$v['ship_time'] = $v['ship_time'] ? date('Y-m-d H:i:s', $v['ship_time']) : '';
			$v['receipt_time'] = $v['receipt_time'] ? date('Y-m-d H:i:s', $v['receipt_time']) : '';
			$list[$k] = $v;
		}
		$this->assign('meta_title', '发货列表');
		$this->assign('list', $list);
		return $this->view->fetch('ship');
	}


	//发货
	public function delivery() {

		$id = input('id');
		$data['logistics_company'] = input('logisticsCompany');
		$data['logistics_no'] = input('logisticsNo');

		if (!$id || !$data['logistics_company'] || !$data['logistics_no']) {
			return ['code' => 1, 'msg' => '缺少参数'];
		}
		$model = new GroupOrder();
		$info = $model->getInfo(['order_id' => $id], 'order_id');
		if (!$info) {
			return ['code' => 1, 'msg' => 'id错误'];
		}
		$data['order_status'] = 2;
		$data['ship_time'] = time();
		$res = $model->saveData($data, ['order_id' => $id]);
		if (!$res) {
			return ['code' => 1, 'msg' => '保存失败'];
		}

		return ['code' => 0, 'msg' => '发货成功'];
	}


	//核销码列表
	public function verify() {
		$where = [];
		$orderby = 'id ASC';
		if (input('status') != null && input('status') != '') {
			switch (input('status')) {
				case '1'://可用的
					$where['is_verify'] = 0;//0可用1已核销2已过期
					$where['trade_status'] = 0;//0无交易1交易中
					break;
				case '2'://已核销的
					$where['is_verify'] = 1;
					$orderby = 'verify_time DESC';
					break;
				case '3'://转让中
					$where['is_verify'] = 0;
					$where['trade_status'] = 1;
					break;
				case '4'://过期的
					$where['is_verify'] = 2;
					break;
				default:
					break;
			}
		}
		$users = new Users();
		if (isset($_GET['username']) && $_GET['username'] != '') {
			$user_info = $users->getInfo(['username' => $_GET['username']], 'users_id');
			if ($user_info['users_id']) {
				$where['users_id'] = $user_info['users_id'];
			} else {
				$where['users_id'] = 0;
			}
		}
		$shop = new Shop();
		if (isset($_GET['shopname']) && $_GET['shopname'] != '') {
			$shop_info = $shop->getInfo(['shop_name' => $_GET['shopname']], 'shop_id');
			if ($shop_info['shop_id']) {
				$where['shop_id'] = $shop_info['shop_id'];
			} else {
				$where['shop_id'] = 0;
			}
		}

		$list = Db::name('verify')->where($where)->order($orderby)->paginate(20, false, ['query' => request()->param()]);
		//echo Db::name('group')->getLastSql();die;

		$goods = new GoodsModel();

		$order = new GroupOrder();
		foreach ($list as $k => $v) {
			switch ($v['is_verify']) {
				case '0':
					if ($v['trade_status'] == 0) {
						$stata_name = '可使用';//1可用的2已核销的3转让中4过期的
					} else {
						$stata_name = '交易中';
					}
					break;
				case '1':
					$stata_name = '已核销';
					break;
				case '2':
					$stata_name = '已过期';
					break;
				default:
					$stata_name = '';
					break;
			}


			$usersinfo = $users->getInfo(['users_id' => $v['users_id']], 'username');
			$v['username'] = $usersinfo['username'];
			$v['state_name'] = $stata_name;
			$orderinfo = $order->getInfo(['order_no' => $v['order_no']], 'goods_id,order_price');
			$v['order_price'] = $orderinfo['order_price'];
			$goodsinfo = $goods->getInfo(['id' => $orderinfo['goods_id']], 'goods_name,image_url,goods_price');
			$v['goods_name'] = $goodsinfo['goods_name'];
			$v['goods_price'] = $goodsinfo['goods_price'];
			$v['goods_img'] = $goodsinfo['image_url'];
			$v['create_time'] = $v['create_time'] ? date('Y-m-d H:i:s', $v['create_time']) : '';
			$v['verify_time'] = $v['verify_time'] ? date('Y-m-d H:i:s', $v['verify_time']) : '';
			if ($v["shop_id"]) {
				$shopinfo = $shop->getInfo(['shop_id' => $v['shop_id']], 'shop_name');
				$v['shop_name'] = $shopinfo['shop_name'];
			} else {
				$v['shop_name'] = '';
			}

			$list[$k] = $v;
		}
		$this->assign('meta_title', '核销码列表');
		$this->assign('list', $list);
		return $this->view->fetch();
	}

	//成团列表
	public function team() {
		$where['is_delete'] = 0;
		input('status') != null && input('status') != '' ? $where['status'] = input('status') : '';
		//var_dump(input('begin_time'));die;
		$begin_time = input('begin_time') != null && input('begin_time') != '' ? strtotime(input('begin_time') . ' 00:00:00') : '';
		$end_time = input('end_time') != null && input('end_time') != '' ? strtotime(input('end_time') . ' 23:59:59') : '';
		if ($begin_time && $end_time) {
			$where['success_time'] = ['between', "$begin_time,$end_time"];
		} elseif ($begin_time) {
			$where['success_time'] = ['gt', $begin_time];
		} elseif ($end_time) {
			$where['success_time'] = ['lt', $end_time];
		}
		$users = new Users();
		if (isset($_GET['username']) && $_GET['username'] != '') {
			$user_info = $users->getInfo(['username' => $_GET['username']], 'users_id');
			if ($user_info['users_id']) {
				$where['users_id'] = $user_info['users_id'];
			} else {
				$where['users_id'] = 0;
			}
		}

		$list = Db::name('group_team')->where($where)->paginate(20, false, ['query' => request()->param()]);

		$model = new GroupModel();
		foreach ($list as $k => $v) {
			switch ($v['status']) {
				case '0':
					$stata_name = '拼团中';//拼团中
					break;
				case '1':
					$stata_name = '已成团';
					break;
				case '2':
					$stata_name = '已失败';
					break;
				default:
					$stata_name = '';
					break;
			}

			$v['state_name'] = $stata_name;
			$groupinfo = $model->getInfo(['group_id' => $v['group_id']], 'goods_name,goods_img,price');
			$v['goods_name'] = $groupinfo['goods_name'];
			$v['goods_img'] = $groupinfo['goods_img'];
			$v['price'] = $groupinfo['price'];
			$v['create_time'] = $v['create_time'] ? date('Y-m-d H:i:s', $v['create_time']) : '';
			$v['success_time'] = $v['success_time'] ? date('Y-m-d H:i:s', $v['success_time']) : '';

			$list[$k] = $v;
		}
		$this->assign('meta_title', '拼团列表');
		$this->assign('list', $list);
		return $this->view->fetch();
	}


	//成团详情
	public function teaminfo() {
		$where['team_id'] = input('id');
		if (!$where['team_id']) {
			return $this->error('id错误');
		}
		$model = new GroupJoin();
		$list = $model->getList($where);

		$users = new Users();
		foreach ($list as $k => &$v) {
			switch ($v['join_status']) {
				case '0':
					$stata_name = '参团中';//拼团中
					break;
				case '1':
					$stata_name = '中产品';
					break;
				case '2':
					$stata_name = '中红包';
					break;
				case '3':
					$stata_name = '拼团失败';
					break;
				default:
					$stata_name = '';
					break;
			}
			$v['state_name'] = $stata_name;
			$usersinfo = $users->getInfo(['users_id' => $v['users_id']], 'username,avatar');
			$v['username'] = $usersinfo['username'];
			$v['avatar'] = $usersinfo['avatar'];
			// $groupinfo = $model->getInfo(['group_id'=>$v['group_id']],'goods_name,goods_img,price');
			// $v['goods_name'] = $groupinfo['goods_name'];
			// $v['goods_img'] = $groupinfo['goods_img'];
			// $v['price'] = $groupinfo['price'];
			$v['create_time'] = $v['create_time'] ? date('Y-m-d H:i:s', $v['create_time']) : '';
		}
		$this->assign('meta_title', '成团详情');
		$this->assign('list', $list);
		return $this->view->fetch();
	}


	//兑换列表
	public function exchangeList() {
		$where['is_delete'] = 0;
		isset($_GET['money_id']) && $_GET['money_id'] != '' ? $where['money_id'] = $_GET['money_id'] : '';
		isset($_GET['status']) && $_GET['status'] != '' ? $where['status'] = $_GET['status'] : '';
		isset($_GET['money_address']) && $_GET['money_address'] != '' ? $where['money_address'] = $_GET['money_address'] : '';
		$begin_time = input('begin_time') != null && input('begin_time') != '' ? strtotime(input('begin_time') . ' 00:00:00') : '';
		$end_time = input('end_time') != null && input('end_time') != '' ? strtotime(input('end_time') . ' 23:59:59') : '';
		if ($begin_time && $end_time) {
			$where['create_time'] = ['between', "$begin_time,$end_time"];
		} elseif ($begin_time) {
			$where['create_time'] = ['gt', $begin_time];
		} elseif ($end_time) {
			$where['create_time'] = ['lt', $end_time];
		}

		$users = new Users();
		if (isset($_GET['username']) && $_GET['username'] != '') {
			$info = $users->getInfo(['username' => $_GET['username']], 'users_id');
			if ($info) {
				$where['users_id'] = $info['users_id'];
			} else {
				$where['users_id'] = 0;
			}
		}


		$list = Db::name('digital_exchange')->where($where)->order('create_time DESC')->paginate(20, false, ['query' => request()->param()]);

		foreach ($list as $k => $v) {
			switch ($v['status']) {
				case 1:
					$stata_name = '待审核';
					break;
				case 2:
					$stata_name = '已成功';
					break;
				case 3:
					$stata_name = '已失败';
					break;
				default:
					$stata_name = '';
					break;
			}
			$usersinfo = $users->getInfo(['users_id' => $v['users_id']], 'username,phone');
			$v['username'] = $usersinfo['username'];
			$v['phone'] = $usersinfo['phone'];
			$v['state_name'] = $stata_name;
			$v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
			$v['finish_time'] = $v['finish_time'] ? date('Y-m-d H:i:s', $v['finish_time']) : '';
			$list[$k] = $v;
		}
		$model = new DigitalExchange();
		$moneylist = $model->getMoneyList();
		$this->assign('meta_title', '兑换列表');
		$this->assign('list', $list);
		$this->assign('moneylist', $moneylist);
		return $this->view->fetch('exchange');
	}

	/**
	 * 导出
	 */
	public function daochu() {

		vendor('PHPExcel');
		vendor('PHPExcel.PHPExcel_IOFactory');
		// 实例化excel
		$phpExcel = new \PHPExcel();

		$phpExcel->getProperties()->setTitle("通用券兑换");
		$phpExcel->getProperties()->setSubject("通用券兑换");
		// 对单元格设置居中效果
		$phpExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//单独添加列名称
		$phpExcel->setActiveSheetIndex(0);
		$phpExcel->getActiveSheet()->setCellValue('A1', 'id');//可以指定位置
		$phpExcel->getActiveSheet()->setCellValue('B1', '用户');
		$phpExcel->getActiveSheet()->setCellValue('C1', '用户手机');
		$phpExcel->getActiveSheet()->setCellValue('D1', '兑换类型');
		$phpExcel->getActiveSheet()->setCellValue('E1', '兑换数量');
		$phpExcel->getActiveSheet()->setCellValue('F1', '钱包地址');
		$phpExcel->getActiveSheet()->setCellValue('G1', '申请时间');
		$phpExcel->getActiveSheet()->setCellValue('H1', '兑换时间');
		//循环添加数据（根据自己的逻辑）

		$where['is_delete'] = 0;
		input('money_id') != '' ? $where['money_id'] = input('money_id') : '';
		input('status') != '' ? $where['status'] = input('status') : '';
		input('money_address') != '' ? $where['money_address'] = input('money_address') : '';

		$begin_time = input('begin_time') != null && input('begin_time') != '' ? strtotime(input('begin_time') . ' 00:00:00') : '';
		$end_time = input('end_time') != null && input('end_time') != '' ? strtotime(input('end_time') . ' 23:59:59') : '';
		if ($begin_time && $end_time) {
			$where['create_time'] = ['between', "$begin_time,$end_time"];
		} elseif ($begin_time) {
			$where['create_time'] = ['gt', $begin_time];
		} elseif ($end_time) {
			$where['create_time'] = ['lt', $end_time];
		}

		$users = new Users();
		if (input('username') != '') {
			$info = $users->getInfo(['username' => input('username')], 'users_id');
			if ($info) {
				$where['users_id'] = $info['users_id'];
			} else {
				$where['users_id'] = 0;
			}
		}
		$model = new DigitalExchange();

		//$where = ['status'=>1];
		//var_dump($where);die;
		$list = $model->getList($where);
		if (empty($list)) {
			return $this->error('无数据可导出');
		}

		foreach ($list as $k => $v) {
			$usersinfo = $users->getInfo(['users_id' => $v['users_id']], 'username,phone');
			$create = date('Y年m月d日 H:i:s', $v['create_time']);
			$finish = $v['finish_time'] ? date('Y年m月d日 H:i:s', $v['finish_time']) : '';
			$i = $k + 2;
			$phpExcel->getActiveSheet()->setCellValue('A' . $i, $v['id']);
			$phpExcel->getActiveSheet()->setCellValue('B' . $i, $usersinfo['username']);
			$phpExcel->getActiveSheet()->setCellValue('C' . $i, $usersinfo['phone']);
			$phpExcel->getActiveSheet()->setCellValue('D' . $i, $v['money_name']);
			$phpExcel->getActiveSheet()->setCellValue('E' . $i, $v['num']);
			$phpExcel->getActiveSheet()->setCellValue('F' . $i, $v['money_address']);
			$phpExcel->getActiveSheet()->setCellValue('G' . $i, $create);
			$phpExcel->getActiveSheet()->setCellValue('H' . $i, $finish);
		}
		// 重命名工作sheet
		$phpExcel->getActiveSheet()->setTitle('通用券兑换');
		// 设置第一个sheet为工作的sheet
		$phpExcel->setActiveSheetIndex(0);
		// 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
		$objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$file = date('Y年m月d日-通用券兑换表', time()) . '.xlsx';
		$objWriter->save($file);

		header("Content-type:application/octet-stream");

		$filename = basename($file);
		header("Content-Disposition:attachment;filename = " . $filename);
		header("Accept-ranges:bytes");
		header("Accept-length:" . filesize($file));
		readfile($file);
		unlink($file);
		exit;
	}


	//修改兑换状态
	public function editExchange() {
		$idlist = $_POST['idlist'];

		if (empty($idlist)) {
			return 'id不能为空';
		}
		$data = [];
		$data['status'] = $_POST['status'];
		$model = new DigitalExchange();
		$list = $model->getList(['id' => ['in', $idlist]]);
		$verify_list = [];
		foreach ($list as $v) {
			if ($v['status'] != 1) {
				return '状态错误';
			}
			$v['verify_list'] = explode(',', $v['verify_list']);
			if (empty($verify_list)) {
				$verify_list = $v['verify_list'];
			} else {
				$verify_list = array_merge($verify_list, $v['verify_list']);
			}

		}
		$data['finish_time'] = time();
		$res = $model->saveInfo($data, ['id' => ['in', $idlist]]);
		if (!$res) {
			return '修改失败';
		}
		$verify = new Verify();
		if ($data['status'] == 2) {
			$verify->saveVerify(['is_verify' => 3, 'trade_status' => 0], ['id' => ['in', $verify_list]]);
		} else {
			$verify->saveVerify(['trade_status' => 0], ['id' => ['in', $verify_list]]);
		}

		return '修改成功';
	}
}