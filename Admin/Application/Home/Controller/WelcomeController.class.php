<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class WelcomeController extends BaseController {
    public function index(){
		$this->init();
        $this->display();
    }
	private function init() {
		$user_ret = $this->getUser();
		$this->assign('expire_time', $user_ret['expire_time']);
		$this->assign('name', C('NAME'));
        $this->loadNotice();
        $this->querySoftware();
    }
    private function querySoftware() {
        $userId = $this->getUserId();
        $mysql = M('Regcode');
        $unuse_ret = $mysql->where("user_id = '$userId' AND beginuse_time IS NULL ")->select();
        $use_ret = $mysql->where("user_id = '$userId' AND beginuse_time IS NOT NULL AND overdue = 0")->select();
        $online_ret = $mysql->where("user_id = '$userId' AND isonline = 1")->select();
        $frozen_ret = $mysql->where("user_id = '$userId' AND frozen = 1")->select();
        $this->assign('unuse_count', sizeof($unuse_ret));
        $this->assign('use_count', sizeof($use_ret));
        $this->assign('online_count', sizeof($online_ret));
        $this->assign('frozen_count', sizeof($frozen_ret));
    }
	private function loadNotice() {
        $mysql = M('Notice');
        $ret = $mysql->select();
        $sort = array(
            'direction' => 'SORT_DESC',
            'field'     => 'id',
        );
        $arrSort = array();
        foreach($ret AS $uniqid => $row){
            foreach($row AS $key=>$value){
                $arrSort[$key][$uniqid] = $value;
            }
        }
        if($sort['direction']){
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $ret);
        }
        if ($ret) {
            $this->assign('notices', $ret);
        }
    }
}