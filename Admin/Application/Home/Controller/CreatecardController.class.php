<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class CreatecardController extends BaseController {
    private $finalSql;

    public function index() {
        if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
        $this->display();
    }

    public function Card() {
	    if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		if (I('post.count/d') == '') {
            return;
        }
		if (I('post.time/d') == '') {
            return;
        }
		if (I('post.modules/d') == '') {
            return;
        }
		$count = I('post.count/d');
		$time = I('post.time/d');
		$modules = I('post.modules/d');
		$all_minutes;
		$timeStr;
		switch ($modules){
			case '1'://小时卡
			$all_minutes = $time * 60;
            $timeStr = $time . '小时';
			break;
			case '2'://天卡
			$all_minutes = $time * 60 * 24;
            $timeStr = $time . '天';
			break;
			case '3'://月卡
			$all_minutes = $time * 60 * 24 * 30;
            $timeStr = $time . '月';
			break;
			case '4'://季卡
			$all_minutes = $time * 60 * 24 * 91;
            $timeStr = $time . '季';
			break;
			case '5'://年卡
			$all_minutes = $time * 60 * 24 * 365;
            $timeStr = $time . '年';
			break;
		}
		$secret = new Secret();
        $mysql = M('Card');
        $codes = array();
        $time = date('Y-m-d H:i:s',time());
        for ($i = 0; $i<$count; $i++) {
            $code = $secret->createRandRegisterCode();
			$mysql->card = $code;
			$mysql->all_minutes = $all_minutes;
			$mysql->produce_time = $time;
			$mysql->time_str = $timeStr;
			$mysql->state = 1;
            $soft_ret = $mysql->add();
            if (!$soft_ret) {
				$date=[
                  'status'=> 1,
                  'msg'=> '数据刷新出现问题 请重试'
                      ];
		        $this->ajaxReturn(json_encode($date,true),'JSON');
            }
            array_push($codes, '充值卡:'.$code . '  时长:'.$timeStr);
        }
		$date=[
                  'status'=> 0,
                  'msg'=> implode('|',$codes)
               ];
		$this->ajaxReturn(json_encode($date,true),'JSON');
	}
}