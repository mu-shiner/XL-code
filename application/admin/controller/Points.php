<?php
/**
 * Created by PhpStorm.
 * User: 罗伟
 * Date: 2022/9/21
 * Time: 21:49
 */

namespace app\admin\controller;


use app\api\model\PointsTrade;
use app\common\controller\Adminbase;
use think\Db;
use app\admin\model\Goods as GoodsModel;

class Points extends Adminbase
{
    //发货管理
    public function ship() {
        isset($_GET['goodsid']) && $_GET['goodsid'] != '' ? $where['goods_id'] = $_GET['goodsid'] : '';
        isset($_GET['status']) && $_GET['status'] != '' ? $where['status'] = $_GET['status'] : '';
        //$where['is_delete'] = 0;

        $list = Db::name('points_trade')->where($where)->order("id desc")->paginate(20, false, ['query' => request()->param()]);
        //echo Db::name('group')->getLastSql();die;
        $users = new \app\admin\model\Users();
        $goods = new GoodsModel();
        foreach ($list as $k => $v) {
            switch ($v['status']) {
                case 0:
                    $stata_name = '无需发货';
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
            // $goodsinfo = $goods->getInfo(['id'=>$v['goods_id']],'image_url,goods_price');
            // $v['goods_price'] = $goodsinfo['goods_price'];
            // $v['goods_img'] = $goodsinfo['image_url'];
            $v['create_time'] = $v['create_time'] ? date('Y-m-d H:i:s', $v['create_time']) : '';
            $v['ship_time'] = $v['ship_time'] ? date('Y-m-d H:i:s', $v['ship_time']) : '';
            $v['receipt_time'] = $v['receipt_time'] ? date('Y-m-d H:i:s', $v['receipt_time']) : '';
            $list[$k] = $v;
        }
        $goods = new GoodsModel();
        $goodslist = $goods->getList(['goods_state' => 1, 'is_prize' => 2], 'id,goods_name');

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
        $info =PointsTrade::where("id",$id)->find();
        if (!$info) {
            return ['code' => 1, 'msg' => 'id错误'];
        }
        if ($info['status'] != 1) {
            return ['code' => 1, 'msg' => '状态错误'];
        }
        $data['status'] = 2;
        $data['ship_time'] = time();
        $res = $info->save($data);
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
        $model = new PointsTrade();
        $where['status'] = 1;
        input('goodsid') != '' ? $where['goods_id'] = input('goodsid') : '';
        $list = PointsTrade::where($where)->select();
        $users = new \app\admin\model\Users();
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
            $model = new PointsTrade();
            $success = 0;
            $error = 0;
            $time = time();
            foreach ($re as $key => $value) {
                //id
                if ($value[0] != null) {
                    $info = PointsTrade::where("id",$value[0])->field("id,status")->find();
                    if (!$info || $info['status'] != 1) {
                        $error++;
                    } else {
                        if ($value[6] != null && $value[7] != null) {
                            $res = $info->save(['logistics_company' => $value[6], 'logistics_no' => $value[7], 'status' => 2, 'receipt_time' => $time], ['id' => $info['id']]);
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
}