<?php
namespace Home\Controller;
use Common\Controller\BaseController;
use Home\Tool\HJCTool;
use Home\Tool\Secret;
class SettingController extends BaseController {
    public function index() {
        if ($this->getUser() ['username'] != C('ADMIN_USER')) {
            http_response_code(404);
            return;
        }
        $this->assign('sitename', C('NAME'));
        $this->assign('give', C('GIVE'));
        $this->assign('card_count', C('CARD'));
        $this->assign('soft_count', C('SOFT'));
        $this->assign('email', C('EMAIL_USER'));
        $this->assign('email_key', C('EMAIL_PASS'));
        $this->assign('qq', C('QQ'));
        $this->assign('fkw', C('FKW'));
        $this->assign('group', C('GROUP'));
        $this->assign('share_msg', C('SHARE_MSG'));
        $this->assign('more', C('MORE'));
        $this->assign('anti_msg', C('ANTI_MSG'));
        $this->assign('disclaimer', C('DISCLAIMER'));
        $this->assign('start_url', C('START_URL'));
		$this->assign('Encryption', C('ENCRYPTION'));
        $this->display();
    }
    public function SaveSysConfig() {
        if ($this->getUser() ['username'] != C('ADMIN_USER')) {
            http_response_code(404);
            return;
        }
		$this->rmdirr(APP_PATH.'Runtime/');
        $NAME = I('post.sitename');
        $GIVE = I('post.give');
        $CARD = I('post.card_count');
        $SOFT = I('post.soft_count');
        $EMAIL_USER = I('post.email');
        $EMAIL_PASS = I('post.email_key');
        $QQ = I('post.qq');
        $FKW = I('post.fkw');
        $GROUP = I('post.group');
		$ENCRYPTION = I('post.Encryption');
        $SHARE_MSG = str_replace(array(
            "\r\n",
            "\r",
            "\n"
        ) , "\\r\\n", I('post.share_msg'));
        $MORE = I('post.more');
        $ANTI_MSG = str_replace(array(
            "\r\n",
            "\r",
            "\n"
        ) , "\\r\\n", I('post.anti_msg'));
        $DISCLAIMER = str_replace(array(
            "\r\n",
            "\r",
            "\n"
        ) , "\\r\\n", I('post.disclaimer'));
        $START_URL = I('post.start_url');
        $pat = array(
            'NAME',
            'GIVE',
            'CARD',
            'SOFT',
            'EMAIL_USER',
            'EMAIL_PASS',
            'QQ',
            'FKW',
            'GROUP',
            'SHARE_MSG',
            'MORE',
            'ANTI_MSG',
            'DISCLAIMER',
            'START_URL',
			'ENCRYPTION'
        );
        $rep = array(
            $NAME,
            $GIVE,
            $CARD,
            $SOFT,
            $EMAIL_USER,
            $EMAIL_PASS,
            $QQ,
            $FKW,
            $GROUP,
            $SHARE_MSG,
            $MORE,
            $ANTI_MSG,
            $DISCLAIMER,
            $START_URL,
			$ENCRYPTION
        );
        $ret = $this->setconfig($pat, $rep);
        if ($ret) {
            $date = ['status' => 0, 'msg' => '保存成功'];
            $this->ajaxReturn(json_encode($date, true) , 'JSON');
        } else {
            $date = ['status' => 1, 'msg' => '保存失败'];
            $this->ajaxReturn(json_encode($date, true) , 'JSON');
        }
    }
    private function setconfig($pat, $rep) {
        if (is_array($pat) and is_array($rep)) {
            for ($i = 0; $i < count($pat); $i++) {
                $pats[$i] = '/\'' . $pat[$i] . '\'(.*?),/';
                if ($pat[$i] == 'SHARE_MSG' || $pat[$i] == 'ANTI_MSG' || $pat[$i] == 'DISCLAIMER') $reps[$i] = "'" . $pat[$i] . "'" . " => " . '"' . $rep[$i] . '",';
                else $reps[$i] = "'" . $pat[$i] . "'" . " => " . "'" . $rep[$i] . "',";
            }
            $fileurl = APP_PATH . "Common/Conf/config.php";
            $string = file_get_contents($fileurl);
            $string = preg_replace($pats, $reps, $string);
            file_put_contents($fileurl, $string);
            return true;
        } else {
            return flase;
        }
    }
    private function rmdirr($dirname) {
        if (!file_exists($dirname)) {
            return false;
        }
        if (is_file($dirname) || is_link($dirname)) {
            return unlink($dirname);
        }
        $dir = dir($dirname);
        if ($dir) {
            while (false !== $entry = $dir->read()) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
            }
        }
        $dir->close();
        return rmdir($dirname);
    }
}

