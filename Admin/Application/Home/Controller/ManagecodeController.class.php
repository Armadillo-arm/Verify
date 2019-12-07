<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class ManagecodeController extends BaseController {
    public function index() {
        $softwares = $this->getSoftwareList2();
        $this->assign('softlist', $softwares);
        $this->display();
    }
    public function CodeList(){
		if (I('post.softid') == '') {
            $date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
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
		$search = null;
		if (I('post.searchParams') != '') {
			$json = I('post.searchParams');
			$json = html_entity_decode($json);
            $json = stripslashes($json);
			$search = json_decode($json,true);
		}
		$softid=I('post.softid/d');
		$userId = $this->getUserId();
		$this->_currentPage = I('post.page/d');
		$this->_itemCountAPage = I('post.limit/d');
		$mysql = M('Regcode');
		$ret = $mysql->where("software_id = '$softid' AND user_id = '$userId'")->select();
		if ($search == null) {
            $list = $mysql->where("software_id = '$softid' AND user_id = '$userId'")->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
        } else {
		    $list = $mysql->where("software_id = '$softid' AND user_id = '$userId'"
			.($search['card'] == null ? '':" AND code = '{$search['card']}'")
			.($search['mark'] == null ? '':" AND mark = '{$search['mark']}'"))->select();
		}
		$date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
				  "data" => $list
               ];
		echo json_encode($date);
	}
    public function CodeDel(){
		if (I('post.code') == '') {
            return;
        }
        $code = I('post.code');
        $userId = $this->getUserId();
        $mysql = M('Regcode');
        $ret = $mysql->where("code = '$code' AND user_id = '$userId'")->delete();
        $date=[
                  'status'=>0,
                  'msg'=>($ret?'删除成功':'删除失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
    }
	public function EditCode(){
		if (I('post.code') == '') {
            return;
        }
		$mysql = M('Regcode');
	    $code = I('post.code');
		$computer_uid = I('post.computer_uid');
		$expire_time = I('post.expire_time');
		$frozen = I('post.frozen');
		$mark = I('post.mark');
        $userId = $this->getUserId();
		$mysql->computer_uid = $computer_uid;
		$mysql->expire_time = $expire_time;
		$mysql->frozen = $frozen;
		$mysql->mark = $mark;
        $ret = $mysql->where("code = '$code' AND user_id = '$userId'")->save();
        $date=[
                  'status'=> ($ret? 0:1),
                  'msg'=> ($ret?'更新成功':'更新成功')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function DelAll(){
		if (I('post.ids') == '') {
            return;
        }
        $id = I('post.ids');
		$ids = explode(',',$id);
		$userId = $this->getUserId();
        $mysql = M('Regcode');
		foreach($ids as $value){
			$mysql->where("id = '$value' AND user_id = '$userId'")->delete();
		}
        $date=[
                  'status'=>0,
                  'msg'=> '删除成功'
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
    }
	public function frozenAll(){
		if (I('post.ids') == '') {
            return;
        }
        $id = I('post.ids');
		$ids = explode(',',$id);
		$userId = $this->getUserId();
        $mysql = M('Regcode');
		foreach($ids as $value){
			$mysql->frozen = 1;
			$mysql->where("id = '$value' AND user_id = '$userId'")->save();
		}
        $date=[
                  'status'=>0,
                  'msg'=> '冻结成功'
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
    }
	public function thawAll(){
		if (I('post.ids') == '') {
            return;
        }
        $id = I('post.ids');
		$ids = explode(',',$id);
		$userId = $this->getUserId();
        $mysql = M('Regcode');
		foreach($ids as $value){
			$mysql->frozen = 0;
			$mysql->where("id = '$value' AND user_id = '$userId'")->save();
		}
        $date=[
                  'status'=>0,
                  'msg'=> '解冻成功'
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
    }
}