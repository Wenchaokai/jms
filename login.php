<?php 
/**
 * 用户登录
 *
 * 用户登录，提交后检查密码和校验码等
 * 
 * 调用模板：/templates/login.html
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: login.php 344 2009-06-23 03:06:07Z juny $
 */

define('JIEQI_MODULE_NAME', 'system');
if($_REQUEST['action']=='login') define('JIEQI_NEED_SESSION', 1);
require_once('global.php');
//<!--jieqi insert check code-->
if(isset($_REQUEST['action']) && $_REQUEST['action']=='login') @session_regenerate_id();
//载入语言
jieqi_loadlang('users', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if(isset($_REQUEST['action']) && $_REQUEST['action']=='login' && !empty($_REQUEST['username']) && !empty($_REQUEST['password']))
{
	jieqi_useraction('login', $_REQUEST);
}else {
	include_once(JIEQI_ROOT_PATH.'/header.php');
	if (!empty($_REQUEST['jumpurl'])) {
		$jieqiTpl->assign('url_login', JIEQI_USER_URL.'/login.php?do=submit&jumpurl='.urlencode($_REQUEST['jumpurl']));
	}elseif (!empty($_REQUEST['forward'])) {
		$jieqiTpl->assign('url_login', JIEQI_USER_URL.'/login.php?do=submit&jumpurl='.urlencode($_REQUEST['forward']));
	}else{
		$jieqiTpl->assign('url_login', JIEQI_USER_URL.'/login.php?do=submit');
	}
	$jieqiTpl->assign('url_register', JIEQI_USER_URL.'/register.php');
	$jieqiTpl->assign('url_getpass', JIEQI_USER_URL.'/getpass.php');
	if(!empty($jieqiConfigs['system']['checkcodelogin'])) $jieqiTpl->assign('show_checkcode', 1);
	else $jieqiTpl->assign('show_checkcode', 0);
	$jieqiTpl->assign('url_checkcode', JIEQI_USER_URL.'/checkcode.php');
	$jieqiTpl->setCaching(0);
	$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH.'/templates/login.html';
	include_once(JIEQI_ROOT_PATH.'/footer.php');
}
?>