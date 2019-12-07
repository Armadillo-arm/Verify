<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class CloudSoftController extends BaseController {
    public function index(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
        $this->display();
    }
	public function SignList(){
		 if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		if (I('post.page') == '') {
            return;
        }
		if (I('post.limit') == '') {
            return;
        }
		$this->_currentPage = I('post.page/d');
		$this->_itemCountAPage = I('post.limit/d');
		$mysql = M('Anti');
		$ret = $mysql->select();
		$list = $mysql->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
		$date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
				  "data" => $list
               ];
		echo json_encode($date);
	}
	public function AntiDel(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		$mysql = M('Anti');
		$anti = I('post.anti');
        $ret = $mysql->where("anti = '$anti'")->delete();
		$date=[
                  'status'=>0,
                  'msg'=>($ret?'删除成功':'删除失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function EditSign(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		$mysql = M('Anti');
		$sid = I('post.id');
		$ret = $mysql->create();
        $ret = $mysql->where("id = '$sid'")->save($ret);
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'更新成功':'更新失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function AddSign(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		$mysql = M('Anti');
		$ret = $mysql->create();
        $ret = $mysql->add();
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'添加成功':'添加失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
}