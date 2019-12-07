<?php
namespace Proxy\Controller;

use Common\Controller\BaseProxyController;
use Home\Tool\HJCTool;

class LoginController extends BaseProxyController {
    public function index(){
		$this->assign('name', C('NAME'));
        $this->display();
    }
	public function Login(){
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
        $password = I('post.password');
        $mysql = M('Proxy');
        $user_ret = $mysql->where("username='" . $username . "'")->find();
        if (!$user_ret) {
			$date=[
                  'status'=>0,
                  'msg'=>'代理用户不存在'
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
			if ($user_ret['frozen'] == 1) {
			   $date=[
                      'status'=>0,
                      'msg'=>'账号冻结 无法登录'
                    ];
		       $this->ajaxReturn(json_encode($date),'JSON');
            }
        }
        $_SESSION['user'] = $username;
        $_SESSION['last_logn_time'] = time();
        $date=[
                      'status'=>1,
                      'msg'=>'登录成功 欢迎使用'.C('NAME')
                    ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
}