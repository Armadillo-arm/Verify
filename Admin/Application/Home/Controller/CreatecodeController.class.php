<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class CreatecodeController extends BaseController {
    private $_softArray;
    public function index(){
        $this->_softArray = $this->getSoftwareList2();
		$this->assign('softlist', $this->_softArray);
        $this->display();
    }
	public function Code() {
		if (I('post.count/d') == '') {
            return;
        }
		if (I('post.modules/d') == '') {
            return;
        }
		if (I('post.time/d') == '') {
            return;
        }
		$count = I('post.count/d');
		$modules = I('post.modules/d');
		$mark = I('post.mark');
		$time = I('post.time/d');
		$softId = I('post.soft/d');
		$userId = $this->getUserId();
		$softname=null;
		$this->_softArray = $this->getSoftwareList2();
        foreach ($this->_softArray as $arr) {
            if ($arr['id'] == $softId) {
                $softname = $arr['name'];
                break;
            }
        }
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
        $mysql = M('Regcode');
        $codes = array();
        $time = date('Y-m-d H:i:s',time());
		
		$ret = $mysql->where("user_id = '$userId' AND beginuse_time IS NULL")->select();
        $unUse = sizeof($ret);
        $max = C('CARD');
        if ($unUse + $count > $max) {
			$date=[
                  'status'=> 1,
                  'msg'=> '未使用注册码张数不能超过' . $max . '张,您最多还可以生成' . ($max - $unUse) . '张'
                  ];
		    $this->ajaxReturn(json_encode($date,true),'JSON');
        }
		for ($i = 0; $i<$count; $i++) {
            $code = $secret->createRandRegisterCode();
			$mysql->code = $code;
			$mysql->user_id = $userId;
			$mysql->software_id = $softId;
			$mysql->all_minutes = $all_minutes;
			$mysql->produce_time = $time;
			$mysql->time_str = $timeStr;
			$mysql->software_name = $softname;
			$mysql->mark = $mark;
            $soft_ret = $mysql->add();
            if (!$soft_ret) {
                $date=[
                  'status'=> 1,
                  'msg'=> '数据刷新出现问题 请重试'
                      ];
		        $this->ajaxReturn(json_encode($date,true),'JSON');
            }
			array_push($codes, '注册码:'.$code . '  时长:'.$timeStr);
        }
		$date=[
                  'status'=> 0,
                  'msg'=> implode('|',$codes)
               ];
		$this->ajaxReturn(json_encode($date,true),'JSON');
	}
}