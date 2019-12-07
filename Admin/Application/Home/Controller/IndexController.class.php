<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;
class IndexController extends BaseController {
    public function index(){
        $user_ret = $this->getUser();
        $this->assign('expire_time', $user_ret['expire_time']);
		$this->assign('name', C('NAME'));
        $this->display();
    }
    public function logout() {
        $this->signout();
    }
	public function InitMenu(){
	    if($this->getUser()['username'] == C('ADMIN_USER')){
		   $date=['json'=>'Public/api/init.json'];
		   $this->ajaxReturn(json_encode($date),'JSON');
		}else{
		   $date=['json'=>'Public/api/init_user.json'];
		   $this->ajaxReturn(json_encode($date),'JSON');
		}
	}
}