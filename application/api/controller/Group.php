<?php


namespace app\api\controller;

use app\common\controller\Apibase;
use app\api\model\Group as GroupModel;
use app\api\model\GroupJoin;
use app\api\model\GroupTeam;
use app\api\model\Users;
use app\api\model\Shop;
use app\api\model\UsersBill;
use app\admin\model\Goods;
use app\api\model\RedisLock;

class Group extends Apibase
{   
    /**
     * 拼团活动列表
     */
    public function groupList()
    {   
        $token = $this->checkToken();
        
        $time = time();
      
        $model = new GroupModel();
        $cond = [
                'status' => 1,//已上架
                'begin_time' => ['elt',$time],
                'end_time' => ['egt',$time],
            ];
        if(isset($this->params['goods_name']) && $this->params['goods_name'] != '')
        {
            $cond['goods_name'] = $this->params['goods_name'];
        }
        $list = $model->getList($cond);
        //echo $model->getLastSql();
        
        $goods = new Goods();
        foreach ($list as &$v)
        {   
            $goodsinfo = $goods->getInfo(['id'=>$v['goods_id']],'goods_price,img_list,subtitle');
            $v['goods_price'] = $goodsinfo['goods_price'];
            $v['img_list'] = explode(',',$goodsinfo['img_list']);
            //$v['goods_info'] = $goodsinfo['goods_info'];
            $v['subtitle'] = $goodsinfo['subtitle'];
        }
        
        return $this->success_req($list);
    }
    
    
    
    /**
     * 拼团活动详情
     */
    public function getGroupInfo()
    {   
        
        $model = new GroupModel();
        
        $group_id = $this->params['group_id'];
        $longitude = isset($this->params[ 'longitude' ]) ? $this->params[ 'longitude' ] : 0;
        $latitude = isset($this->params[ 'latitude' ]) ? $this->params[ 'latitude' ] : 0;
        $info = $model->getInfo(['group_id'=>$group_id]);
        $goods = new Goods();
        $goodsinfo = $goods->getInfo(['id'=>$info['goods_id']],'goods_price,img_list,check_type,goods_info,subtitle,goods_class,sales,stock,verify_num');
        // $users = new Users();
        // $userinfo = $users->getInfo(['users_id'=>$info['users_id']],'username');
        // $info['username'] = $userinfo['username'];
        $info['goods_price'] = $goodsinfo['goods_price'];
        $info['img_list'] = explode(',',$goodsinfo['img_list']);
        $info['goods_info'] = $goodsinfo['goods_info'];
        $info['subtitle'] = $goodsinfo['subtitle'];
        $info['sales'] = $goodsinfo['sales'];
        $info['stock'] = $goodsinfo['stock'];
        $info['verify_num'] = $goodsinfo['verify_num'];
        //根据商品核销类型查找店铺
        if($goodsinfo['check_type'] && $goodsinfo['goods_class'] == 2)
        {
            $shop = new Shop();
            $where = [
                    'check_type' => $goodsinfo['check_type'],
                    'shop_status' => 1,
                    'take_down' => 0
                ];
            isset($this->params['city_name']) && $this->params['city_name'] != ''?$where['city_name'] = $this->params['city_name'] : '';
            isset($this->params['district_name']) && $this->params['district_name'] != ''?$where['district_name'] = $this->params['district_name'] : '';
            $shoplist = $shop->getList($where,'shop_id,shop_name,start_time,end_time,full_address,address,longitude,latitude,logo,is_top');
            $list3 = [];
            if($shoplist)
            {   
                $list = [];
                $list1 = [];
                $list2 = [];
                $sort = [];
                $sort1 = [];
                foreach ($shoplist as &$v)
                {   
                    
                    $v['distance'] = 0;
                    if($longitude && $latitude && $v['longitude'] && $v['latitude'])
                    {   
                        //计算距离
                        $v['distance'] = $this->GetDistance($latitude,$longitude,$v['latitude'],$v['longitude']);
                        //置顶
                        if($v['is_top'] == 1)
                        {
                            $list[] = $v;
                            $sort[] = $v['distance'];
                        }
                        else
                        {
                            $list1[] = $v;
                            $sort1[] = $v['distance'];
                        }
                        
                    }
                    else
                    {
                        $list2[] = $v;
                    }
                }
                
                //距离排序
                array_multisort($sort1,SORT_ASC,SORT_NUMERIC,$list1);
                array_multisort($sort,SORT_ASC,SORT_NUMERIC,$list);
                // $this->write_log($list);
                // $this->write_log($list1);
                // $this->write_log($list2);
                $list3 = array_merge($list,$list1,$list2);
                //$list3 = $list + $list1 + $list2;
                //$this->write_log($list3);
            }
            $info['shop_list'] = $list3;
        }
        
        return $this->success_req($info);
    }
    
    public function getShopInfo()
    {   
        $this->write_log($this->params);
        $shop = new Shop();
        $shop_id = $this->params['shop_id'];
        $info = $shop->getInfo(['shop_id'=>$shop_id],'shop_name,logo,goods_content');
        return $this->success_req($info);
    }
    
    
    //已报名活动列表
    public function getMyGroupList()
    {   
        
        $token = $this->checkToken();
        if(!isset($this->users_id))
        {   
            return  $this->error_req(100, '用户未登录');
        }
        //状态：1拼团中2拼团成功3拼团失败
        switch ($this->params['status']) {
            case '1':
                $where['join_status'] = 0;
                break;
            case '2':
                $where['join_status'] = ['in',[1,2]];
                break;
            case '3':
                $where['join_status'] = 3;
                break;
            default:
                break;
        }
        
        $time = time();
        $groupjoin = new GroupJoin();
        $where['users_id'] = $this->users_id;
        
        $list = $groupjoin->getList($where);
        if($list)
        {  
            $goods = new Goods();
            $team = new GroupTeam();
            $group = new GroupModel();
            foreach ($list as $k => &$v)
            {
                $info = $team->getInfo(['team_id'=>$v['team_id']]);
                $v['team_num'] = $info['team_num'];
                $v['join_num'] = $info['join_num'];
                $v['end_time'] = $info['end_time'];
                $v['goods_class'] = $info['goods_class'];
                $groupinfo = $group->getInfo(['group_id'=>$v['group_id']],'goods_id');
                $goodsinfo = $goods->getInfo(['id'=>$groupinfo['goods_id']],'goods_name,goods_price,image_url,img_list');
                $v['goods_name'] = $goodsinfo['goods_name'];
                $v['goods_price'] = $goodsinfo['goods_price'];
                $v['image_url'] = $goodsinfo['image_url'];
            }
        }
        return $this->success_req($list);
    }
    
     /**
    * 计算两组经纬度坐标 之间的距离
    * params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km);
    * return m or km
    */

    public function GetDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2)
    {
    	$radLat1 = $lat1 * PI ()/ 180.0; //PI()圆周率
    	$radLat2 = $lat2 * PI() / 180.0;
    	$a = $radLat1 - $radLat2;
    	$b = ($lng1 * PI() / 180.0) - ($lng2 * PI() / 180.0);
    	$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
    	$s = $s * 6378.137;
    	$s = round($s * 1000);
    	if ($len_type == 1)
    	{
    		$s /= 1000;
    	}
    	return round($s, $decimal);
    }

   
}
