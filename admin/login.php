<?php
/**
 * 后台用户登录
 *
 * 后台用户登录
 * 
 * 调用模板：/templates/admin/login.html
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: login.php 344 2009-06-23 03:06:07Z juny $
 */

define('JIEQI_MODULE_NAME', 'system');
define('JIEQI_ADMIN_LOGIN', 1);
if($_REQUEST['action']=='login') define('JIEQI_NEED_SESSION', 1);
require_once('../global.php');

if(!empty($_SESSION['jieqiUserId']) && !empty($_SESSION['jieqiAdminLogin'])){
	if (empty($_REQUEST['jumpurl'])) {
		$_REQUEST['jumpurl']=JIEQI_URL.'/admin/index.php';
	}
	header('Location: '.$_REQUEST['jumpurl']);
	exit;
}

//if(JIEQI_LOCAL_URL != JIEQI_USER_URL) header('Location: '.JIEQI_USER_URL.jieqi_addurlvars(array()));
if(isset($_REQUEST['action']) && $_REQUEST['action']=='login' && empty($_SESSION['jieqiUserId'])) @session_regenerate_id();
//载入语言
jieqi_loadlang('users', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if(!isset($_REQUEST['action'])) $_REQUEST['action']='';
if($_REQUEST['action']=='login' && !empty($_REQUEST['password']) && (!empty($_REQUEST['username']) || !empty($_SESSION['jieqiUserUname']))){
	if(empty($_REQUEST['username']) && !empty($_SESSION['jieqiUserUname'])) $_REQUEST['username'] = $_SESSION['jieqiUserUname'];
	if(empty($_REQUEST['jumpurl'])) $_REQUEST['jumpurl']=JIEQI_URL.'/admin/index.php';
	jieqi_useraction('login', $_REQUEST);
	exit;
}

include_once(JIEQI_ROOT_PATH.'/admin/header.php');

$self_fname = $_SERVER['PHP_SELF'] ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);

if (!empty($_REQUEST['jumpurl'])) {
	$jieqiTpl->assign('url_login', JIEQI_USER_URL.'/admin/'.$self_fname.'?do=submit&jumpurl='.urlencode($_REQUEST['jumpurl']));
}else{
	$jieqiTpl->assign('url_login', JIEQI_USER_URL.'/admin/'.$self_fname.'?do=submit');
}
if(empty($_SESSION['jieqiUserId'])){
	$jieqiTpl->assign('jieqi_userid', 0);
	$jieqiTpl->assign('jieqi_username', '');
}else{
	$jieqiTpl->assign('jieqi_userid', $_SESSION['jieqiUserId']);
	$jieqiTpl->assign('jieqi_username', jieqi_htmlstr($_SESSION['jieqiUserUname']));
}
if(!empty($jieqiConfigs['system']['checkcodelogin'])) $jieqiTpl->assign('show_checkcode', 1);
else $jieqiTpl->assign('show_checkcode', 0);

if(empty($jieqiConfigs['system']['usegd'])){
	$jieqiTpl->assign('usegd', 0);
}else{
	$jieqiTpl->assign('usegd', 1);
}
$jieqiTpl->assign('url_checkcode', JIEQI_USER_URL.'/checkcode.php');
$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH.'/templates/admin/login.html';
include_once(JIEQI_ROOT_PATH.'/admin/footer.php');

//包含页头页尾


?>