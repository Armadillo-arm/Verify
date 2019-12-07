<?php
// 所有控制器的父控制器

namespace Common\Controller;
use Home\Tool\HJCTool;
use Think\Controller;

class BaseController extends Controller {
    private $_currentPage = 1;
    private $_itemCountAPage = 10;
    private $_pageCount;
    private $_userId = -1;
    public function getVerifyImg() {
        $config = array(
            'length' => 4,
            'useNoise' => true,
            'codeSet' => 'ABCDEFGHJKMNPRSTUVWXYZ',
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }
    public function check_verify($code, $id = ''){
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }
    protected function isEmpty($str){
        return $str == '';
    }

    public function __construct() {
        parent::__construct();
        if (strstr($_SERVER[REQUEST_URI], '/Home/Auth') || strstr($_SERVER[REQUEST_URI], '/intro')) {
            return;
        }
        if (!$_SESSION['user']) {
            if (strstr($_SERVER[REQUEST_URI], '/Prpxy') || strstr($_SERVER[REQUEST_URI], '/Home/login') || strstr($_SERVER[REQUEST_URI], '/Home/reg') || strstr($_SERVER[REQUEST_URI], '/Home/forget')) {
            } else {
                $this->signout();
            }
        } else {
			$mysql_user = M('User');
            $user_ret = $mysql_user->where("username = '{$_SESSION['user']}'")->find();
			if (strtotime(date('Y-m-d H:i:s', time())) - strtotime($user_ret['expire_time']) > 0) {
               $this->signout();
            }
            if ($_SESSION['last_logn_time']) {
                $minute = (time() - $_SESSION['last_logn_time']) / 60;
                if ($minute > 60) {
                    $this->signout();
                }
            }
            $_SESSION['last_logn_time'] = time();
        }
        $this->assign('username', $_SESSION['user']);
    }
    protected function signout(){
        unset($_SESSION['user']);
        HJCTool::alertToLocation(null, '/Home/login');
    }
    protected function getUser(){
        $mysql_user = M('User');
        $user_ret = $mysql_user->where("username = '{$_SESSION['user']}'")->find();
        if (!$user_ret) {
            $this->signout();
        }
        return $user_ret;
    }
    protected function getUserId(){
        if ($this->_userId != -1) return $this->_userId;
        $mysql_user = M('User');
        $user_ret = $mysql_user->where("username = '{$_SESSION['user']}'")->find();
        if (!$user_ret) {
            $this->signout();
        }
        $this->_userId = $user_ret['id'];
        return $this->_userId;
    }
    protected function getSoftwareList() {
        $userId = $this->getUserId();
        $mysql_soft = M('Software');
        $soft_ret = $mysql_soft->where("user_id = '$userId'")->select();
        if (!$soft_ret) {
            return '';
        }
        return $soft_ret;
    }
    protected function getSoftwareList2() {
        $userId = $this->getUserId();
        $mysql_soft = M('Software');
		$soft_ret = $mysql_soft->where("user_id = '$userId' AND authmode = 0")->select();
        if (!$soft_ret) {
            return '';
        }
        return $soft_ret;
    }
}