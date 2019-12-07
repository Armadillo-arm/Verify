<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Home\Tool\HJCTool;

class ModifyPasswordController extends BaseController {
    public function index() {
        $this->display();
    }
	public function Change() {
		if (I('post.old_password') == '' || I('post.new_password') == '') {
            return;
        }
        if (I('post.new_password', '', '/^[a-z0-9A-Z]{6,16}$/') == '') {
            $date=[
              'status'=>1,
              'msg'=>'密码格式错误: 请输入6-16位英文字母或数字'
            ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
        if (I('post.old_password') == I('new_password')) {
			$date=[
              'status'=>1,
              'msg'=>'新密码不可和旧密码相同'
            ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
        $oldpwd = HJCTool::secret(I('post.old_password'));
        $newpwd = HJCTool::secret(I('post.new_password'));
        $userId = $this->getUserId();
        $mysql = M('User');
        $ret = $mysql->where("id = '$userId' AND password = '$oldpwd'")->find();
        if ($ret) {
            $updateret = $mysql->where("id = '$userId'")->setField('password',$newpwd);
            if ($updateret) {
				$date=[
                  'status'=>0,
                  'msg'=>'修改成功,请重新登录!'
                 ];
		        $this->ajaxReturn(json_encode($date),'JSON');
            } else {
                $date=[
                  'status'=>1,
                  'msg'=>'修改失败'
                 ];
		        $this->ajaxReturn(json_encode($date),'JSON');
            }
        } else {
            $date=[
                  'status'=>1,
                  'msg'=>'旧密码不正确'
            ];
		    $this->ajaxReturn(json_encode($date),'JSON');
        }
	}
}