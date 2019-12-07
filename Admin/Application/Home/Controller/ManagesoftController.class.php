<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;

class ManagesoftController extends BaseController {
    public function index(){
        $this->display();
    }
	public function SoftList(){
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
		$userId = $this->getUserId();
		$mysql = M('Software');
		$ret = $mysql->where("user_id = '$userId'")->select();
		if ($search == null) {
            $list = $mysql->where("user_id = '$userId'")->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
		} else {
		    $list = $mysql->where("user_id = '$userId' AND name = '{$search['name']}'")->select();
		}
		$date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
				  "data" => $list
               ];
		echo json_encode($date);
	}

    public function DeleteSoft(){
        if (I('post.id') == '') return;
        $sid = I('post.id');
        $userId = $this->getUserId();
        $mysql = M('Software');
        $updateret = $mysql->where("id = '$sid' AND user_id = '$userId'")->delete();
        if ($updateret) {
            $mysql = M('Regcode');
            $mysql->where("software_id = '$sid' AND user_id = '$userId'")->delete();
            $date=[
                  'status'=>0,
                  'msg'=>'删除成功'
                  ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        } else {
            $date=[
                  'status'=>1,
                  'msg'=>'删除失败'
                  ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
    }
	public function FrozenSoft(){
		if (I('post.id') == '') return;
		$id = I('post.id');
		$frozen = trim(I('post.frozen'));
		$mysql = M('Software');
		$ret = $mysql->where("id = '$id'")->setField('frozen',$frozen);
		if (!$ret) {
            $date=[
                  'status'=> 1,
                  'msg'=>'修改失败'
                  ];
		    echo json_encode($date);
        } else {
            $date=[
                  'status'=> 0,
                  'msg'=>'修改成功'
                  ];
		    echo json_encode($date);
        }
	}
	public function EditSoft(){
		if (I('post.id') == '') return;
		$this->EditAuth();
	}
	private function EditAuth(){
		$sid = I('post.id');
		$name = trim(I('post.name'));
		$mysql = M('Software');
		$ret = $mysql->create();
		$updateret = $mysql->where("id = '$sid'")->save($ret);
		if (!$updateret) {
            $date=[
                  'status'=> 1,
                  'msg'=>'修改失败'
                  ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        } else {
                $mysql = M('Regcode');
                $mysql->where("software_id = '{$sid}'")->setField('software_name',$name);
                $date=[
                  'status'=> 0,
                  'msg'=>'修改成功'
                  ];
		        $this->ajaxReturn(json_encode($date),'JSON');
        }
	}
	
}