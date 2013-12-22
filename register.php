<?php 
/**
 * 用户注册
 *
 * 用户注册页面
 * 
 * 调用模板：/templates/register.html
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: register.php 344 2009-06-23 03:06:07Z juny $
 */


define('JIEQI_MODULE_NAME', 'system');
if(isset($_REQUEST['action']) && $_REQUEST['action']=='newuser') define('JIEQI_NEED_SESSION', 1);
require_once('global.php');
//<!--jieqi insert check code-->
//if(JIEQI_LOCAL_URL != JIEQI_USER_URL) header('Location: '.JIEQI_USER_URL.jieqi_addurlvars(array()));
jieqi_loadlang('users', JIEQI_MODULE_NAME);
//是否允许注册
if (!defined("JIEQI_ALLOW_REGISTER") || JIEQI_ALLOW_REGISTER != 1) {
	jieqi_printfail($jieqiLang['system']['user_stop_register']);
}

if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'register';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
switch ($_REQUEST['action']) {
	case 'newuser':
		jieqi_useraction('register', $_REQUEST);
		break;
	case 'register':
	default:
		include_once(JIEQI_ROOT_PATH.'/header.php');
		$jieqiTpl->assign('form_action', JIEQI_USER_URL.'/register.php');
		$jieqiTpl->assign('check_url', JIEQI_USER_URL.'/regcheck.php');

		if(!empty($jieqiConfigs['system']['checkcodelogin'])) $jieqiTpl->assign('show_checkcode', 1);
		else $jieqiTpl->assign('show_checkcode', 0);
		
		$jieqiTpl->assign('url_checkcode', JIEQI_USER_URL.'/checkcode.php');
		$jieqiTpl->setCaching(0);
		$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH.'/templates/register.html';
		include_once(JIEQI_ROOT_PATH.'/footer.php');
		break;
}

?>