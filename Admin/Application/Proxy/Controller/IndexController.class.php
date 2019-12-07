<?php
namespace Proxy\Controller;

use Common\Controller\BaseProxyController;
use Home\Tool\HJCTool;

class IndexController extends BaseProxyController {
    public function index(){
		$this->assign('username', $this->getUser()['username']);
		$this->assign('name', C('NAME'));
        $this->display();
    }
	public function logout() {
        $this->signout();
    }
}