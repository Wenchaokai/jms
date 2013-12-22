<?php 
/**
 * 手工加虚拟币
 *
 * 手工加虚拟币
 * 
 * 调用模板：/modules/pay/templates/admin/changeegold.html
 * 
 * @category   jieqicms
 * @package    pay
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: 99billreturnv2.php 234 2008-11-28 01:53:06Z juny $
 */

define('JIEQI_MODULE_NAME', 'pay');
require_once('../../../global.php');
//检查权限
jieqi_getconfigs('system', 'power');
if(!jieqi_checkpower($jieqiPower['system']['deluser'], $jieqiUsersStatus, $jieqiUsersGroup, true, true) || !jieqi_checkpower($jieqiPower['system']['adminvip'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) jieqi_printfail(LANG_NO_PERMISSION);
if(empty($_REQUEST['uid']) || !is_numeric($_REQUEST['uid'])) jieqi_printfail(LANG_ERROR_PARAMETER);
$_REQUEST['uid'] = intval($_REQUEST['uid']);
include_once(JIEQI_ROOT_PATH.'/class/users.php');
$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
$user=$users_handler->get($_REQUEST['uid']);
if(!is_object($user)) jieqi_printfail(LANG_NO_USER);

jieqi_loadlang('egold', JIEQI_MODULE_NAME);


//LANG_ERROR_PARAMETER

if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'show';
$uid = $user->getVar('uid');
$uname = $user->getVar('name');
if(strlen($uname) == 0) $uname = $user->getVar('uname');
$uname_n = $user->getVar('name', 'n');
if(strlen($uname_n) == 0) $uname_n = $user->getVar('uname', 'n');
switch ($_REQUEST['action']) {
	case 'update':
		if(empty($_REQUEST['egold']) || !is_numeric($_REQUEST['egold'])) jieqi_printfail(LANG_ERROR_PARAMETER);

		$_REQUEST['egold'] = intval($_REQUEST['egold']);
		if(!empty($_REQUEST['money']) && is_numeric($_REQUEST['money'])) $money = round($_REQUEST['money'] * 100);
		else $money = 0;
		if(!empty($_REQUEST['usesliver'])) $usesliver = 1;
		else  $usesliver = 0;
		if(!empty($_REQUEST['score'])) $score = intval($_REQUEST['score']);
		else $score = 0;
		if($_REQUEST['egold'] > 0) $uservip = 1;
		else{
			$uservip = 0;
			$now_egold = intval($user->getVar('egold'));
			$now_esilver = intval($user->getVar('esilver'));
			if(($usesliver == 1 && ($now_esilver + $_REQUEST['egold']) < 0) || ($usesliver == 0 && ($now_egold + $_REQUEST['egold']) < 0)) jieqi_printfail($jieqiLang['pay']['change_egold_notenough']);
		}
		$ret=$users_handler->income($uid, $_REQUEST['egold'], $usesliver, $score, $uservip);
		if(!$ret) jieqi_printfail(sprintf($jieqiLang['pay']['change_egold_failure'], $uname));

		include_once($jieqiModules['pay']['path'].'/class/paylog.php');
		$paylog_handler=JieqiPaylogHandler::getInstance('JieqiPaylogHandler');
		$paylog= $paylog_handler->create();
		$paylog->setVar('siteid', JIEQI_SITE_ID);
		$paylog->setVar('buytime', JIEQI_NOW_TIME);
		$paylog->setVar('rettime', JIEQI_NOW_TIME);
		$paylog->setVar('buyid', $uid);
		$paylog->setVar('buyname', $uname_n);
		$paylog->setVar('buyinfo', '');
		$paylog->setVar('moneytype', 0);
		$paylog->setVar('money', $money);
		$paylog->setVar('egoldtype', $usesliver);
		$paylog->setVar('egold', $_REQUEST['egold']);
		$paylog->setVar('paytype', 'manual');
		$paylog->setVar('retserialno', '');
		$paylog->setVar('retaccount', '');
		$paylog->setVar('retinfo', '');
		$paylog->setVar('masterid', $_SESSION['jieqiUserId']);
		$paylog->setVar('mastername', $_SESSION['jieqiUserName']);
		$paylog->setVar('masterinfo', '');
		$paylog->setVar('note', '');
		$paylog->setVar('payflag', 1);

		$paylog_handler->insert($paylog);
		jieqi_jumppage(jieqi_geturl('system', 'user', $uid, 'info'), LANG_DO_SUCCESS, sprintf($jieqiLang['pay']['change_egold_success'], $uname));
		break;
	case 'show':
	default:
		include_once(JIEQI_ROOT_PATH.'/admin/header.php');
		$jieqiTpl->assign('url_changeegold', $jieqiModules['pay']['url'].'/admin/changeegold.php');
		$jieqiTpl->assign('uid', $uid);
		$jieqiTpl->assign('uname', $uname);
		$jieqiTpl->assign('egold', $user->getVar('egold'));
		$jieqiTpl->assign('esliver', $user->getVar('esliver'));
		$jieqiTpl->assign('useesliver', JIEQI_SILVER_USAGE);
		$jieqiTpl->setCaching(0);
		$jieqiTset['jieqi_contents_template'] = $jieqiModules['pay']['path'].'/templates/admin/changeegold.html';
		include_once(JIEQI_ROOT_PATH.'/admin/footer.php');
		break;
}

?>