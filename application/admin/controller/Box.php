<?php


namespace app\admin\controller;


use app\common\controller\Adminbase;
use think\Db;
use think\Exception;

class Box extends AdminBase {

	public function index() {
		$list = Db::name('box')->where('is_del', 0)->paginate(20, false, ['query' => request()->param()]);
		$this->assign('list', $list);
		return $this->view->fetch('index');
	}

	public function edit() {
		$id = input('id');
		$info = Db::name('box')->where('id',$id)->find();
		if(!empty($_POST)){

		}
		$this->assign('info', $info);
		return $this->view->fetch('edit');
	}

	//上/下架
	public function editBox() {
		$id = input('id');
		$status = input('status');
		$model = new \app\admin\model\Box();
		$info = $model->where(['id' => $id])->find();
		if (!$info) {
			return $this->error('id错误');
		}
		$res = $model->savebox(['status' => $status], ['id' => $id]);
		if (!$res) {
			return $this->error('修改失败');
		}
		$this->addlog('修改盲盒，id:' . $id);
		return $this->success('保存成功', 'Box/index');
	}

	public function add() {
		if (!empty($_POST)) {
			$img = $_POST['attach_id'];
			unset($_POST['attach_id']);
			$data = $_POST;
			$data['img'] = $img;
			Db::startTrans();
			try {
				$res = Db::name('box')->insert($data);
				Db::commit();
				$this->success('新增成功', 'Box/index');
			} catch (Exception $e) {
				Db::rollback();
				$this->error($e);
			}
		}
		return $this->view->fetch('add');
	}

	public function del() {
		$id = input('id');
		Db::startTrans();
		$box = new \app\admin\model\Box();
		try {
			$res = $box->savebox(['is_del' => 1], ['id' => $id]);
			Db::commit();
			$this->success('删除', 'Box/index');
		} catch (Exception $e) {
			Db::rollback();
			$this->error($e);
		}
	}

}