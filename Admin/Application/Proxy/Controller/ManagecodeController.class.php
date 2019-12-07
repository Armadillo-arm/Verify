<?php
namespace Proxy\Controller;

use Common\Controller\BaseProxyController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class ManagecodeController extends BaseProxyController {
    public function index() {
        $softwares = $this->getSoftwareList();
        $this->assign('softlist', $softwares);
        $this->display();
    }
    public function CodeList(){
		if (I('post.softid') == '') {
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
		$ret = $mysql->where("software_id = '$softid' AND proxy_id = '$userId'")->select();
		if ($search == null) {
            $list = $mysql->where("software_id = '$softid' AND proxy_id = '$userId'")->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
		} else {
			$list = $mysql->where("software_id = '$softid' AND proxy_id = '$userId' AND code = '{$search['card']}'")->select();
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
        $ret = $mysql->where("code = '$code' AND proxy_id = '$userId'")->delete();
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
	    $code = I('post.code');
        $userId = $this->getUserId();
		$mysql = M('Regcode');
		$ret = $mysql-create();
        $ret = $mysql->where("code = '$code' AND proxy_id = '$userId'")->save($ret);
        $date=[
                  'status'=> ($ret? 0:1),
                  'msg'=> ($ret?'更新成功':'更新成功')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
}