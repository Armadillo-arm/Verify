<?php
namespace Common\Controller;

use Home\Tool\HJCTool;
use Think\Controller;

class BaseProxyController extends Controller {
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
        if (!$_SESSION['user']) {
            if (strstr($_SERVER[REQUEST_URI], '/Proxy')) {
            } else {
                $this->signout();
            }
        } else {
			$mysql_user = M('Proxy');
            $user_ret = $mysql_user->where("username = '" . $_SESSION['user'] . "'")->find();
			if ($user_ret['frozen'] == 1) {
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
        HJCTool::alertToLocation(null, '/Proxy/Login');
    }
    protected function getUser(){
        $mysql_user = M('Proxy');
        $user_ret = $mysql_user->where("username = '" . $_SESSION['user'] . "'")->find();
        if (!$user_ret) {
            $this->signout();
        }
        return $user_ret;
    }
    protected function getUserId(){
        if ($this->_userId != -1) return $this->_userId;
        $mysql_user = M('Proxy');
        $user_ret = $mysql_user->where("username = '" . $_SESSION['user'] . "'")->find();
        if (!$user_ret) {
            $this->signout();
        }
        $this->_userId = $user_ret['id'];
        return $this->_userId;
    }
    protected function getSoftwareList() {
        $user = $this->getUser();
		$var = explode(",",$user['soft']);
        $mysql_soft = M('Software');
		$ret = array();
		foreach($var as $value){
			$soft_ret = $mysql_soft->where("id = '" . $value . "'" )->find();
			array_push($ret,$soft_ret);
        }
        
        if (!$ret) {
            return '';
        }
        return $ret;
    }
}