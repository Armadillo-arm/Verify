<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class AdminController extends BaseController {
    public function index(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
        $this->display();
    }
	public function EditUser(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		if(I('post.username') == '' )return;
		if(I('post.expire_time') == '')return;
		if(I('post.email') == '')return;
		$username = I('post.username');
		$mysql = M('User');
		$ret = $mysql->create();
		$ret = $mysql->where("username = '$username'")->save($ret);
		$date=[
                  'status'=>0,
                  'msg'=>($ret?'修改成功':'修改失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function table(){
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
		$search = null;
		if (I('post.searchParams') != '') {
			$json = I('post.searchParams');
			$json = html_entity_decode($json);
            $json = stripslashes($json);
			$search = json_decode($json,true);
		}
		$this->_currentPage = I('post.page/d');
		$this->_itemCountAPage = I('post.limit/d');
		$mysql = M('user');
		$ret = $mysql->select();
		if ($search == null) {
            $list = $mysql->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
        } else {
		    $list = $mysql->where("username = '{$search['username']}'")->select();
		}
		$date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
				  "data" => $list
               ];
		echo json_encode($date);
	}
	public function UserDel(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		$mysql = M('User');
		$user = I('post.username');
		$ret = $mysql->where("username = '{$user}'")->delete();
		$date=[
                  'status'=>0,
                  'msg'=>($ret?'删除成功':'删除失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
}