<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class BuyProxyController extends BaseController {
    public function index(){
		$mysql = M('Proxy');
		$user_id = $this->getUserId();
		$ret = $mysql->where("user_id = '$user_id'")->select();
		$this->assign('proxylist', $ret);
        $this->display();
    }
	public function BuyProxyList(){
		if (I('post.proxy_id') == '') {
            $date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => 0,
				  "data" => ''
                  ];
		    echo json_encode($date);
			return;
        }
		if (I('post.page') == '') {
            return;
        }
		if (I('post.limit') == '') {
            return;
        }
		$proxy_id = I('post.proxy_id');
		$user_id = $this->getUserId();
		$this->_currentPage = I('post.page/d');
		$this->_itemCountAPage = I('post.limit/d');
		$mysql = M('Buyproxy');
		$ret = $mysql->where("proxy_id = '$proxy_id'")->select();
		$list = $mysql->where("proxy_id = '$proxy_id'")->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
		$date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
				  "data" => $list
               ];
		echo json_encode($date);
	}
	public function Del(){
		$mysql = M('Buyproxy');
		$id = I('post.id');
        $ret = $mysql->where("id = '$id'")->delete();
		$date=[
                  'status'=>0,
                  'msg'=>($ret?'删除成功':'删除失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
}