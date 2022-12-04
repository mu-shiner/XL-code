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
use app\admin\model\Lottery as LotteryModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Users;
use app\api\model\GroupOrder;
use app\api\model\UsersLottery;

class Lottery extends AdminBase {
	//抽奖列表
	public function index() {

		$list = Db::name('lottery')->paginate(20);

		foreach ($list as $k => $v) {
			switch ($v['is_open']) {
				case 0:
					$stata_name = '已关闭';
					break;
				case 1:
					$stata_name = '已开启';
					break;
				default:
					$stata_name = '';
					break;
			}
			$v['state_name'] = $stata_name;
			$list[$k] = $v;
		}
		$this->assign('meta_title', '抽奖列表');
		$this->assign('list', $list);
		return $this->view->fetch('index');
	}

	/**
	 * 添加拼团
	 */
	public function lotteryAdd() {
		// echo("<pre>");
		// var_dump($_POST);echo("</pre>");die;
		//判断是否表单提交数据
		if (empty($_POST)) {
			return $this->error('数据不能为空');
		}

		$data = [];
		$id = isset($_POST['lottery_id']) ? $_POST['lottery_id'] : '';
		$data['begin_time'] = isset($_POST['begin_time']) ? $_POST['begin_time'] : '';//活动开始时间
		$data['end_time'] = isset($_POST['end_time']) ? $_POST['end_time'] : '';//活动结束时间
		$goods_id = isset($_POST['goods_id']) ? $_POST['goods_id'] : '';//商品id
		$goods_num = isset($_POST['goods_num']) ? $_POST['goods_num'] : '';//商品数量
		$data['goodsid'] = isset($_POST['goodsid']) ? $_POST['goodsid'] :'';
		$goods = new GoodsModel();

		$model = new LotteryModel();

		$arr = [];

		foreach ($goods_id as $k => $v) {
			$goodsinfo = $goods->getInfo(['id' => $v], 'goods_name,image_url,goods_class');
			$arr[$k]['goods_id'] = $v;
			$arr[$k]['goods_name'] = $goodsinfo['goods_name'];
			$arr[$k]['goods_img'] = $goodsinfo['image_url'];
			$arr[$k]['goods_num'] = $goods_num[$k];
			$arr[$k]['surplus_num'] = $goods_num[$k];
			$arr[$k]['goods_class'] = $goodsinfo['goods_class'];
			$arr[$k]['create_time'] = time();
		}
		Db::name("lottery_goods")->where(['lottery_id' => $id])->delete();
		// echo("<pre>");
		// var_dump($arr);
		// echo("</pre>");die;
		foreach ($arr as $key => $value) {
			$model->saveLotteryGoods($value, ['lottery_id' => $id, 'goods_level' => $key + 1]);
			//echo $model->getLastSql();
			// echo("<hr/>");
		}

		$res = $model->saveLottery($data, ['lottery_id' => $id]);


		return $this->success('保存成功', 'Lottery/index');


		// $this->assign('goodslist', $goodslist);
		// $times = date('Y-m-d H:i:00');
		// $this->assign('times', $times);
		// return $this->view->fetch('add');
	}

	//修改抽奖
	public function editLottery() {

		$id = input('id');
		$data = [];
		$data['is_open'] = input('is_open');
		$model = new LotteryModel();
		$info = $model->getInfo(['lottery_id' => $id]);
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
		$res = $model->saveLottery($data, ['lottery_id' => $id]);
		if (!$res) {
			return $this->error('修改失败');
		}
		return $this->success('保存成功', 'Lottery/index');
	}

	//删除抽奖
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

		$model = new LotteryModel();
		$info = $model->getInfo(['lottery_id' => $id]);
		if (!$info) {
			return $this->error('id错误');
		}

		$list = $model->getGoodsList(['lottery_id' => $id]);
// 		var_dump($list);die();
		$this->assign('list', $list);
		$this->assign('info', $info);
		$goods = new GoodsModel();
		$goodslist = $goods->getList(['goods_state' => 1, 'is_prize' => 1], 'id,goods_name');
		$mhqlist = $goods->getList(['goods_state' => 1,'goods_class'=>2, 'is_prize' => 0], 'id,goods_name');
		$this->assign('goodslist', $goodslist);
		$this->assign("mhqlist",$mhqlist);
		$this->assign('meta_title', '抽奖编辑');
		return $this->view->fetch();
	}

	//发货管理
	public function lotteryShip() {
		isset($_GET['goodsid']) && $_GET['goodsid'] != '' ? $where['goods_id'] = $_GET['goodsid'] : '';
		isset($_GET['status']) && $_GET['status'] != '' ? $where['status'] = $_GET['status'] : '';
		//$where['is_delete'] = 0;
		$where['is_get'] = 1;

		$list = Db::name('users_lottery')->where($where)->order("id desc")->paginate(20, false, ['query' => request()->param()]);
		//echo Db::name('group')->getLastSql();die;
		$users = new Users();
		$goods = new GoodsModel();
		foreach ($list as $k => $v) {
			switch ($v['status']) {
				case 0:
					$stata_name = '未填写';
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
				case -1:
				    	$stata_name = '审核未通过';
					break;
				 case 4:
				     	$stata_name = '申请发货';
					break;
				 case 5:
				     	$stata_name = '申请兑换积分';
					break;	
				default:
					$stata_name = '';
					break;
			}

			$usersinfo = $users->getInfo(['users_id' => $v['users_id']], 'username');
			$v['username'] = $usersinfo['username'];
			$v['state_name'] = $stata_name;
			// $goodsinfo = $goods->getInfo(['id'=>$v['goods_id']],'image_url,goods_price');
			// $v['goods_price'] = $goodsinfo['goods_price'];
			// $v['goods_img'] = $goodsinfo['image_url'];
			$v['create_time'] = $v['create_time'] ? date('Y-m-d H:i:s', $v['create_time']) : '';
			$v['ship_time'] = $v['ship_time'] ? date('Y-m-d H:i:s', $v['ship_time']) : '';
			$v['receipt_time'] = $v['receipt_time'] ? date('Y-m-d H:i:s', $v['receipt_time']) : '';
			$list[$k] = $v;
		}
		$goods = new GoodsModel();
		$goodslist = $goods->getList(['goods_state' => 1, 'is_prize' => 1], 'id,goods_name');

		$this->assign('goodslist', $goodslist);
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
		$model = new UsersLottery();
		$info = $model->getInfo(['id' => $id], 'status');
		if (!$info) {
			return ['code' => 1, 'msg' => 'id错误'];
		}
		if ($info['status'] != 1) {
			return ['code' => 1, 'msg' => '状态错误'];
		}
		$data['status'] = 2;
		$data['ship_time'] = time();
		$res = $model->saveInfo($data, ['id' => $id]);
		if (!$res) {
			return ['code' => 1, 'msg' => '保存失败'];
		}

		return ['code' => 0, 'msg' => '发货成功'];
	}


	/**
	 * 导出
	 */
	public function daochu() {

		vendor('PHPExcel');
		vendor('PHPExcel.PHPExcel_IOFactory');
		// 实例化excel
		$phpExcel = new \PHPExcel();

		$phpExcel->getProperties()->setTitle("抽奖发货");
		$phpExcel->getProperties()->setSubject("抽奖发货");
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
		$phpExcel->getActiveSheet()->setCellValue('B1', '物品名称');
		$phpExcel->getActiveSheet()->setCellValue('C1', '用户');
		$phpExcel->getActiveSheet()->setCellValue('D1', '收货人');
		$phpExcel->getActiveSheet()->setCellValue('E1', '收货电话');
		$phpExcel->getActiveSheet()->setCellValue('F1', '收货地址');
		$phpExcel->getActiveSheet()->setCellValue('G1', '发货物流');
		$phpExcel->getActiveSheet()->setCellValue('H1', '快递单号');
		//循环添加数据（根据自己的逻辑）
		$model = new UsersLottery();
		$where['is_get'] = 1;
		$where['status'] = 1;
		input('goodsid') != '' ? $where['goods_id'] = input('goodsid') : '';
		$list = $model->getList($where);
		$users = new Users();
		foreach ($list as $k => $v) {
			$usersinfo = $users->getInfo(['users_id' => $v['users_id']], 'username');

			$i = $k + 2;
			$phpExcel->getActiveSheet()->setCellValue('A' . $i, $v['id']);
			$phpExcel->getActiveSheet()->setCellValue('B' . $i, $v['goods_name']);
			$phpExcel->getActiveSheet()->setCellValue('C' . $i, $usersinfo['username']);
			$phpExcel->getActiveSheet()->setCellValue('D' . $i, $v['receipt_name']);
			$phpExcel->getActiveSheet()->setCellValue('E' . $i, $v['telephone']);
			$phpExcel->getActiveSheet()->setCellValue('F' . $i, $v['complete_address']);
		}
		// 重命名工作sheet
		$phpExcel->getActiveSheet()->setTitle('中奖发货信息');
		// 设置第一个sheet为工作的sheet
		$phpExcel->setActiveSheetIndex(0);
		// 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
		$objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$file = date('Y年m月d日-奖品发货表', time()) . '.xlsx';
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


	/**
	 * 导入
	 */
	public function daoru() {
		vendor('PHPExcel');
		vendor('PHPExcel.PHPExcel_IOFactory');
		vendor('PHPExcel.PHPExcel_Cell');

		$objPHPExcel = new \PHPExcel();
		$file = request()->file('excel');
		if ($file) {

			$file_types = explode(".", $_FILES ['excel'] ['name']); // ["name"] => string(25) "excel文件名.xls"
			$file_type = $file_types [count($file_types) - 1];//xls后缀
			$file_name = $file_types [count($file_types) - 2];//xls去后缀的文件名
			/*判别是不是.xls文件，判别是不是excel文件*/
			if (strtolower($file_type) != "xls" && strtolower($file_type) != "xlsx") {
				echo '不是Excel文件，重新上传';
				die;
			}

			$info = $file->move(ROOT_PATH . 'public' . DS . 'excel');//上传位置
			$path = ROOT_PATH . 'public' . DS . 'excel' . DS;
			$file_path = $path . $info->getSaveName();//上传后的EXCEL路径
			//echo $file_path;//文件路径


			$objReader = \PHPExcel_IOFactory::createReader('Excel2007');

			$objPHPExcel = $objReader->load($file_path, $encode = 'utf-8');
			$sheet = $objPHPExcel->getSheet(0);

			//读入数据,转换为数组格式
			$re = $sheet->toArray();
			unset($re[0]);
			// unlink($file_path);
			// $re = array_slice($re,0,50);
			// echo("<pre>");
			// var_dump($re);
			// echo("</pre>");die;
			$model = new UsersLottery();
			$success = 0;
			$error = 0;
			$time = time();
			foreach ($re as $key => $value) {
				//id
				if ($value[0] != null) {
					$info = $model->getInfo(['id' => $value[0]], 'id,status');
					if (!$info || $info['status'] != 1) {
						$error++;
					} else {
						if ($value[6] != null && $value[7] != null) {
							$res = $model->saveInfo(['logistics_company' => $value[6], 'logistics_no' => $value[7], 'status' => 2, 'receipt_time' => $time], ['id' => $info['id']]);
							if ($res) {
								$success++;
							}
						} else {
							$error++;
						}

					}
				}
			}
			unlink($file_path);
			$this->success('导入成功' . $success . '条，失败' . $error . '条');
		} else {
			$this->error('导入失败');
		}
	}
	 public function applycheck()
    {
        $id = input('id/d');
        $status = input('status/d');
        $model = new UsersLottery();
        $info = $model->getInfo(['id' => $id], 'status,goods_id,users_id');
        if (!$info) {
            return ['code' => 1, 'msg' => 'id错误'];
        }
        if ($info['status'] != 4 && $info['status']!=5 ) {
            return ['code' => 1, 'msg' => '状态错误'];
        }
        if($status == -1){
            $model->where("id",$id)->update(['status'=>-1]);
        }else{
            if($info['status'] == 4){
                $model->where("id",$id)->update(['status'=>1]);
            }else if($info['status'] == 5){
                $model->where("id",$id)->update(['status'=>3,'receipt_time'=>time()]);
                $jifen = \app\admin\model\Goods::where("id",$info['goods_id'])->value("jifen");
                if($jifen && $jifen > 0){
                    Users::where("users_id",$info['users_id'])->setInc("point",$jifen);
                }
            }
        }
        return ['code' => 0, 'msg' => '审核完成'];
    }
}