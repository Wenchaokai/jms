<?php
/**
 * 本地用户处理相关函数
 *
 * 本地用户处理相关函数
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: userlocal.php 317 2009-01-06 09:03:33Z juny $
 */



/**
 * 本地用户注册，预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
 * @access     public
 * @return     int    
 */
function jieqi_uregister_lprepare(&$params){
	global $jieqiConfigs;
	global $jieqiLang;
	global $query;
	global $users_handler;
	//载入参数设置
	if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs');
	//载入语言包
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	//初始化查询类
	if(!is_a($query, 'JieqiQueryHandler')){
		jieqi_includedb();
		$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
	}

	//获得注册人ip
	if(empty($params['uip']) || !is_numeric(str_replace('.','',$params['uip']))) $params['uip'] = jieqi_userip();

	//同一个IP重复注册时间限制
	$jieqiConfigs['system']['regtimelimit']=intval($jieqiConfigs['system']['regtimelimit']);
	if($jieqiConfigs['system']['regtimelimit']>0){
		$sql="SELECT * FROM ".jieqi_dbprefix('system_registerip')." WHERE ip='".jieqi_dbslashes($params['uip'])."' AND regtime>".(JIEQI_NOW_TIME - $jieqiConfigs['system']['regtimelimit'] * 3600)." LIMIT 0,1";
		$res=$query->execute($sql);
		if($query->getRow()){
			$params['error'] = sprintf($jieqiLang['system']['user_register_timelimit'], $jieqiConfigs['system']['regtimelimit']);
			if($params['return']) return false;
			else jieqi_printfail($params['error']);
		}
	}

	//变量检查
	$params['username'] = trim($params['username']);

	//用户名转换成小写
	$fromstr = $params['username'];
	$strlen = strlen($fromstr);
	$tmpstr = '';
	for($i = 0; $i < $strlen; $i++){
		if(ord($fromstr[$i]) > 0x80){
			$tmpstr .= $fromstr[$i].$fromstr[$i+1];
			$i++;
		}else{
			$tmpstr .= strtolower($fromstr[$i]);
		}
	}
	$params['username'] = $tmpstr;


	$params['email'] = trim($params['email']);
	$params['password'] = trim($params['password']);
	$params['repassword'] = trim($params['repassword']);
	if(empty($params['checkcode'])) $params['checkcode']='';
	else $params['checkcode'] = trim($params['checkcode']);

	$params['error']='';
	if(!is_a($users_handler, 'JieqiUsersHandler')){
		include_once(JIEQI_ROOT_PATH.'/class/users.php');
		$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
	}
	//检查用户名格式
	if (strlen($params['username'])==0) $params['error'] .= $jieqiLang['system']['need_username'].'<br />';
	elseif(preg_match('/^\s*$|^c:\\con\\con$|[@%,;:\.\|\*\"\'\\\\\/\s\t\<\>\&]|　/is', $params['username'])) $params['error'] .= $jieqiLang['system']['error_user_format'].'<br />';
	elseif($jieqiConfigs['system']['usernamelimit']==1 && !preg_match('/^[A-Za-z0-9]+$/',$params['username'])) $params['error'] .= $jieqiLang['system']['username_need_engnum'].'<br />';

	//检查昵称
	if(isset($params['nickname'])){
		if (strlen($params['nickname'])==0) $params['error'] .= $jieqiLang['system']['need_nickname'].'<br />';
		elseif(preg_match('/^\s*$|^c:\\con\\con$|[@%,;:\.\|\*\"\'\\\\\/\s\t\<\>\&]|　/is', $params['nickname'])) $params['error'] .= $jieqiLang['system']['error_nick_format'].'<br />';
	}else{
		$params['nickname'] = $params['username'];
	}

	//检查Email格式
	if (strlen($params['email'])==0) $params['error'] .= $jieqiLang['system']['need_email'].'<br />';
	elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i",$params['email'])) $params['error'] .= $jieqiLang['system']['error_email_format'].'<br />';

	//检查密码
	if (strlen($params['password'])==0 || strlen($params['repassword'])==0) $params['error'] .= $jieqiLang['system']['need_pass_repass'].'<br />';
	elseif ($params['password'] != $params['repassword']) $params['error'] .= $jieqiLang['system']['password_not_equal'].'<br />';

	//检查用户名是否已注册
	if($users_handler->getByname($params['username'], 3) != false) $params['error'] .= $jieqiLang['system']['user_has_registered'].'<br />';
	//检查昵称是否已注册
	if($params['nickname'] != $params['username'] && $users_handler->getByname($params['nickname'], 3) != false) $params['error'] .= $jieqiLang['system']['nick_has_used'].'<br />';

	//检查Email是否已注册
	if($users_handler->getCount(new Criteria('email', $params['email'], '=')) > 0) $params['error'] .= $jieqiLang['system']['email_has_registered'].'<br />';
	//检查验证码
	if(!empty($jieqiConfigs['system']['checkcodelogin']) && $params['checkcode'] != $_SESSION['jieqiCheckCode']) $params['error'] .= $jieqiLang['system']['error_checkcode'].'<br />';

	//记录注册信息
	if(!empty($params['error'])){
		if($params['return']) return false;
		else jieqi_printfail($params['error']);
	}else{
		return true;
	}
}

/**
 * 本地用户注册，正式处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
 * @access     public
 * @return     int    
 */
function jieqi_uregister_lprocess(&$params){
	global $jieqiConfigs;
	global $jieqiLang;
	global $query;
	global $users_handler;
	//载入参数设置
	if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs');
	//载入语言包
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	//初始化查询类
	if(!is_a($query, 'JieqiQueryHandler')){
		jieqi_includedb();
		$query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
	}

	if(!is_a($users_handler, 'JieqiUsersHandler')){
		include_once(JIEQI_ROOT_PATH.'/class/users.php');
		$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
	}
	include_once(JIEQI_ROOT_PATH.'/lib/text/textfunction.php');

	$newUser = $users_handler->create();
	$newUser->setVar('siteid', JIEQI_SITE_ID);
	$newUser->setVar('uname', $params['username']);
	$newUser->setVar('name', $params['nickname']);
	$newUser->setVar('pass', $users_handler->encryptPass($params['password']));
	$newUser->setVar('groupid', JIEQI_GROUP_USER);
	$newUser->setVar('regdate', JIEQI_NOW_TIME);
	$newUser->setVar('initial', jieqi_getinitial($params['username']));
	$newUser->setVar('sex', $params['sex']);
	$newUser->setVar('email', $params['email']);
	$newUser->setVar('url', $params['url']);
	$newUser->setVar('avatar', 0);
	$newUser->setVar('workid', 0);
	$newUser->setVar('qq', $params['qq']);
	$newUser->setVar('icq', '');
	$newUser->setVar('msn', $params['msn']);
	$newUser->setVar('mobile', '');
	$newUser->setVar('sign', '');
	$newUser->setVar('intro', '');
	$newUser->setVar('setting', '');
	$newUser->setVar('badges', '');
	$newUser->setVar('lastlogin', JIEQI_NOW_TIME);
	$newUser->setVar('showsign', 0);
	$newUser->setVar('viewemail', $params['viewemail']);
	$newUser->setVar('notifymode', 0);
	$newUser->setVar('adminemail', $params['adminemail']);
	$newUser->setVar('monthscore', 0);
	$newUser->setVar('experience', $jieqiConfigs['system']['scoreregister']);
	$newUser->setVar('score', $jieqiConfigs['system']['scoreregister']);
	$newUser->setVar('egold', 0);
	$newUser->setVar('esilver', 0);
	$newUser->setVar('credit', 0);
	$newUser->setVar('goodnum', 0);
	$newUser->setVar('badnum', 0);
	$newUser->setVar('isvip', 0);
	$newUser->setVar('overtime', 0);
	$newUser->setVar('state', 0);
	if (!$users_handler->insert($newUser)){
		$params['uid'] = $newUser->getVar('uid', 'n');
		$params['error'] = $jieqiLang['system']['register_failure'];
		if($params['return']) return false;
		else jieqi_printfail($params['error']);
	}else{

		//自动登录
		//记录注册时间IP
		if($jieqiConfigs['system']['regtimelimit']>0){
			$sql="DELETE FROM ".jieqi_dbprefix('system_registerip')." WHERE regtime<".(JIEQI_NOW_TIME - ($jieqiConfigs['system']['regtimelimit'] > 72 ? $jieqiConfigs['system']['regtimelimit'] : 72) * 3600);
			$query->execute($sql);
			$sql="INSERT INTO ".jieqi_dbprefix('system_registerip')." (ip, regtime, count) VALUES ('".jieqi_dbslashes($params['uip'])."', '".JIEQI_NOW_TIME."', '0')";
			$query->execute($sql);
		}

		//更新在线用户表
		include_once(JIEQI_ROOT_PATH.'/class/online.php');
		$online_handler =& JieqiOnlineHandler::getInstance('JieqiOnlineHandler');
		include_once(JIEQI_ROOT_PATH.'/include/visitorinfo.php');
		$online = $online_handler->create();
		$online->setVar('uid', $newUser->getVar('uid', 'n'));
		$online->setVar('siteid', JIEQI_SITE_ID);
		$online->setVar('sid', session_id());
		$online->setVar('uname', $newUser->getVar('uname', 'n'));
		$tmpvar = strlen($newUser->getVar('name', 'n')) > 0 ? $newUser->getVar('name', 'n') : $newUser->getVar('uname', 'n');
		$online->setVar('name', $tmpvar);
		$online->setVar('pass', $newUser->getVar('pass', 'n'));
		$online->setVar('email', $newUser->getVar('email', 'n'));
		$online->setVar('groupid', $newUser->getVar('groupid', 'n'));
		$tmpvar=JIEQI_NOW_TIME;
		$online->setVar('logintime', $tmpvar);
		$online->setVar('updatetime', $tmpvar);
		$online->setVar('operate', '');
		$tmpvar=VisitorInfo::getIp();
		$online->setVar('ip', $tmpvar);
		$online->setVar('browser', VisitorInfo::getBrowser());
		$online->setVar('os', VisitorInfo::getOS());
		$location=VisitorInfo::getIpLocation($tmpvar);
		if(JIEQI_SYSTEM_CHARSET == 'big5'){
			include_once(JIEQI_ROOT_PATH.'/include/changecode.php');
			$location=jieqi_gb2big5($location);
		}
		$online->setVar('location', $location);
		$online->setVar('state', '0');
		$online->setVar('flag', '0');
		$online_handler->insert($online);

		//设置SESSION
		jieqi_setusersession($newUser);

		//设置COOKIE
		$jieqi_user_info = array();
		$jieqi_user_info['jieqiUserId']=$_SESSION['jieqiUserId'];
		$jieqi_user_info['jieqiUserName']=$_SESSION['jieqiUserName'];
		$jieqi_user_info['jieqiUserGroup']=$_SESSION['jieqiUserGroup'];

		include_once(JIEQI_ROOT_PATH.'/include/changecode.php');
		if(JIEQI_SYSTEM_CHARSET == 'gbk') $jieqi_user_info['jieqiUserName_un']=jieqi_gb2unicode($_SESSION['jieqiUserName']);
		else $jieqi_user_info['jieqiUserName_un']=jieqi_big52unicode($_SESSION['jieqiUserName']);
		$jieqi_user_info['jieqiUserLogin']=JIEQI_NOW_TIME;
		$cookietime=0;
		@setcookie('jieqiUserInfo', jieqi_sarytostr($jieqi_user_info), $cookietime, '/',  JIEQI_COOKIE_DOMAIN, 0);
		$jieqi_visit_info['jieqiUserLogin']=$jieqi_user_info['jieqiUserLogin'];
		$jieqi_visit_info['jieqiUserId']=$jieqi_user_info['jieqiUserId'];
		@setcookie('jieqiVisitInfo', jieqi_sarytostr($jieqi_visit_info), JIEQI_NOW_TIME+99999999, '/',  JIEQI_COOKIE_DOMAIN, 0);

		//推广积分
		if(JIEQI_PROMOTION_REGISTER > 0 && !empty($_COOKIE['jieqiPromotion'])){
			$users_handler->changeCredit(intval($_COOKIE['jieqiPromotion']), intval(JIEQI_PROMOTION_REGISTER), true);
			setcookie('jieqiPromotion', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
		}
	}
	//$params['jumpurl']=JIEQI_URL.'/';
	if(empty($params['jumpurl'])) $params['jumpurl']=JIEQI_URL.'/';
	return true;
}

//*****************************************************************
//*****************************************************************

/**
 * 本地用户登录，预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_ulogin_lprepare(&$params){
	$params['username']=trim($params['username']);
	return true;
}

/**
 * 本地用户登录，正式处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_ulogin_lprocess(&$params){
	global $jieqiLang;
	//载入语言包
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');

	include_once(JIEQI_ROOT_PATH.'/include/checklogin.php');
	if(isset($params['usecookie']) && is_numeric($params['usecookie'])) $params['usecookie']=intval($params['usecookie']);
	else $params['usecookie'] = 0;
	if(empty($params['checkcode'])) $params['checkcode'] = '';
	$islogin = jieqi_logincheck($params['username'], $params['password'], $params['checkcode'], $params['usecookie']);
	if($islogin == 0){
		if(defined('JIEQI_ADMIN_LOGIN')){
			$_SESSION['jieqiAdminLogin']=1;
			$jieqi_online_info = empty($_COOKIE['jieqiOnlineInfo']) ? array() : jieqi_strtosary($_COOKIE['jieqiOnlineInfo']);
			$jieqi_online_info['jieqiAdminLogin']=1;
			@setcookie('jieqiOnlineInfo', jieqi_sarytostr($jieqi_online_info), 0, '/',  JIEQI_COOKIE_DOMAIN, 0);

			//记录登录日志
			include_once(JIEQI_ROOT_PATH.'/class/logs.php');
			$logs_handler = JieqiLogsHandler::getInstance('JieqiLogsHandler');
			$logdata = array('logtype'=>1);
			$logs_handler->addlog($logdata);
		}
		if(empty($params['jumpurl'])) {
			if(!empty($params['jumpreferer']) && !empty($_SERVER['HTTP_REFERER']) && basename($_SERVER['HTTP_REFERER']) != 'login.php') $params['jumpurl']=$_SERVER['HTTP_REFERER'];
			else $params['jumpurl']=JIEQI_URL.'/';
		}
	}else{
		//返回 0 正常, -1 用户名为空 -2 密码为空 -3 用户名或者密码为空
		//-4 用户名不存在 -5 密码错误 -6 用户名或密码错误 -7 校验码错误 -8 帐号已经有人登陆
		switch($islogin){
			case -1:
				$params['error'] = $jieqiLang['system']['need_username'];
				break;
			case -2:
				$params['error'] = $jieqiLang['system']['need_password'];
				break;
			case -3:
				$params['error'] = $jieqiLang['system']['need_userpass'];
				break;
			case -4:
				$params['error'] = $jieqiLang['system']['no_this_user'];
				break;
			case -5:
				$params['error'] = $jieqiLang['system']['error_password'];
				break;
			case -6:
				$params['error'] = $jieqiLang['system']['error_userpass'];
				break;
			case -7:
				$params['error'] = $jieqiLang['system']['error_checkcode'];
				break;
			case -8:
				$params['error'] = $jieqiLang['system']['other_has_login'];
				break;
			case -9:
				$params['error'] = $jieqiLang['system']['user_has_denied'];
				break;
			default:
				$params['error'] = $jieqiLang['system']['login_failure'];
				break;
		}
		$params['errorno'] = $islogin;
		if($params['return']) return false;
		else jieqi_printfail($params['error']);
	}
	return true;
}

//*****************************************************************
//*****************************************************************

/**
 * 本地用户退出，预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_ulogout_lprepare(&$params){
	$params['uid'] = intval($_SESSION['jieqiUserId']);
	return true;
}

/**
 * 本地用户退出，正式处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_ulogout_lprocess(&$params){
	include_once(JIEQI_ROOT_PATH.'/class/online.php');
	$online_handler =& JieqiOnlineHandler::getInstance('JieqiOnlineHandler');
	$criteria = new CriteriaCompo(new Criteria('sid', session_id()));
	$criteria->add(new Criteria('uid', intval($_SESSION['jieqiUserId'])), 'OR');
	$online_handler->delete($criteria);

	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	if (!empty($_COOKIE['jieqiUserInfo'])){
		setcookie('jieqiUserInfo', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
	}
	if (!empty($_COOKIE['jieqiOnlineInfo'])){
		setcookie('jieqiOnlineInfo', '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
	}
	if (!empty($_COOKIE[session_name()])){
		setcookie(session_name(), '', 0, '/', JIEQI_COOKIE_DOMAIN, 0);
	}

	$_SESSION = array();
	@session_destroy();
	return true;
}

//*****************************************************************
//*****************************************************************

/**
 * 本地用户删除，预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_udelete_lprepare(&$params){
	return true;
}

/**
 * 本地用户删除，正式处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_udelete_lprocess(&$params){
	global $users_handler;
	global $jieqiLang;
	//载入语言包
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	if(!is_a($users_handler, 'JieqiUsersHandler')){
		include_once(JIEQI_ROOT_PATH.'/class/users.php');
		$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
	}
	$user=$users_handler->get($params['uid']);
	if(!is_object($user)){
		$params['error'] = LANG_NO_USER;
		$params['username'] = '';
		if($params['return']) return false;
		else jieqi_printfail($params['error']);
	}else{
		$params['username'] = $user->getVar('uname', 'n');
	}

	if(!$users_handler->delete($params['uid'])){
		$params['error'] = $jieqiLang['system']['delete_user_failure'];
		if($params['return']) return false;
		else jieqi_printfail($params['error']);
	}else{
		//记录登录日志
		/*
		include_once(JIEQI_ROOT_PATH.'/class/logs.php');
		$logs_handler = JieqiLogsHandler::getInstance('JieqiLogsHandler');
		$logdata = array('logtype'=>2, 'targetid'=>$user->getVar('uid', 'n'), 'targettitle'=>$user->getVar('uname', 'n'), 'lognote'=>$params['reason'], 'logdata'=>'', 'fromdata'=>$log_fromdata, 'todata'=>'');
		$logs_handler->addlog($logdata);
		*/

		//记录日志
		include_once(JIEQI_ROOT_PATH.'/class/userlog.php');
		$userlog_handler = JieqiUserlogHandler::getInstance('JieqiUserlogHandler');
		$newlog=$userlog_handler->create();
		$newlog->setVar('siteid', JIEQI_SITE_ID);
		$newlog->setVar('logtime', JIEQI_NOW_TIME);
		$newlog->setVar('fromid', $_SESSION['jieqiUserId']);
		$newlog->setVar('fromname', $_SESSION['jieqiUserName']);
		$newlog->setVar('toid', $user->getVar('uid', 'n'));
		$newlog->setVar('toname', $user->getVar('uname', 'n'));
		$newlog->setVar('reason', $params['reason']);
		$newlog->setVar('chginfo', $jieqiLang['system']['delete_user']);
		$newlog->setVar('chglog', '');
		$newlog->setVar('isdel', '1');
		$newlog->setVar('userlog', serialize($user->getVars()));
		$userlog_handler->insert($newlog);
		return true;
	}
}

//*****************************************************************
//*****************************************************************

/**
 * 本地用户编辑，预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_uedit_lprepare(&$params){
	global $users_handler;
	global $jieqiPower;
	global $jieqiUsersStatus;
	global $jieqiUsersGroup;
	global $jieqiLang;
	global $jieqiConfigs;
	//载入参数设置
	if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs');
	//载入语言包
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	if(!is_a($users_handler, 'JieqiUsersHandler')){
		include_once(JIEQI_ROOT_PATH.'/class/users.php');
		$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
	}
	$user=$users_handler->get($params['uid']);
	if(!is_object($user)){
		$params['error'] = LANG_NO_USER;
		if($params['return']) return false;
		else jieqi_printfail($params['error']);
	}else{
		$params['username'] = $user->getVar('uname', 'n');
	}
	//管理员修改用户资料的等级
	$tmpstr = $_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);
	if(empty($_SESSION['jieqiAdminLogin']) || strstr($tmpstr, 'useredit.php')){
		$params['adminlevel'] = 0;
	}else{
		if(!isset($jieqiPower['system'])) jieqi_getconfigs('system', 'power');
		if(jieqi_checkpower($jieqiPower['system']['deluser'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) $params['adminlevel'] = 5;
		elseif(jieqi_checkpower($jieqiPower['system']['adminvip'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) $params['adminlevel'] = 4;
		elseif(jieqi_checkpower($jieqiPower['system']['changegroup'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) $params['adminlevel'] = 3;
		elseif(jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) $params['adminlevel'] = 2;
		else $params['adminlevel'] = 0;
	}

	//判断登录用户是不是本人，session对应或者密码对应都行
	if($params['adminlevel'] == 0){
		if($params['uid'] == $_SESSION['jieqiUserId']) $params['adminlevel'] = 1;
		elseif(!empty($params['oldpass']) && ($user->getVar('pass', 'n') == $params['oldpass'] || $user->getVar('pass', 'n') == $users_handler->encryptPass($params['oldpass']))) $params['adminlevel'] = 1;
	}
	
	if($params['adminlevel'] == 0){
		$params['error'] = LANG_NO_PERMISSION;
		if($params['return']) return false;
		else jieqi_printfail($params['error']);
	}
	
	$params['error']='';

	//只允许本人修改
	if($params['adminlevel'] == 1){
		//检查Email格式
		if(isset($params['email'])){
			$params['email'] = trim($params['email']);
			if (strlen($params['email'])==0) $params['error'] .= $jieqiLang['system']['need_email'].'<br />';
			elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i",$params['email']) ) $params['error'] .= $jieqiLang['system']['error_email_format'].'<br />';

			//检查Email是否已注册
			if($params['email'] != $user->getVar('email','n')){
				if($users_handler->getCount(new Criteria('email', $params['email'], '=')) > 0) $params['error'] .= $jieqiLang['system']['email_has_registered'].'<br />';
			}
		}

		//修改昵称
		$params['changenick']=false;
		if(isset($params['nickname']) && $user->getVar('name', 'n') != $params['nickname']){
			if($params['nickname'] != ''){
				if($users_handler->getByname($params['nickname'], 3) != false) $params['error'] .= $jieqiLang['system']['user_name_exists'].'<br />';
			}
			$params['changenick']=true;
		}

		//修改密码
		if(!empty($params['newpass'])){
			$params['oldpass'] = trim($params['oldpass']);
			$params['newpass'] = trim($params['newpass']);
			$params['repass'] = trim($params['repass']);
			if ($params['newpass'] != $params['repass']) $params['error'] .= $jieqiLang['system']['password_not_equal'].'<br />';
			elseif(strlen($params['newpass']) == 0) $params['error'] .= $jieqiLang['system']['need_pass_repass'].'<br />';
			elseif($user->getVar('pass', 'n') != $params['oldpass'] && $user->getVar('pass', 'n') != $users_handler->encryptPass($params['oldpass'])) $params['error'] .= $jieqiLang['system']['error_old_pass'].'<br />';
		}
	}
	if(!empty($params['error'])){
		if($params['return']) return false;
		else jieqi_printfail($params['error']);
	}else{
		return true;
	}
}

/**
 * 本地用户编辑，正式处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_uedit_lprocess(&$params){
	global $users_handler;
	global $jieqiLang;
	global $jieqiConfigs;
	global $jieqiHonors;
	global $jieqiUsersStatus;
	global $jieqiUsersGroup;
	//载入参数设置
	if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs');
	//载入语言包
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	if(!is_a($users_handler, 'JieqiUsersHandler')){
		include_once(JIEQI_ROOT_PATH.'/class/users.php');
		$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
	}
	$user=$users_handler->get($params['uid']);
	if(!is_object($user)){
		$params['error'] = LANG_NO_USER;
		if($params['return']) return false;
		else jieqi_printfail($params['error']);
	}

	$chglog=array();
	$chginfo='';

	$user->unsetNew();
	if($params['adminlevel'] > 0){
		if(strlen($params['newpass'])>0){
			$user->setVar('pass',$users_handler->encryptPass($params['newpass']));
		}
	}

	if($params['adminlevel'] == 1){
		//本人修改
		$user->setVar('name', $params['nickname']);
		if(strlen($params['newpass'])>0){
			$user->setVar('pass',$users_handler->encryptPass($params['newpass']));
		}
		$user->setVar('sex', $params['sex']);
		$user->setVar('email', $params['email']);
		$user->setVar('url', $params['url']);
		$user->setVar('qq', $params['qq']);
		$user->setVar('msn', $params['msn']);
		if($params['viewemail'] != 1) $params['viewemail']=0;
		$user->setVar('viewemail', $params['viewemail']);
		$user->setVar('adminemail', $params['adminemail']);
		if(isset($params['workid']) && intval($user->getVar('workid', 'n')) != intval($params['workid'])){
			$user->setVar('workid', $params['workid']);
			$params['changework']=true;
		}else{
			$params['changework']=false;
		}
		$user->setVar('sign', $params['sign']);
		$user->setVar('intro', $params['intro']);
		if (!$users_handler->insert($user)){
			$params['error'] = empty($params['lang_failure']) ? $jieqiLang['system']['user_edit_failure'] : $params['lang_failure'];
			if($params['return']) return false;
			else jieqi_printfail($params['error']);
		}else{
			if($params['changework'] && $_SESSION['jieqiUserId'] == $user->getVar('uid')){
				jieqi_getconfigs('system', 'honors');
				$honorid=jieqi_gethonorid($user->getVar('score'), $jieqiHonors);
				$_SESSION['jieqiUserHonor'] = $jieqiHonors[$honorid]['name'][intval($user->getVar('workid', 'n'))];
			}
			if($params['changenick'] && $_SESSION['jieqiUserId'] == $user->getVar('uid')){
				$_SESSION['jieqiUserName']=(strlen($user->getVar('name', 'n')) > 0) ? $user->getVar('name', 'n') : $user->getVar('uname', 'n');
			}
			$user->saveToSession();
			return true;
		}
	}else{
		//管理员修改

		if($params['adminlevel'] >= 2){
			//修改密码
			if(strlen($params['pass'])>0){
				$user->setVar('pass',$users_handler->encryptPass($params['pass']));
				$chginfo.=$jieqiLang['system']['userlog_change_password'];
			}
			//经验值
			if(is_numeric($params['experience']) && $params['experience'] != $user->getVar('experience')){
				$chglog['experience']['from']=$user->getVar('experience');
				$chglog['experience']['to']=$params['experience'];
				$user->setVar('experience', $params['experience']);
				if($chglog['experience']['from'] > $chglog['experience']['to']){
					$chginfo.=sprintf($jieqiLang['system']['userlog_less_experience'], $chglog['experience']['from'] - $chglog['experience']['to']);
				}else{
					$chginfo.=sprintf($jieqiLang['system']['userlog_add_experience'], $chglog['experience']['to'] - $chglog['experience']['from']);
				}
			}
			//积分
			if(is_numeric($params['score']) && $params['score'] != $user->getVar('score')){
				$chglog['score']['from']=$user->getVar('score');
				$chglog['score']['to']=$params['score'];
				$user->setVar('score', $params['score']);
				if($chglog['score']['from'] > $chglog['score']['to']){
					$chginfo.=sprintf($jieqiLang['system']['userlog_less_score'], $chglog['score']['from'] - $chglog['score']['to']);
				}else{
					$chginfo.=sprintf($jieqiLang['system']['userlog_add_score'], $chglog['score']['to'] - $chglog['score']['from']);
				}
			}
		}

		if($params['adminlevel'] >= 3){
			//会员等级
			if(is_numeric($params['groupid']) && $params['groupid'] != $user->getVar('groupid')){
				if($params['groupid'] == JIEQI_GROUP_ADMIN && $jieqiUsersGroup != JIEQI_GROUP_ADMIN){
					$params['error'] = $jieqiLang['system']['cant_set_admin'];
					if($params['return']) return false;
					else jieqi_printfail($params['error']);
				}
				$chglog['groupid']['from']=$user->getVar('groupid');
				$chglog['groupid']['to']=$params['groupid'];
				$user->setVar('groupid', $params['groupid']);
				$chginfo.=sprintf($jieqiLang['system']['userlog_change_group'], $jieqiGroups[$chglog['groupid']['from']], $jieqiGroups[$chglog['groupid']['to']]);
			}
		}

		if($params['adminlevel'] >= 4){
			//虚拟货币
			if(is_numeric($params['egold']) && $params['egold'] != $user->getVar('egold')){
				$chglog['egold']['from']=$user->getVar('egold');
				$chglog['egold']['to']=$params['egold'];
				$user->setVar('egold', $params['egold']);
				if($chglog['egold']['from'] > $chglog['egold']['to']){
					$chginfo.=sprintf($jieqiLang['system']['userlog_less_egold'], JIEQI_EGOLD_NAME, $chglog['egold']['from'] - $chglog['egold']['to']);
				}else{
					$chginfo.=sprintf($jieqiLang['system']['userlog_add_egold'], JIEQI_EGOLD_NAME, $chglog['egold']['to'] - $chglog['egold']['from']);
				}
			}
			//银币
			if(is_numeric($params['esilver']) && $params['esilver'] != $user->getVar('esilver')){
				$chglog['esilver']['from']=$user->getVar('esilver');
				$chglog['esilver']['to']=$peyment;
				$user->setVar('esilver', $params['esilver']);
				if($chglog['esilver']['from'] > $chglog['esilver']['to']){
					$chginfo.=sprintf($jieqiLang['system']['userlog_less_esilver'], $chglog['esilver']['from'] - $chglog['esilver']['to']);
				}else{
					$chginfo.=sprintf($jieqiLang['system']['userlog_add_esilver'], $chglog['esilver']['to'] - $chglog['esilver']['from']);
				}
			}

			//VIP状态
			if(is_numeric($params['isvip']) && $params['isvip'] != $user->getVar('isvip')){
				$tmpstr=$user->getViptype();
				$chglog['isvip']['from']=$user->getVar('isvip');
				$chglog['isvip']['to']=$params['groupid'];
				$user->setVar('isvip', $params['isvip']);
				$chginfo.=sprintf($jieqiLang['system']['userlog_change_vip'], $tmpstr, $user->getViptype());
			}

		}

		if (!$users_handler->insert($user)){
			$params['error'] = $jieqiLang['system']['change_user_failure'];
			if($params['return']) return false;
			else jieqi_printfail($params['error']);
		}else{
			//记录登录日志
			/*
			include_once(JIEQI_ROOT_PATH.'/class/logs.php');
			$logs_handler = JieqiLogsHandler::getInstance('JieqiLogsHandler');
			$logdata = array('logtype'=>2, 'targetid'=>$user->getVar('uid', 'n'), 'targettitle'=>$user->getVar('uname', 'n'), 'lognote'=>$params['reason'], 'logdata'=>$chginfo, 'fromdata'=>$log_fromdata, 'todata'=>serialize($user));
			$logs_handler->addlog($logdata);
			*/

			//记录日志
			include_once(JIEQI_ROOT_PATH.'/class/userlog.php');
			$userlog_handler = JieqiUserlogHandler::getInstance('JieqiUserlogHandler');
			$newlog=$userlog_handler->create();
			$newlog->setVar('siteid', JIEQI_SITE_ID);
			$newlog->setVar('logtime', JIEQI_NOW_TIME);
			$newlog->setVar('fromid', $_SESSION['jieqiUserId']);
			$newlog->setVar('fromname', $_SESSION['jieqiUserName']);
			$newlog->setVar('toid', $user->getVar('uid', 'n'));
			$newlog->setVar('toname', $user->getVar('uname', 'n'));
			$newlog->setVar('reason', $params['reason']);
			$newlog->setVar('chginfo', $chginfo);
			$newlog->setVar('chglog', serialize($chglog));
			$newlog->setVar('isdel', '0');
			$newlog->setVar('userlog', '');
			$userlog_handler->insert($newlog);
			return true;
		}
	}
	return true;
}



?>