<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Tool\HJCTool;

require getcwd() . '/Application/Home/Common/Email/email.class.php';

class ForgetController extends BaseController {
    public function index() {
		$this->assign('name', C('NAME'));
        $this->display();
    }

    public function Send() {
        if (!$this->check_verify(I('post.captcha'))) {
			$date=[
                  'status'=> 0,
                  'msg'=> '验证码错误'
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
        $username = I('post.username');
        $email = I('post.email');
        $mysql = M('User');
        $ret = $mysql->where("username = '$username' AND email = '$email'")->find();
        if (!$ret) {
			$date=[
                  'status'=>0,
                  'msg'=>'用户名或邮箱不正确'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
        $time = date('Y-m-d H:i:s', time());
        if ($ret['forget_time']) {
            $second = strtotime($time) - strtotime($ret['forget_time']);
            if ($second < 1 * 60) {
				$date=[
                  'status'=>0,
                  'msg'=>'1分钟之内请勿重复提交'
                      ];
		        $this->ajaxReturn(json_encode($date),'JSON');
            }
        }
        $newPwd = $this->rand();
        $secretPwd = HJCTool::secret($newPwd);
		$mysql->forget_time = $time;
		$mysql->password = $newPwd;
        $updateret = $mysql->where("username = '$username'")->save();
        if ($updateret) {
            $this->sendEmail($username, $newPwd, $email);
        } else {
			$date=[
                  'status'=>0,
                  'msg'=>'未知错误'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }

    }
    private function rand( $len = 6 ) {
        $data = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str  = '';
        while ( strlen( $str ) < $len ) {
            $str .= substr( $data, mt_rand( 0, strlen( $data ) - 1 ), 1 );
        }
        return $str;
    }
    private function sendEmail($username, $password, $smtpemailto) {
    $mail = new \PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP();
    $mail->SMTPDebug = 0;                 
    $mail->SMTPAuth = true;
    $mail->Host = 'smtp.qq.com';
    $mail->Port = '25';
    $mail->Username = C('EMAIL_USER');
    $mail->Password = C('EMAIL_PASS');
    $mail->SetFrom(C('EMAIL_USER'), C('NAME'));
    $mail->AddReplyTo(null, null);
    $mail->Subject = C('NAME').'('.$smtpemailto.')找回密码';
    $mail->MsgHTML($username . ",您好！您的新密码为: " . $password . "。请尽快登录并修改密码！");
    $mail->AddAddress($smtpemailto, null);
    $rs =  $mail->Send() ? true : $mail->ErrorInfo;
    if(!$rs){
            $date=[
                  'status'=>0,
                  'msg'=>'未知错误'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        } else {
			$date=[
                  'status'=>1,
                  'msg'=>'邮件已发送至:'.$smtpemailto.' 请及时查看'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
    }
}