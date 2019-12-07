<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Tool\HJCTool;

class RegController extends BaseController {
    public function index() {
		$this->assign('name', C('NAME'));
        $this->display();
    }
    public function Register() {
        if (!$this->check_verify(I('post.captcha'))) {
			$date=[
                  'status'=>0,
                  'msg'=>'验证码错误'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
        if ($this->isEmpty(I('post.username', '', '/^[a-z0-9A-Z]{6,16}$/'))) {
			$date=[
                  'status'=>0,
                  'msg'=>'账号格式错误: 请输入6-16位英文字母或数字'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
        if ($this->isEmpty(I('post.password', '', '/^[a-z0-9A-Z]{6,16}$/'))) {
			$date=[
                  'status'=>0,
                  'msg'=>'密码格式错误: 请输入6-16位英文字母或数字'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
        $username = I('post.username');
        $password = HJCTool::secret(I('post.password'));
        $email = I('post.email');
        $mysql = M('User');
        if ($mysql->where("username='" . $username . "'")->find()) {
			$date=[
                  'status'=>0,
                  'msg'=>'用户已存在'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
		if($username == C('ADMIN_USER')){
			$time = new \DateTime('2099-12-30 00:00:00');
			$expire_time = date('Y-m-d H:i:s', $time->format("U"));
		} else {
			$expire_time = date('Y-m-d H:i:s', time()+(C('GIVE')*60));
		}
		$mysql->username = $username;
		$mysql->password = $password;
		$mysql->email = $email;
		$mysql->reg_time = date('Y-m-d H:i:s', time());
		$mysql->expire_time = $expire_time;
        $ret = $mysql->add();
        if ($ret) {
			$date=[
                  'status'=>1,
                  'msg'=>'注册成功'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
    }

}