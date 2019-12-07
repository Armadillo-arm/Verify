<?php
namespace Auth\Controller;

use Think\Controller;
use Home\Tool\Secret;
use Home\Tool\HJCTool;
require getcwd() . '/Application/Auth/Common/Des/DES.php';

class VerifyController extends Controller
{
    private $key;
	private $pri;
	private $pub;
	public function GetPub(){
		$this->pub  = file_get_contents(APP_PATH . "Auth/Common/Rsa/Public.key");
		Header('Public:'.base64_encode($this->pub));
		$this->Result();
	}
	private function lzy($url = ''){
		    $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($curl);
			preg_match_all('|<iframe(.+?)src="(.+?)"(.+?)frameborder="0"|i', $result, $datarr);
			$fndata = $datarr[2][1];
            curl_close($curl);
			
		
			$curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://www.lanzous.com'.$fndata.'?'.str_replace("/fn?","",$fndata));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            $result = curl_exec($curl);
			preg_match('|sign(.*)\'|', $result, $sign);
            curl_close($curl);
			
			
            $url = "https://www.lanzous.com/ajaxm.php";
            $refer = 'https://www.lanzous.com'.$fndata;
            $post_data = array ("action" => "downprocess","sign" => str_replace("sign':","",$sign[0]),"ves" => '1');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_REFERER, $refer);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($output);
			return $result->dom.'/file/'.$result->url;
	}
	public function Index(){
		   $OP = I("server.HTTP_API");
		   $this->pri = file_get_contents(APP_PATH . "Auth/Common/Rsa/Private.key");
		   $this->pub  = file_get_contents(APP_PATH . "Auth/Common/Rsa/Public.key");
		   /*switch(C('ENCRYPTION')){
			   case 0:
			   break;
			   case 1:
			   $pub  = file_get_contents(APP_PATH . "Auth/Common/Rsa/Public.key");
			   $pri  = file_get_contents(APP_PATH . "Auth/Common/Rsa/Private.key");
			   
               $encode = openssl_public_encrypt($OP, $encrypted, openssl_get_publickey($pub)) ? base64_encode($encrypted) : null;
			   echo '公钥加密:'.$encode.'<br/>';
			   
			   $decode = openssl_private_decrypt(base64_decode($encode), $encrypted, openssl_get_privatekey($pri)) ? $encrypted : null;
			   echo '私钥解密:'.$decode.'<br/>';
			   
			   $encode = openssl_private_encrypt($OP, $encrypted, openssl_get_privatekey($pri)) ? base64_encode($encrypted) : null;
			   echo '私钥加密:'.$encode.'<br/>';
			   
			   $decode = openssl_public_decrypt(base64_decode($encode), $encrypted, openssl_get_publickey($pub)) ? $encrypted : null;
			   echo '公钥解密:'.$decode.'<br/>';
			   exit;
			   break;
		   }*/
		   if($OP == '')$this->Result();
		   if(I("server.HTTP_KEY") == '')$this->Result();
		   $this->key = substr(md5(md5(md5(I("server.HTTP_KEY")))),0,8);
		   $data = array('code' => 404,'msg' => '','data' => array());
		   switch($OP){
		     case 'PanGolin_GetAds'://取广告流
			      $Ads = M("Ads");
				  $data['code'] = 200;
				  $data['msg'] = '成功';
				  $data['data'] = $Ads->select();
				  switch(C('ENCRYPTION')){
					  case 0://DES
					  header('Model:0');
					  header('Result:'. \DES::encrypt($this->key, json_encode($data)));
					  break;
					  case 1://RSA
					  header('Model:1');
					  $plainData = str_split(json_encode($data), 117);
                      foreach ($plainData as $chunk) {
                               $partialEncrypted = '';
                               openssl_private_encrypt($chunk, $partialEncrypted, openssl_get_privatekey($this->pri));
                               $encrypted .= $partialEncrypted;
                      }
                      $encrypted = base64_encode($encrypted);
					  echo $encrypted;
					  
					  
					  $plainData = str_split(base64_decode($encrypted), 128);
                      foreach ($plainData as $chunk) {
                               $partialEncrypted = '';
                               openssl_public_decrypt($chunk, $partialEncrypted, openssl_get_publickey($this->pub));
                               $encrypted .= $partialEncrypted;
                      }
					  echo $encrypted;
					  header('Result:'. $encrypted);
					  break;
				  }
		          $this->Result();
			 break;
			 case 'PanGolin_GetNotice'://取公告
			      $Notice = M("Notice");
				  $data['code'] = 200;
				  $data['msg'] = '成功';
				  $data['data'] = $Notice->select();
				  header('Result:'. \DES::encrypt($this->key, json_encode($data)));
		          $this->Result();
			 break;
			 case 'PanGolin_GetModel'://取模块
			      $Model = M("Model");
				  $data['code'] = 200;
				  $data['msg'] = '成功';
				  $data['data'] = $Model->select();
				  $pos = 0;
				  foreach($data['data'] as $value){
					  if(strpos($value['model_res_url'], 'https://www.lanzous.com') === 0){
						  $data['data'][$pos]['model_res_url'] = $this->lzy($value['model_res_url']);
					  }
					  $pos += 1;
				  }
				  header('Result:'. \DES::encrypt($this->key, json_encode($data)));
		          $this->Result();
			 break;
			 case 'PanGolin_GetConfInfo'://取配置信息
			      $data = ["code" => 200
		                  ,"msg" => '成功'
		                  ,"fkw" => C('FKW')
		                  ,"share_msg" => C('SHARE_MSG')
		                  ,"group" => C('GROUP')
		                  ,"qq" => C('QQ')
						  ,"more" => C('MORE')
						  ,"disclaimer" => C('DISCLAIMER')
						  ,"start_url" => C('START_URL')];
				  $this->Result($data);
			 break;
			 case 'PanGolin_CreateUser'://用户注册
			      $username = I("server.HTTP_USERNAME");
                  $password = HJCTool::secret(I("server.HTTP_PASSWORD"));
				  $email    = I("server.HTTP_EMAIL");
				  $User = M('User');
                  if ($User->where("username = '$username'")->find()) {
					   $data['msg'] = '用户名不存在';
                       $this->Result($data);
                  }
		          if($username == C('ADMIN_USER')){
			           $time = new \DateTime('2099-12-30 00:00:00');
			           $expire_time = date('Y-m-d H:i:s', $time->format("U"));
		          } else {
			           $expire_time = date('Y-m-d H:i:s', time()+(C('GIVE')*60));
		          }
				  $User->username = $username;
		          $User->password = $password;
		          $User->email = $email;
		          $User->reg_time = date('Y-m-d H:i:s', time());
		          $User->expire_time = $expire_time;
                  $ret = $User->add();
                  if ($ret) {
			          $data = ["code" => 200,"msg" => '注册成功'];
					  $this->Result($data);
                  }
			 break;
			 case 'PanGolin_UserLogin'://用户登录
			      
			      $username = I("server.HTTP_USERNAME");
                  $password = HJCTool::secret(I("server.HTTP_PASSWORD"));
                  $User = M('User');
                  $user_ret = $User->where("username = '$username'")->find();
                  if (!$user_ret) {
					   $data['msg'] = '用户名不存在';
                       $this->Result($data);
                  } else {
                      if ($user_ret['password'] != $password) {
                          $data['msg'] = '密码不正确';
                          $this->Result($data);
                         }
                  }
		          if (strtotime(date('Y-m-d H:i:s', time())) - strtotime($user_ret['expire_time']) > 0) {
                      $data['msg'] = '已到期';
                      $this->Result($data);
                  }
				  $_SESSION['user'] = $username;
                  $_SESSION['last_logn_time'] = time();
				  $Soft = M('Software');
				  $soft_count = sizeof($Soft->where("user_id = '{$user_ret['id']}'")->select());
				  $Recode = M('Regcode');
				  $card_count = sizeof($Recode->where("user_id = '{$user_ret['id']}'")->select());
				  $user_ret['soft_count'] = $soft_count;
				  $user_ret['card_count'] = $card_count;
				  $user_ret['cookie'] = session_id();
				  echo $_COOKIE['PHPSESSID'];
				  $data['code'] = 200;
                  $data['msg'] = '验证成功';
				  $data['data'] = $user_ret;
                  $this->Result($data);
			 break;
			 case 'PanGolin_UserPay'://用户充值
			      $username = I("server.HTTP_USERNAME");
                  $card = I("server.HTTP_CARD");
                  $User = M('User');
                  $user_ret = $User->where("username = '$username'")->find();
                  if (!$user_ret) {
					   $data['msg'] = '用户名不存在';
                       $this->Result($data);
                  }
				  $Card = M('Card');
				  $card_ret = $Card->where("card = '$card'")->find();
				  if (!$card_ret) {
					   $data['msg'] = '充值卡不存在';
                       $this->Result($data);
                  }
				  if ($card_ret['state'] == 0){
		               $data['msg'] = '充值卡已被使用';
                       $this->Result($data);
				  }
				  $hours = $card_ret['all_minutes'] * 60;
		          $out = strtotime($user_ret['expire_time']) < time() ? time() : strtotime($user_ret['expire_time']);
		          $expire_time = date('Y-m-d H:i:s', $out + $hours);
				  $update_ret = $User->where("username = '$username'")->setField('expire_time',$expire_time);
				  if (!$update_ret) {
					   $data['msg'] = '充值失败';
                       $this->Result($data);
                  }
				  $update_ret = $Card->where("card = '$card'")->setField('state',0);
				  if (!$update_ret) {
					   $data['msg'] = '充值异常';
                       $this->Result($data);
                  }else{
					   $data['code'] = 200;
					   $data['msg'] = '充值成功';
                       $this->Result($data);
				  }
			 break;
			 case 'PanGolin_GetCode'://机器码取后台未到期的注册码
                  $mac = I("server.HTTP_MAC");
				  $appid = I("server.HTTP_APPID");
				  $Soft = M('Software');
				  $soft_ret = $Soft->where("id = $appid")->find();
				  if (!$soft_ret) {
					   $data['msg'] = '验证不通过,Appid不正确,没有该软件! 商户审核请联系QQ'.C('QQ');
                       $this->Result($data);
                  }
				  if ($soft_ret['frozen'] == 1) {
					   $data['msg'] = '验证不通过,软件被禁用!';
                       $this->Result($data);
                  }
				  $Regcode = M('Regcode');
				  $regcode_ret = $Regcode->where("software_id = $appid AND overdue = 0 AND computer_uid = '{$mac}'")->find();
				  if (!$regcode_ret) {
					   $data['msg'] = '机器码未找到';
                       $this->Result($data);
                  }
				  $card = $regcode_ret['code'];
				  $regcode_ret = $Regcode->where("code = '{$card}'")->find();
				  if (!$regcode_ret) {
					   $data['msg'] = '注册码未找到';
                       $this->Result($data);
                  }
				  if ($regcode_ret['overdue'] == 1) {
					   $data['msg'] = '注册码已过期';
                       $this->Result($data);
                  }
				  if ($regcode_ret['frozen'] == 1) {
					   $data['msg'] = '注册码被冻结';
                       $this->Result($data);
                  }
				  $data['code'] = 200;
                  $data['msg'] = '获取成功';
				  $data['data'] = $regcode_ret;
                  $this->Result($data);
			 break;
			 case 'PanGolin_GetSoftInfo'://获取对应应用信息
			      $appid = I("server.HTTP_APPID");
				  $Soft = M('Software');
				  $soft_ret = $Soft->where("id = $appid")->find();
				  if (!$soft_ret) {
					   $data['msg'] = '验证不通过,Appid不正确,没有该软件! 商户审核请联系QQ'.C('QQ');
                       $this->Result($data);
                  }
				  if ($soft_ret['frozen'] == 1) {
					   $data['msg'] = '验证不通过,软件被禁用!';
                       $this->Result($data);
                  }
				  $user_id = $soft_ret['user_id'];
				  $User = M('User');
				  $user_ret = $User->where("id = '$user_id'")->find();
                  if (!$user_ret) {
					   $data['msg'] = '授权用户不存在';
                       $this->Result($data);
                  }
				  if (strtotime(date('Y-m-d H:i:s', time())) - strtotime($user_ret['expire_time']) > 0) {
			           $data['msg'] = '授权用户已到期';
                       $this->Result($data);
                  }
				  $data['code'] = 200;
                  $data['msg'] = '获取成功';
				  $data['data'] = $soft_ret;
				  $this->Result($data);
			 break;
			 case 'PanGolin_Bug'://反馈提交
				  $Bug = M('Advise');
				  $content = htmlspecialchars_decode(I("server.HTTP_BUG"));
				  $submitTime = date('Y-m-d H:i:s', time());
				  $submitIP = HJCTool::getRealIP();
				  $username = I("server.HTTP_USERNAME");
				  $data_bug = array();
				  $data_bug['content'] = base64_decode($content);
				  $data_bug['submit_time'] = $submitTime;
                  $data_bug['submit_ip'] = $submitIP;
				  $data_bug['username'] = $username == '' ? $submitIP : $username;
                  $Bug->add($data_bug);
				  $data['code'] = 200;
                  $data['msg'] = '提交成功';
				  $this->Result($data);
			 break;
			 case 'PanGolin_Verify'://授权码验证
				  $appid = I("server.HTTP_APPID");
				  $code = I("server.HTTP_CODE");
				  $mac = I("server.HTTP_MAC");
				  $Soft = M('Software');
				  $soft_ret = $Soft->where("id = $appid")->find();
				  if (!$soft_ret) {
					   $data['msg'] = '验证不通过,Appid不正确,没有该软件! 商户审核请联系QQ'.C('QQ');
                       $this->Result($data);
                  }
				  if ($soft_ret['frozen'] == 1) {
					   $data['msg'] = '验证不通过,软件被禁用!';
                       $this->Result($data);
                  }
				  $user_id = $soft_ret['user_id'];
				  $User = M('User');
				  $user_ret = $User->where("id = '$user_id'")->find();
                  if (!$user_ret) {
					   $data['msg'] = '授权用户不存在';
                       $this->Result($data);
                  }
				  if (strtotime(date('Y-m-d H:i:s', time())) - strtotime($user_ret['expire_time']) > 0) {
			           $data['msg'] = '授权用户已到期';
                       $this->Result($data);
                  }
				  //注册码验证
				  $Recode = M('Regcode');
				  $code_ret = $Recode->where("code = '$code'")->find();
				  if (!$code_ret) {
					   $data['msg'] = '注册码不存在';
                       $this->Result($data);
                  }
				  if ($code_ret['overdue'] == 1){
					   $Recode->where("code = '$code'")->setField('isonline',0);
		               $data['msg'] = '注册码已过期';
                       $this->Result($data);
				  }
				  if ($code_ret['frozen'] == 1){
					   $Recode->where("code = '$code'")->setField('isonline',0);
		               $data['msg'] = '注册码被冻结';
                       $this->Result($data);
				  }
				  if ($code_ret['expire_time']){
					   if (strtotime(date('Y-m-d H:i:s', time())) - strtotime($code_ret['expire_time']) > 0) {
						   $Recode->where("code = '$code'")->setField('overdue',1);
					       $Recode->where("code = '$code'")->setField('isonline',0);
		                   $data['msg'] = '注册码已过期';
                           $this->Result($data);
					   }
				  }
				  if ($code_ret['computer_uid'] != ''){
					   if($code_ret['computer_uid'] != $mac && $soft_ret['bindmode'] == '0'){
						   $data['msg'] = '登录失败,该注册码已绑定其他设备';
                           $this->Result($data);
					   }
				  }
				  if ($code_ret['software_id'] != $appid){
		               $data['msg'] = '登录失败,注册码不属于该软件';
                       $this->Result($data);
				  }
				  $hours = $code_ret['all_minutes'] / 60;
                  $expire_time = $code_ret['expire_time'];
                  $last_time = date('Y-m-d H:i:s', time());
                  $last_ip = HJCTool::getRealIP();
                  $use_count = $code_ret['use_count'] + 1;
				  switch ($soft_ret['bindmode']) {
                          case '0':
                          $token = substr(md5($mac . $code . time()), 0, 5);
                          break;
                          case '1':
                          $token = substr(md5($mac . $code), 0, 5);
                          break;
                          case '2':
                          $token = substr(md5($code), 0, 5);
                          break;
                 }
				 if ($code_ret['beginuse_time']  == ''){
		               $beginuse_time = date('Y-m-d H:i:s', time());
                       $expire_time = date('Y-m-d H:i:s', strtotime("+{$hours} hour"));
					   $update = array('beginuse_time' => $beginuse_time
					                ,'expire_time' => $expire_time
									,'computer_uid' => $mac
									,'last_time' => $last_time
									,'last_ip' => $last_ip
									,'use_count' => 1
									,'isonline' => 1
									,'token' => $token);
					   $Recode->where("code = '$code'")->setField($update);
				  } else {
					   $expire_time = date('Y-m-d H:i:s', strtotime("+{$hours} hour"));
				       $update = array('beginuse_time' => $beginuse_time
					                ,'expire_time' => $expire_time
									,'computer_uid' => $mac
									,'last_time' => $last_time
									,'last_ip' => $last_ip
									,'use_count' => $use_count
									,'isonline' => 1
									,'token' => $token);
					   $Recode->where("code = '$code'")->setField($update);
				  }
				  $data['code'] = 200;
                  $data['msg'] = '验证成功';
				  $data['token'] = $token;
				  $data['time'] = $expire_time;
				  $this->Result($data);
			 break;
			 case 'PanGolin_Trial'://试用
			      $appid = I("server.HTTP_APPID");
				  $mac = I("server.HTTP_MAC");
				  $Soft = M('Software');
				  $soft_ret = $Soft->where("id = $appid")->find();
				  if (!$soft_ret) {
					   $data['msg'] = '验证不通过,Appid不正确,没有该软件! 商户审核请联系QQ'.C('QQ');
                       $this->Result($data);
                  }
				  if ($soft_ret['frozen'] == 1) {
					   $data['msg'] = '验证不通过,软件被禁用!';
                       $this->Result($data);
                  }
				  $user_id = $soft_ret['user_id'];
				  $User = M('User');
				  $user_ret = $User->where("id = '$user_id'")->find();
                  if (!$user_ret) {
					   $data['msg'] = '授权用户不存在';
                       $this->Result($data);
                  }
				  if (strtotime(date('Y-m-d H:i:s', time())) - strtotime($user_ret['expire_time']) > 0) {
			           $data['msg'] = '授权用户已到期';
                       $this->Result($data);
                  }
				  $last_ip = HJCTool::getRealIP();
                  $last_time = date('Y-m-d H:i:s', time());
				  $try_minutes = $soft_ret['try_minutes'];
                  $try_count = $soft_ret['try_count'];
                  if ($try_minutes <= 0 || $try_count <= 0) {
					  $data['msg'] = '试用失败,软件不支持试用';
                      $this->Result($data);
                  }
                  $token = substr(md5($mac . time()), 0, 5);
				  $Trial = M('Trial');
                  $trial_ret = $Trial->where("computer_uid = '{$mac}' AND software_id = $appid")->find();
				  if(!$trial_ret){
					  $data_info['computer_uid'] = $mac;
                      $data_info['software_id'] = $appid;
					  $data_info['last_ip'] = $last_ip;
                      $data_info['last_time'] = $last_time;
					  $data_info['token'] = $token;
					  $update_ret = $Trial->add($data_info);
					  if($update_ret){
                         $expire_time = date('Y-m-d H:i:s', strtotime("+{$try_minutes} minute"));
					     $data['code'] = 200;
                         $data['msg'] = '试用成功!还可以试用'. ($try_count - 1) . '次';
				         $data['token'] = $token;
				         $data['time'] = $expire_time;
						 $this->Result($data);
					  }
				  } else {
					  $remainder_count = $try_count - $trial_ret['has_try_count'];
					  if ($remainder_count > 0) {
						  $has_try_count = $trial_ret['has_try_count'] + 1;
						  $data_info['has_try_count'] = $has_try_count;
					      $data_info['last_ip'] = $last_ip;
                          $data_info['last_time'] = $last_time;
					      $data_info['token'] = $token;
					      $update_ret = $Trial->where("computer_uid = '{$mac}' AND software_id = $appid")->save($data_info);
					      if($update_ret){
                             $expire_time = date('Y-m-d H:i:s', strtotime("+{$try_minutes} minute"));
					         $data['code'] = 200;
                             $data['msg'] = '试用成功!还可以试用'. ($remainder_count - 1) . '次';
				             $data['token'] = $token;
				             $data['time'] = $expire_time;
							 $this->Result($data);
					      }
					  } else {
						  $data['msg'] = '试用失败,试用次数已用完';
                          $this->Result($data);
					  }
				  }
			 break;
			 case 'PanGolin_Check'://检测
			      $appid = I("server.HTTP_APPID");
				  $mac = I("server.HTTP_MAC");
				  $token = I("server.HTTP_TOKEN");
				  $type = I("server.HTTP_TYPE");
				  $code = I("server.HTTP_CODE");
				  $Soft = M('Software');
				  $soft_ret = $Soft->where("id = $appid")->find();
				  if (!$soft_ret) {
					   $data['msg'] = '验证不通过,Appid不正确,没有该软件! 商户审核请联系QQ'.C('QQ');
                       $this->Result($data);
                  }
				  if ($soft_ret['frozen'] == 1) {
					   $data['msg'] = '验证不通过,软件被禁用!';
                       $this->Result($data);
                  }
				  $user_id = $soft_ret['user_id'];
				  $User = M('User');
				  $user_ret = $User->where("id = '$user_id'")->find();
                  if (!$user_ret) {
					   $data['msg'] = '授权用户不存在';
                       $this->Result($data);
                  }
				  if (strtotime(date('Y-m-d H:i:s', time())) - strtotime($user_ret['expire_time']) > 0) {
			           $data['msg'] = '授权用户已到期';
                       $this->Result($data);
                  }
				  switch($type){
					  case 'formal'://注册码
					  $Recode = M('Regcode');
				      $code_ret = $Recode->where("code = '$code'")->find();
				      if (!$code_ret) {
					       $data['msg'] = '注册码不存在';
                           $this->Result($data);
                      }
				      if ($code_ret['overdue'] == 1){
					       $Recode->where("code = '$code'")->setField('isonline',0);
		                   $data['msg'] = '注册码已过期';
                           $this->Result($data);
				      }
				      if ($code_ret['frozen'] == 1){
					       $Recode->where("code = '$code'")->setField('isonline',0);
		                   $data['msg'] = '注册码被冻结';
                           $this->Result($data);
				      }
				      if ($code_ret['expire_time']){
					      if (strtotime(date('Y-m-d H:i:s', time())) - strtotime($code_ret['expire_time']) > 0) {
						      $Recode->where("code = '$code'")->setField('overdue',1);
					          $Recode->where("code = '$code'")->setField('isonline',0);
		                      $data['msg'] = '注册码已过期';
                              $this->Result($data);
					      }
				      }
					  if ($code_ret['token'] != $token) {
						  $data['msg'] = '验证不通过,禁止多开';
                          $this->Result($data);
                      }
				      $Recode->where("code = '$code'")->setField('isonline',1);
				      $data['code'] = 200;
                      $data['msg'] = '校验成功';
				      $this->Result($data);
					  break;
					  case 'trial'://试用
					  $Trial = M('Trial');
                      $trial_ret = $Trial->where("computer_uid = '{$mac}' AND software_id = $appid")->find();
					  if(!$trial_ret){
					     $data['msg'] = '验证不通过';
                         $this->Result($data);
					  }
					  if ($trial_ret['computer_uid'] != $mac) {
						  $data['msg'] = '验证不通过,机器码被改变';
                          $this->Result($data);
                      }
					  $cle = time() - strtotime($trial_ret['last_time']);
                      $m = $cle / 60;
                      if ($m > $soft_ret['try_minutes']) {
						  $data['msg'] = '验证不通过,试用到期';
                          $this->Result($data);
                      } else {
						  $data['code'] = 200;
                          $data['msg'] = '校验成功';
						  $this->Result($data);
                      }
					  break;
				  }
			 break;
			 case 'PanGolin_Query'://查码
			      $code = I("server.HTTP_CODE");
				  $Recode = M('Regcode');
				  $code_ret = $Recode->where("code = '$code'")->find();
				  if (!$code_ret) {
					   $data['msg'] = '注册码不存在';
                       $this->Result($data);
                  }
				  $data['code'] = 200;
                  $data['msg'] = '获取成功';
				  $data['data'] = $code_ret;
                  $this->Result($data);
			 break;
			 case 'PanGolin_CheckVer'://检测最新版
			      $Ver = I("server.HTTP_VER");
				  $Version = M('Version');
				  $Ver_ret = $Version->where("ver_state = 1 AND ver_number > '$Ver'")->order('ver_number DESC')->select();
				  if(!$Ver_ret){
				      $data['msg'] = '已是最新版';
                      $this->Result($data);
				  }
				  $data['code'] = 200;
                  $data['msg'] = '有新版本';
				  $data['data'] = $Ver_ret[0];
                  $this->Result($data);
			 break;
			 case 'PanGolin_Query_Ver'://获取版本信息
			      $Ver = I("server.HTTP_VER");
				  $Version = M('Version');
				  $Ver_ret = $Version->where("ver_number = '$Ver'")->find();
				  if(!$Ver_ret){
				      $data['msg'] = '获取失败';
                      $this->Result($data);
				  }
				  $data['code'] = 200;
                  $data['msg'] = '获取成功';
				  $data['data'] = $Ver_ret;
                  $this->Result($data);
			 break;
			 case 'PanGolin_GetPage_data'://获取快发卡商品信息
				  $kfk = I("server.HTTP_URL");
				  $url = 'https://api.kuaifaka.com/purch/get_page_data';
                  $header = array();
                  $header[] = 'authtype:web';
                  $header[] = 'link:'.str_replace('https://www.kuaifaka.com/purchasing?link=','',$kfk);;
                  $data = [
                          'need_fl_info' => 1,
                          'need_pr_info' => 1,
	                      'need_pay_info' => 1];
                  $curl = curl_init();
                  curl_setopt($curl, CURLOPT_URL, $url);
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                  curl_setopt($curl, CURLOPT_POST, 1);
                  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                  $result = json_decode(curl_exec($curl), true);
                  curl_close($curl);
				  $Goods = array();
                  $buyer_token = $result['data']['buyer_token'];
                  foreach($result['data']['goods'] as $value){
					  $good = array();
					  $good['name'] = $value['name'];
					  $good['cate_id'] = $value['cate_id'];
					  $products_info = array();
					  foreach($value['products'] as $products){
						  $products_info['name'] = $products['name'];
						  $products_info['product_id'] = $products['product_id'];
						  $good['products'][] = $products_info;
                      }
					  array_push($Goods,$good);
                  }
				  $data = array();
				  $data['code'] = 200;
                  $data['msg'] = '获取成功';
				  $data['buyer_token'] = $buyer_token;
				  $data['WxPay'] = true;
				  $data['ZfbPay'] = true;
				  $data['QQPay'] = true;
				  $data['data'] = $Goods;
				  header('Result:'. \DES::encrypt($this->key, json_encode($data)));
				  echo '欢迎使用'.C('NAME').' 作者QQ1834661238 接验证定做 软件定做 接口开发';
		          exit; 
			 break;
			 case 'PanGolin_Build_order'://快发卡创建订单
				  $product_id = I('server.HTTP_PRODUCTID');
				  $Pay_Type = I('server.HTTP_PAYTYPE');
				  $buyer_token = I('server.HTTP_BUYERTOKEN');
				  $kfk = I("server.HTTP_URL");
				  $url = 'https://api.kuaifaka.com/purch/create_order_num';
                  $header = array();
                  $header[] = 'authtype:web';
                  $header[] = 'link:'.str_replace('https://www.kuaifaka.com/purchasing?link=','',$kfk);;
                  $curl = curl_init();
                  curl_setopt($curl, CURLOPT_URL, $url);
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                  curl_setopt($curl, CURLOPT_POST, 1);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                  $result = json_decode(curl_exec($curl), true);
                  curl_close($curl);
				  //得到订单号
				  $order_num = $result['data']['order_num'];
				  //得到RSA加密数据
				  $data = $result['data']['key'];
                  $pub  = file_get_contents(APP_PATH . "Auth/Common/public.key");
                  $key1 = openssl_get_publickey($pub);
                  $encode = openssl_public_encrypt($data, $encrypted, $key1) ? base64_encode($encrypted) : null;
				  //创建订单
				  $url = 'https://api.kuaifaka.com/purch/build_order';
                  $header = array();
                  $header[] = 'authtype:web';
                  $header[] = 'link:'.str_replace('https://www.kuaifaka.com/purchasing?link=','',$kfk);
				  $data = [
                          'pr_id' => $product_id,
                          'num' => 1,
	                      'order_push' => 0,
		                  'order_num' => $order_num,
		                  'contact' => '1233210123',
		                  'buyer_token' => $buyer_token,
		                  'order_key' => $encode];
				  switch($Pay_Type){
					  case 1://支付宝
					  $data['pay_type'] = 12;
					  break;
					  case 2://微信
					  $data['pay_type'] = 9;
					  break;
					  case 3://QQ钱包
					  $data['pay_type'] = 3;
					  break;
				  }
                  $curl = curl_init();
                  curl_setopt($curl, CURLOPT_URL, $url);
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                  curl_setopt($curl, CURLOPT_POST, 1);
				  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                  $result = json_decode(curl_exec($curl), true);
                  curl_close($curl);
				  if($result['state'] != 'ok'){
					  $data = array();
					  $data['msg'] = '订单创建失败';
					  $data['code'] = 404;
                      $this->Result($data);
				  }
				  //获取支付接口信息
				  $url = 'https://api.kuaifaka.com/purch/get_pay_info';
                  $header = array();
                  $header[] = 'authtype:web';
                  $header[] = 'link:'.str_replace('https://www.kuaifaka.com/purchasing?link=','',$kfk);
				  $data = [
                          'order_num' => $order_num
                          ];
                  $curl = curl_init();
                  curl_setopt($curl, CURLOPT_URL, $url);
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                  curl_setopt($curl, CURLOPT_POST, 1);
				  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                  $result = json_decode(curl_exec($curl), true);
                  curl_close($curl);
				  $data = array();
				  $data['code'] = 200;
                  $data['msg'] = '获取成功';
				  
				  $pay_info = array();
				  $pay_info['payurl'] = $result['msg'];
				  $pay_info['paytype'] = $result['data'];
				  $pay_info['order_num'] = $order_num;
				  $pay_info['paysoft'] = $Pay_Type;
				  $data['data'] = $pay_info;
				  $this->Result($data);
			 break;
			 case 'PanGolin_Get_order_state'://判断是否支付并获取卡密
			      $order_num = I("server.HTTP_ORDER");
			      $url = 'https://api.kuaifaka.com/purch/get_order_state';
                  $header = array();
                  $header[] = 'authtype:web';
				  $data = [
                          'order_num' => $order_num
                          ];
                  $curl = curl_init();
                  curl_setopt($curl, CURLOPT_URL, $url);
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                  curl_setopt($curl, CURLOPT_POST, 1);
				  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                  $result = json_decode(curl_exec($curl), true);
                  curl_close($curl);
				  if($result['data']['code'] != 0){
					  $data = array();
					  $data['code'] = 404;
					  $data['msg'] = '等待付款';
					  $this->Result($data);
				  }
				  $url = 'https://api.kuaifaka.com/pc/cards_query';
                  $header = array();
                  $header[] = 'authtype:web';
                  $pub  = file_get_contents(APP_PATH . "Auth/Common/public.key");
                  $key1 = openssl_get_publickey($pub);
                  $encode = openssl_public_encrypt($order_num, $encrypted, $key1) ? base64_encode($encrypted) : null;
				  $data = [
                          'order_num' => $encode
                          ];
                  $curl = curl_init();
                  curl_setopt($curl, CURLOPT_URL, $url);
                  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
                  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
                  curl_setopt($curl, CURLOPT_POST, 1);
				  curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                  $result = json_decode(curl_exec($curl), true);
                  curl_close($curl);
				  $code_info = array();
                  $code_info['Result'] = $result['data']['card_contents'][0]['no'];
				  $data = array();
				  $data['code'] = 200;
				  $data['msg'] = '支付成功';
				  $data['data'] = $code_info;
				  $this->Result($data);
			 break;
			 default:
			      echo '欢迎使用'.C('NAME').' 作者QQ1834661238 接验证定做 软件定做 接口开发';
				  exit;
			 break;
		   }
	}
	//返回数据并加密
    private function Result($data = '')
	{
		 if($data != ''){
		    $Anti = M('Anti');
            $ret = $Anti->select();
			$sign = array();
			$sign['msg'] = C('ANTI_MSG');
			$sign['data'] = $ret;
			header('Result:'. \DES::encrypt($this->key, json_encode($data,JSON_FORCE_OBJECT)));
			header('Sign:'.\DES::encrypt($this->key, json_encode($sign)));
		 }
		 echo '欢迎使用'.C('NAME').' 作者QQ1834661238 接验证定做 软件定做 接口开发';
		 exit;
	}
}