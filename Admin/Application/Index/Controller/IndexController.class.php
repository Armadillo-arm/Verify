<?php
namespace Index\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
		$this->assign('name', C('NAME'));
		$this->assign('qq', C('QQ'));
        $this->display();
    }
}