<?php
namespace Proxy\Controller;
use Common\Controller\BaseProxyController;
use Home\Tool\HJCTool;

class ProxyController extends BaseProxyController {
    public function index(){
		$softwares = $this->getSoftwareList();
        $this->assign('softlist', $softwares);
        $this->display();
    }
	public function ProxyList(){
		if (I('post.page') == '') {
            return;
        }
		if (I('post.limit') == '') {
            return;
        }
		$user_id = $this->getUserId();
		$this->_currentPage = I('post.page/d');
		$this->_itemCountAPage = I('post.limit/d');
		$mysql = M('Proxy');
		$ret = $mysql->where("proxy_id = '$user_id'")->select();
		$list = $mysql->where("proxy_id = '$user_id'")->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
		$date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
				  "data" => $list
               ];
		echo json_encode($date);
	}
	public function ProxyDel(){
		$mysql = M('Proxy');
		$id = I('post.id');
		$curr_proxy = $mysql->where("id = '$id'")->find();
		$proxy_user = $this->getUser();
		$user_id = $this->getUserId();
		$proxy_money = $proxy_user['money'] + $curr_proxy['money'];
        $ret = $mysql->where("id = '$id'")->delete();
		$mysql->where("id = '$user_id'")->setField('money',$proxy_money);
		$regcode = M('Regcode');
		$regcode->where("proxy_id = '$$id'")->delete();
		$date=[
                  'status'=>0,
                  'msg'=>($ret?'删除成功':'删除失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function EditProxy(){
		$mysql = M('Proxy');
		$sid = I('post.id');
		$money = I('post.money');
		$user_id = $this->getUserId();
		if($money < 0){
			$date=[
                  'status'=>0,
                  'msg'=>'可用余额不能为负数'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
		}
		$curr_proxy = $mysql->where("id = '$sid'")->find();
		$proxy_user = $this->getUser();
		if($proxy_user['money'] < $money-$curr_proxy['money']){
		   $date=[
                  'status'=>0,
                  'msg'=>'你的可用余额不足以给下级代理卡可用余额'
              ];
		   $this->ajaxReturn(json_encode($date),'JSON');
		}
		$proxy_money = $proxy_user['money'] - ($money-$curr_proxy['money']);
		$mysql->where("id = '$user_id'")->setField('money',$proxy_money);
		$proxy = M('Proxy');
		$ret = $proxy->create();
        $ret = $proxy->where("id = '$sid'")->save($ret);
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'更新成功':'更新失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function AddProxy(){
		$proxy_user = $this->getUser();
		if($proxy_user['is_addproxy'] == 0){
			$date=[
                  'status'=>0,
                  'msg'=>'你没有开通代理权限的功能暂时无法使用 请联系上级开通权限'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
		}
		$mysql = M('Proxy');
		$money = I('post.money');
		if($money < 0){
			$date=[
                  'status'=>0,
                  'msg'=>'可用余额不能为负数'
              ];
		    $this->ajaxReturn(json_encode($date),'JSON');
		}
		if($proxy_user['money'] < $money){
		   $date=[
                  'status'=>0,
                  'msg'=>'你的可用余额不能低于代理余额 代理余额多少是直接扣除你的余额'
              ];
		   $this->ajaxReturn(json_encode($date),'JSON');
		}
		$proxy_money = $proxy_user['money'] - $money;
		$user_id = $this->getUserId();
		$mysql->where("id = '$user_id'")->setField('money',$proxy_money);
		$proxy = M('Proxy');
		$ret = $proxy->create();
		$ret['proxy_id'] = $user_id;
        $ret = $proxy->add($ret);
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'添加成功':'添加失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
}