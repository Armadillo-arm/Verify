<?php
namespace Proxy\Controller;

use Common\Controller\BaseProxyController;
use Home\Tool\HJCTool;

class ContactController extends BaseProxyController {

    public function index() {
        $this->display();
    }
	public function submit() {
		if (I('post.content') == '') {
            return;
        }
		$username = $this->getUser()['username'];
        $submitTime = date('Y-m-d H:i:s', time());
        $submitIP = HJCTool::getRealIP();
        $content = I('post.content');
        $mysql = M('Advise');
		$mysql->username = $username;
		$mysql->submit_time = $submitTime;
		$mysql->submit_ip = $submitIP;
		$mysql->content = $content;
        $mysql->add();
		$date=[
          'status'=>0,
          'msg'=>'提交成功'
         ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
    
}