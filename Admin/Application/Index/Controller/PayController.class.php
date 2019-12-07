<?php
namespace Index\Controller;
use Think\Controller;
use Home\Tool\HJCTool;



class PayController extends Controller {
    public function index(){
		$this->assign('name', C('NAME'));
		$this->assign('fkw', C('FKW'));
        $this->display();
    }
    public function Check() {
        if (I('post.card', '', '/^[0-9A-Z]{12}$/') == '') {
			$date=[
                      'status'=> 0 ,
                      'msg'=> '充值卡格式错误:请输入12位大写英文字母或数字'
                    ];
	        $this->ajaxReturn(json_encode($date),'JSON');
        }
		$username = I('post.username');
		$User = M('User');
		$user_ret = $User->where("username = '$username'")->find();
		if(!$user_ret){
			$date=[
                      'status'=> 0 ,
                      'msg'=> '用户名不存在'
                    ];
	        $this->ajaxReturn(json_encode($date),'JSON');
		}
        $card = I('post.card');
        $Card = M('Card');
        $card_ret = $Card->where("card = '$card'")->find();
        if (!$card_ret) {
             $date=[
                      'status'=> 0 ,
                      'msg'=> '充值卡不存在'
                    ];
	         $this->ajaxReturn(json_encode($date),'JSON');
        }
		if ($card_ret['state'] == 0){
		     $date=[
                      'status'=> 0 ,
                      'msg'=> '充值卡已被使用'
                    ];
	         $this->ajaxReturn(json_encode($date),'JSON');
	    }
		$hours = $card_ret['all_minutes'] * 60;
		$out = strtotime($user_ret['expire_time']) < time() ? time() : strtotime($user_ret['expire_time']);
		$expire_time = date('Y-m-d H:i:s', $out + $hours);
		$update_ret = $User->where("username = '$username'")->setField('expire_time',$expire_time);
		if (!$update_ret) {
			 $date=[
                      'status'=> 0 ,
                      'msg'=> '充值失败'
                    ];
	         $this->ajaxReturn(json_encode($date),'JSON');
        }
		$update_ret = $Card->where("card = '$card'")->setField('state',0);
		if (!$update_ret) {
		     $date=[
                      'status'=> 0 ,
                      'msg'=> '充值异常'
                    ];
	         $this->ajaxReturn(json_encode($date),'JSON');
        }else{
			 $date=[
                      'status'=> 1 ,
                      'msg'=> '用户名:'.$username.'<br>'.'充值时长:'.$card_ret['time_str'].'<br>'.'账号可用时间:'.$expire_time
                    ];
	         $this->ajaxReturn(json_encode($date),'JSON');
		}
    }
}