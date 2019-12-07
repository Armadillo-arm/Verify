<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class GgController extends BaseController {

    public function index(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
        $this->display();
    }
	public function AddNotice() {
	     if($this->getUser()['username'] != C('ADMIN_USER')){
			  http_response_code(404);
			  return;
         }
		 $creat_time = date('Y-m-d H:i:s', time());
         $content = I('post.content');
         $title = I('post.title');
         $mysql = M('Notice');
		 $mysql->title = $title;
		 $mysql->content = $content;
		 $mysql->creat_time = $creat_time;
         $mysql->add();
		 $date=[
                  'status'=>0,
                  'msg'=>'推送成功'
               ];
		 $this->ajaxReturn(json_encode($date),'JSON');
	}
	public function EditNotice(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		$sid = I('post.id');
		$Notice = M('Notice');
		$ret = $Notice->create();
        $ret = $Notice->where("id = '$sid'")->save($ret);
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'更新成功':'更新失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function  NoticeDel(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		$mysql = M('Notice');
		$id = I('post.id');
        $ret = $mysql->where("id = '$id'")->delete();
		$date=[
                  'status'=>0,
                  'msg'=>($ret?'删除成功':'删除失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function NoticeList(){
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
		$mysql = M('Notice');
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
}