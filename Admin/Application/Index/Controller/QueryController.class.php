<?php
namespace Index\Controller;
use Home\Tool\HJCTool;
use Think\Controller;


class QueryController extends Controller {
    public function index(){
		$this->assign('name', C('NAME'));
        $this->display();
    }
    public function Check() {
        if (I('post.code', '', '/^[0-9A-Z]{12}$/') == '') {
			$date=[
                      'status'=> 0 ,
                      'msg'=> '注册码格式错误:请输入12位大写英文字母或数字'
                    ];
	        $this->ajaxReturn(json_encode($date),'JSON');
        }
        $card = I('post.code');
        $mysql = M('Regcode');
        $ret = $mysql->where("code = '$card'")->find();
        if (!$ret) {
             $date=[
                      'status'=> 0 ,
                      'msg'=> '注册码不存在'
                    ];
	         $this->ajaxReturn(json_encode($date),'JSON');
        }
        $codes = array();
        array_push($codes,'授权码:'.$card);
		array_push($codes,'所属软件ID:'.$ret['software_id']);
		array_push($codes,'所属软件:'.$ret['software_name']);
		if($ret['proxy_id'] != 0){
		   $Proxy = M('Proxy');
		   $proxy_ret = $Proxy->where("id = '{$ret['proxy_id']}'")->find();
		   array_push($codes,'所属代理:'.$proxy_ret['username']);
		}
		array_push($codes,'生成时间:'.$ret['produce_time']);
        array_push($codes,'时长:'.$ret['time_str']);
        if ($ret['expire_time']) {
           array_push($codes,'激活状态:已激活');
           array_push($codes,'设备串号:'.$ret['computer_uid']);
           array_push($codes,'使用时间:'.$ret['beginuse_time']);
           array_push($codes,'到期时间:'.$ret['expire_time']);
           array_push($codes,'上次登录时间:'.$ret['last_time']);
           array_push($codes,'使用次数:'.$ret['use_count']);
        } else {
           array_push($codes,'激活状态:未激活');
        }
        if ($ret['frozen'] == 1) {
           array_push($codes,'冻结状态:已冻结');
        }else{
           array_push($codes,'冻结状态:未冻结');
        }
        $date=[
                'status'=> 1 ,
                'msg'=> implode("<br>",$codes)
              ];
	    $this->ajaxReturn(json_encode($date),'JSON');
    }
}