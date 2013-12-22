<?php 
/**
 * 编辑用户资料
 *
 * 编辑用户资料
 * 
 * 调用模板：/templates/useredit.html
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: useredit.php 274 2008-12-09 06:34:24Z juny $
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
		jieqi_useraction('edit', $_REQUEST);
		break;
	case 'edit':
	default:
		include_once(JIEQI_ROOT_PATH.'/header.php');
	    $jieqiTpl->assign('username', $jieqiUsers->getVar('uname', 's'));
		$jieqiTpl->assign('nickname', $jieqiUsers->getVar('name', 'e'));
		$jieqiTpl->assign('email', $jieqiUsers->getVar('email', 'e'));
	    $jieqiTpl->assign('sex', $jieqiUsers->getVar('sex', 'e'));
		$jieqiTpl->assign('qq',$jieqiUsers->getVar('qq', 'e'));
		$jieqiTpl->assign('msn',$jieqiUsers->getVar('msn', 'e'));
		$jieqiTpl->assign('url',$jieqiUsers->getVar('url', 'e'));
		$jieqiTpl->assign('sign',$jieqiUsers->getVar('sign', 'e'));
		$jieqiTpl->assign('intro',$jieqiUsers->getVar('intro', 'e'));
		$jieqiTpl->assign('viewemail',$jieqiUsers->getVar('viewemail', 'e'));
		$jieqiTpl->assign('adminemail',$jieqiUsers->getVar('adminemail', 'e'));
		$jieqiTpl->setCaching(0);
		$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH.'/templates/useredit.html';
		include_once(JIEQI_ROOT_PATH.'/footer.php');
		break;
}
?>