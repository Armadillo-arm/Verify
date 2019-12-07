<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;

class ModelController extends BaseController {
    public function index(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
        $this->display();
    }
	public function ModelList(){
		 if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		if (I('post.page') == '') {
            return;
        }
		if (I('post.limit') == '') {
            return;
        }
		$this->_currentPage = I('post.page/d');
		$this->_itemCountAPage = I('post.limit/d');
		$mysql = M('Model');
		$ret = $mysql->select();
		$list = $mysql->limit(abs($this->_currentPage-1) * $this->_itemCountAPage . ", " . $this->_itemCountAPage)->select();
		$date=[
                  'code'=>0,
                  'msg'=>'',
				  'count' => sizeof($ret),
				  "data" => $list
               ];
		echo json_encode($date);
	}
	public function ModelDel(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		$mysql = M('Model');
		$id = I('post.id');
        $ret = $mysql->where("id = '$id'")->delete();
		$date=[
                  'status'=>0,
                  'msg'=>($ret?'删除成功':'删除失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	public function EditModel(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		$sid = I('post.id');
		$model_title = I('post.title');
		$model_describe = I('post.describe');
		$model_whole_name = I('post.whole_name');
		$model_single_code = htmlspecialchars_decode(I('post.single_code'));
		$model_dex_url = I('post.dex_url');
		$model_res_url = I('post.res_url');
		$model_xml = htmlspecialchars_decode(I('post.xml'));
		$model_vip = I('post.vip');
		$model_state = I('post.state');
		$model_img = htmlspecialchars_decode(I('post.img'));
		$model_config_rule = I('post.config_rule');
		$model_replace_activity = I('post.replace_activity');
		
		$Model = M('Model');
		$Model->model_title = $model_title;
		$Model->model_describe = $model_describe;
		$Model->model_whole_name = $model_whole_name;
		$Model->model_single_code = $model_single_code;
		$Model->model_dex_url = $model_dex_url;
		$Model->model_res_url = $model_res_url;
		$Model->model_xml = $model_xml;
		$Model->model_vip = $model_vip;
		$Model->model_state = $model_state;
		$Model->model_img = $model_img;
		$Model->model_config_rule = $model_config_rule;
		$Model->model_replace_activity = $model_replace_activity;
        $ret = $Model->where("id = '$sid'")->save();
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'更新成功':'更新失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
	
	public function AddModel(){
		if($this->getUser()['username'] != C('ADMIN_USER')){
			http_response_code(404);
			return;
        }
		$model_title = I('post.title');
		$model_describe = I('post.describe');
		$model_whole_name = I('post.whole_name');
		$model_single_code = htmlspecialchars_decode(I('post.single_code'));
		$model_dex_url = I('post.dex_url');
		$model_res_url = I('post.res_url');
		$model_xml = htmlspecialchars_decode(I('post.xml'));
		$model_vip = I('post.vip');
		$model_state = I('post.state');
		$model_img = htmlspecialchars_decode(I('post.img'));
		$model_config_rule = I('post.config_rule');
		$model_replace_activity = I('post.replace_activity');
		
		$Model = M('Model');
		$data = array();
		$data['model_title'] = $model_title;
		$data['model_describe'] = $model_describe;
		$data['model_whole_name'] = $model_whole_name;
		$data['model_single_code'] = $model_single_code;
		$data['model_dex_url'] = $model_dex_url;
		$data['model_res_url'] = $model_res_url;
		$data['model_xml'] = $model_xml;
		$data['model_vip'] = $model_vip;
		$data['model_state'] = $model_state;
		$data['model_img'] = $model_img;
		$data['model_config_rule'] = $model_config_rule;
		$data['model_replace_activity'] = $model_replace_activity;
        $ret = $Model->add($data);
		$date=[
                  'status'=>($ret ? 0:1),
                  'msg'=>($ret?'添加成功':'添加失败')
              ];
		$this->ajaxReturn(json_encode($date),'JSON');
	}
}