<?php

namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;

class ChangesoftController extends BaseController {
    private $_softwareName;
    public function index() {
        $this->display();
    }
	public function CreateSoft(){
	    $a = $this->getSoftwareList();
		if ($a != '') {
              if (count($a) == C('SOFT')) {
				  $date=[
                  'status'=>1,
                  'msg'=>'每个用户只能创建'.C('SOFT').'个应用 你已达上限'
                  ];
		          $this->ajaxReturn(json_encode($date),'JSON');
              }
        }
		$this->auth();
	}
    private function auth() {
		$software = trim(I('post.software'));
        $user_id = $this->getUserId();
		$create_time = date('Y-m-d H:i:s', time());
        $mysql = M('Software');
        $ret = $mysql->create();
		$ret['user_id'] = $user_id;
		$ret['create_time'] = $create_time;
		$updateret = $mysql->add($ret);
         if (!$updateret) {
                $date=[
                  'status'=>1,
                  'msg'=>'创建失败'
                ];
		        $this->ajaxReturn(json_encode($date),'JSON');
         } else {
                $date=[
                  'status'=>0,
                  'msg'=>'创建成功'
                ];
		        $this->ajaxReturn(json_encode($date),'JSON');
         }
    }
    
}