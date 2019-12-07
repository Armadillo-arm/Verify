<?php
namespace Proxy\Controller;

use Common\Controller\BaseProxyController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class CreatecodeController extends BaseProxyController {
    public function index(){
        $this->_softArray = $this->getSoftwareList();
		$this->assign('softlist', $this->_softArray);
        $this->display();
    }
	public function InitPrice(){
		if (I('post.softid/d') == '') {
            return;
        }
		$softid = I('post.softid/d');
		$proxy_user = $this->getUser();
	    $mysql = M('Price');
		$ret = $mysql->where("soft_id = '$softid'")->find();
		$rate = array();
		if($proxy_user['proxy_id'] == 0){
		   $ret['hour'] += round($ret['hour'] * $proxy_user['rate'],2);
		   $ret['day'] += round($ret['day'] * $proxy_user['rate'],2);
		   $ret['month'] += round($ret['month'] * $proxy_user['rate'],2);
		   $ret['season'] += round($ret['season'] * $proxy_user['rate'],2);
		   $ret['year'] += round($ret['year'] * $proxy_user['rate'],2);
		}else{
			$mysql_user = M('Proxy');
			$proxy_id = $proxy_user['proxy_id'];
            $user_ret = $mysql_user->where("id = '$proxy_id'")->find();
			array_push($rate,$user_ret['rate']);
			while($user_ret['proxy_id'] != 0){
				$proxy_id = $user_ret['proxy_id'];
				$user_ret = $mysql_user->where("id = '$proxy_id'")->find();
				array_push($rate,$user_ret['rate']);
			}
			foreach($rate as $value){
				$ret['hour'] += round($ret['hour'] * $value,2);
		        $ret['day'] += round($ret['day'] * $value,2);
		        $ret['month'] += round($ret['month'] * $value,2);
		        $ret['season'] += round($ret['season'] * $value,2);
		        $ret['year'] += round($ret['year'] * $value,2);
            }
		    $ret['hour'] += round($ret['hour'] * $proxy_user['rate'],2);
		    $ret['day'] += round($ret['day'] * $proxy_user['rate'],2);
		    $ret['month'] += round($ret['month'] * $proxy_user['rate'],2);
		    $ret['season'] += round($ret['season'] * $proxy_user['rate'],2);
		    $ret['year'] += round($ret['year'] * $proxy_user['rate'],2);
		}
		$_SESSION['hour'] = $ret['hour'];
		$_SESSION['day'] = $ret['day'];
		$_SESSION['month'] = $ret['month'];
		$_SESSION['season'] = $ret['season'];
		$_SESSION['year'] = $ret['year'];
		$this->ajaxReturn(json_encode($ret,true),'JSON');
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
		$time = I('post.time/d');
		$proxy_user = $this->getUser();
		if($_SESSION['hour'] == 0 || $_SESSION['day'] == 0 || $_SESSION['month'] == 0 || $_SESSION['season'] == 0 || $_SESSION['year'] == 0){
			$date=[
                  'status'=> 1,
                  'msg'=> '上级未分配卡密价格 请联系上级'
                  ];
		    $this->ajaxReturn(json_encode($date,true),'JSON');
		}
		switch ($modules){
			case '1'://小时卡
			$money = $_SESSION['hour'] * $count * $time;
			break;
			case '2'://天卡
			$money = $_SESSION['day'] * $count * $time;
			break;
			case '3'://月卡
			$money = $_SESSION['month'] * $count * $time;
			break;
			case '4'://季卡
			$money = $_SESSION['season'] * $count * $time;
			break;
			case '5'://年卡
			$money = $_SESSION['year'] * $count * $time;
			break;
		}
		if($money > $proxy_user['money']){
		    $date=[
                  'status'=> 1,
                  'msg'=> '可用余额不足'.$money.'你还需要充值'.($money-$proxy_user['money']).'元'
                  ];
		    $this->ajaxReturn(json_encode($date,true),'JSON');
		}
		$mark = I('post.mark');
		$softId = I('post.soft/d');
		$userId = $this->getUserId();
		$softname=null;
		$this->_softArray = $this->getSoftwareList();
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
		
		$ret = $mysql->where("proxy_id = '$userId' AND beginuse_time IS NULL")->select();
        $unUse = sizeof($ret);
        $max= C('CARD');
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
			$mysql->proxy_id = $userId;
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
		$proxy_money = $proxy_user['money'] - $money;
		$proxy = M('Proxy');
		$proxy->where("id = '$userId'")->setField('money',$proxy_money);
		$mysql_buyproxy = M('Buyproxy');
		$proxy_id = $this->getUserId();
		$proxy_username = $this->getUser()['username'];
		$mysql_buyproxy->username = $proxy_username;
		$mysql_buyproxy->proxy_id = $proxy_id;
		$mysql_buyproxy->price_type = $modules;
		$mysql_buyproxy->proxy_money = $proxy_money;
		$mysql_buyproxy->money = $money;
		$mysql_buyproxy->count = $count;
		$mysql_buyproxy->software_name = $softname;
		$mysql_buyproxy->add();
		$date=[
                  'status'=> 0,
                  'msg'=> implode('|',$codes)
               ];
		$this->ajaxReturn(json_encode($date,true),'JSON');
	}
}