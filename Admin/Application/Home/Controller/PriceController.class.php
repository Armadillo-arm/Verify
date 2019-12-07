<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class PriceController extends BaseController {
    public function index(){
		$softwares = $this->getSoftwareList2();
        $this->assign('softlist', $softwares);
		$user_id = $this->getUserId();
		$mysql = M('Proxy');
		$ret = $mysql->where("user_id = '$user_id'")->select();
		$this->assign('proxylist', $ret);
        $this->display();
    }
	public function PriceList(){
		if (I('post.page') == '') {
            return;
        }
		if (I('post.limit') == '') {
            return;
        }
		$user_id = $this->getUserId();
		$this->_currentPage = I('post.page/d');
		$this->_itemCountAPage = I('post.limit/d');
		$mysql = M('Price');
		$ret = $mysql->where("user_id = '$user_id'")->select();
		$list = $mysql->where("user_id = '$user_id'")->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
		$i = 0;
		foreach ($list as $value) {
			     $soft = M('Software');
				 $proxy = M('Proxy');
                 $list[$i]['soft_name'] = $soft->where("id = '{$value['soft_id']}'")->find()['name'];
				 $list[$i]['proxy_name'] = $proxy->where("id = '{$value['proxy_id']}'")->find()['username'];
				 $i = $i + 1;
        }
		$date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
				  "data" => $list
               ];
		echo json_encode($date);
	}
	public function PriceDel(){
		$mysql = M('Price');
		$id = I('post.id');
        $ret = $mysql->where("id = '$id'")->delete();
		$date=[
                  'status'=>0,
                  'msg'=>($ret?'删除成功':'删除失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function EditPrice(){
		$mysql = M('Price');
		$sid = I('post.id');
		$ret = $mysql->create();
        $ret = $mysql->save($ret);
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'更新成功':'更新失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function AddPrice(){
		$mysql = M('Price');
		$proxy_id = I('post.proxy_id');
		$soft_id = I('post.soft_id');
		$hour = I('post.hour');
		$day = I('post.day');
		$month = I('post.month');
		$season = I('post.season');
		$year = I('post.year');
		$user_id = $this->getUserId();
		$result = $mysql->where("soft_id = '$soft_id' AND proxy_id = '$proxy_id'")->find();
		if($result){
			$date=[
                  'status'=>1,
                  'msg'=>'添加失败 该代理已经设置软件开卡价格'
                  ];
		    $this->ajaxReturn(json_encode($date),'JSON');
		}
		$mysql->proxy_id = $proxy_id;
		$mysql->soft_id = $soft_id;
		$mysql->hour = $hour;
		$mysql->day = $day;
		$mysql->month = $month;
		$mysql->season = $season;
		$mysql->year = $year;
		$mysql->user_id = $user_id;
        $ret = $mysql->add();
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'添加成功':'添加失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
}