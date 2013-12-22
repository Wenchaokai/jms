<?php 
/**
 * 用户密码修改
 *
 * 用户密码修改，需要输入原密码和两遍新密码
 * 
 * 调用模板：/templates/passedit.html
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: passedit.php 344 2009-06-23 03:06:07Z juny $
 */

define('JIEQI_MODULE_NAME', 'system');
require_once('global.php');
jieqi_checklogin();
jieqi_loadlang('users', JIEQI_MODULE_NAME);
include_once(JIEQI_ROOT_PATH.'/class/users.php');
$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
$jieqiUsers = $users_handler->get($_SESSION['jieqiUserId']);
if(!$jieqiUsers) jieqi_printfail(LANG_NO_USER);

if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'edit';
switch ( $_REQUEST['action'] ) {
	case 'update':
		$_REQUEST['uid'] = $_SESSION['jieqiUserId'];
		$_REQUEST['jumpurl'] = JIEQI_URL.'/userdetail.php';
		$_REQUEST['lang_failure'] = $jieqiLang['system']['pass_edit_failure'];
		$_REQUEST['lang_success'] = $jieqiLang['system']['pass_edit_success'];
		jieqi_useraction('edit', $_REQUEST);
		break;
	case 'edit':
	default:
		include_once(JIEQI_ROOT_PATH.'/header.php');
		$jieqiTpl->assign('url_passedit', JIEQI_USER_URL.'/passedit.php?do=submit');
		$jieqiTpl->setCaching(0);
		$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH.'/templates/passedit.html';
		include_once(JIEQI_ROOT_PATH.'/footer.php');
		break;
}

?>