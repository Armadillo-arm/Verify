<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class ProxyController extends BaseController {
    public function index(){
		$softwares = $this->getSoftwareList2();
        $this->assign('softlist', $softwares);
        $this->display();
    }
	public function ProxyList(){
		if (I('post.page') == '') {
            return;
        }
		if (I('post.limit') == '') {
            return;
        }
		$user_id = $this->getUserId();
		$this->_currentPage = I('post.page/d');
		$this->_itemCountAPage = I('post.limit/d');
		$mysql = M('Proxy');
		$ret = $mysql->where("user_id = '$user_id'")->select();
		$list = $mysql->where("user_id = '$user_id'")->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
		$date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
				  "data" => $list
               ];
		echo json_encode($date);
	}
	public function ProxyDel(){
		$mysql = M('Proxy');
		$id = I('post.id');
        $ret = $mysql->where("id = '$id'")->delete();
		$date=[
                  'status'=>0,
                  'msg'=>($ret?'删除成功':'删除失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function EditProxy(){
		$mysql = M('Proxy');
		$sid = I('post.id');
		$ret = $mysql->create();
        $ret = $mysql->where("id = '$sid'")->save($ret);
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'更新成功':'更新失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function AddProxy(){
		$mysql = M('Proxy');
		$username = I('post.username');
		$password = I('post.password');
		$money = I('post.money');
		$rate = I('post.rate');
		$soft = I('post.soft');
		$frozen = I('post.frozen');
		$is_addproxy = I('post.is_addproxy');
		$user_id = $this->getUserId();
		
		$mysql->is_addproxy = $is_addproxy;
		$mysql->user_id = $user_id;
		$mysql->username = $username;
		$mysql->password = $password;
		$mysql->money = $money;
		$mysql->rate = $rate;
		$mysql->soft = $soft;
		$mysql->frozen = $frozen;
        $ret = $mysql->add();
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'添加成功':'添加失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
}