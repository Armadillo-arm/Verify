<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Tool\HJCTool;

class LoginController extends BaseController {
    public function index() {
		$this->assign('name', C('NAME'));
        $this->display();
    }
    public function Login() {
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
        $mysql = M('User');
        $user_ret = $mysql->where("username = '" . $username . "'")->find();
        if (!$user_ret) {
			$date=[
                  'status'=>0,
                  'msg'=>'用户不存在'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        } else {
            if ($user_ret['password'] != $password) {
				$date=[
                      'status'=>0,
                      'msg'=>'密码不正确'
                    ];
		        $this->ajaxReturn(json_encode($date),'JSON');
            }
			if (strtotime(date('Y-m-d H:i:s', time())) - strtotime($user_ret['expire_time']) > 0) {
			   $date=[
                      'status'=>0,
                      'msg'=>'账号已到期'
                    ];
		       $this->ajaxReturn(json_encode($date),'JSON');
            }
        }

        $id = $user_ret['id'];
        $lastlogin_time = $user_ret['currentlogin_time'] ? : date('Y-m-d H:i:s', time());
        $lastlogin_ip = $user_ret['currentlogin_ip'];
        $currentlogin_ip = HJCTool::getRealIP();
        $currentlogin_time = date('Y-m-d H:i:s', time());
        $login_count = $user_ret['login_count'] + 1;
		$mysql->lastlogin_time = $lastlogin_time;
		$mysql->lastlogin_ip = $lastlogin_ip;
		$mysql->currentlogin_ip = $currentlogin_ip;
		$mysql->currentlogin_time = $currentlogin_time;
		$mysql->login_count = $login_count;
        $ret = $mysql->where("id = '$id'")->save();
        if ($ret) {
            $_SESSION['user'] = $username;
            $_SESSION['last_logn_time'] = time();
            $date=[
                      'status'=>1,
                      'msg'=>'登录成功 欢迎使用'.C('NAME')
                    ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        } else {
			$date=[
                      'status'=>0,
                      'msg'=>'登录失败 未知异常'
                    ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
    }

}