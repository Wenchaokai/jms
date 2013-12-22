<?php
/**
 * 用户资料管理
 *
 * 修改用户资料
 * 
 * 调用模板：/templates/admin/topuser.html
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: usermanage.php 344 2009-06-23 03:06:07Z juny $
 */

define('JIEQI_MODULE_NAME', 'system');
require_once('../global.php');
//检查权限
include_once(JIEQI_ROOT_PATH.'/class/power.php');
$power_handler =& JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminuser'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
if(empty($_REQUEST['id'])) jieqi_printfail(LANG_NO_USER);
jieqi_loadlang('users', JIEQI_MODULE_NAME);
$_REQUEST['id'] = intval($_REQUEST['id']);
include_once(JIEQI_ROOT_PATH.'/class/users.php');
$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
$user=$users_handler->get($_REQUEST['id']);
if(!is_object($user)) jieqi_printfail(LANG_NO_USER);

if($user->getVar('groupid') == JIEQI_GROUP_ADMIN && $jieqiUsersGroup != JIEQI_GROUP_ADMIN) jieqi_printfail($jieqiLang['system']['cant_manage_admin']);

if(jieqi_checkpower($jieqiPower['system']['deluser'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) $adminlevel=4;
elseif(jieqi_checkpower($jieqiPower['system']['adminvip'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) $adminlevel=3;
elseif(jieqi_checkpower($jieqiPower['system']['changegroup'], $jieqiUsersStatus, $jieqiUsersGroup, true, true)) $adminlevel=2;
else $adminlevel=1;


if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'edit';
switch ( $_REQUEST['action'] ) {
	case 'update':
		$_POST['reason'] = trim($_POST['reason']);
		$_POST['pass'] = trim($_POST['pass']);
		$_POST['repass'] = trim($_POST['repass']);

		if (strlen($_POST['reason'])==0) $errtext .= $jieqiLang['system']['change_user_reason'].'<br />';
		//检查密码
		if ($_POST['pass'] != $_POST['repass']) $errtext .= $jieqiLang['system']['password_not_equal'].'<br />';
		//记录注册信息
		if(empty($errtext)) {
			$log_fromdata = serialize($user);
			//处理删除
			if($adminlevel>=4 && isset($_POST['deluser']) && $_POST['deluser']==1){
				$_REQUEST['uid'] = $user->getVar('uid');
				$_REQUEST['jumpurl'] = JIEQI_URL.'/admin/users.php';
				jieqi_useraction('delete', $_REQUEST);
			}else{
				$_REQUEST['uid'] = $user->getVar('uid');
				$_REQUEST['jumpurl'] = JIEQI_URL.'/admin/users.php';
				jieqi_useraction('edit', $_REQUEST);
			}
			exit;
		} else {
			jieqi_printfail($errtext);
		}
		break;
	case 'edit':
	default:
		include_once(JIEQI_ROOT_PATH.'/admin/header.php');
		include_once(JIEQI_ROOT_PATH.'/lib/html/formloader.php');
		$edit_form = new JieqiThemeForm($jieqiLang['system']['user_manage'], 'usermanage', JIEQI_URL.'/admin/usermanage.php');
		$edit_form->addElement(new JieqiFormLabel($jieqiLang['system']['table_users_uname'], $user->getVar('uname')));
		$pass=new JieqiFormPassword($jieqiLang['system']['table_users_pass'], 'pass', 25, 20);
		$pass->setDescription($jieqiLang['system']['not_change_password']);
		$edit_form->addElement($pass);
		$edit_form->addElement(new JieqiFormPassword($jieqiLang['system']['confirm_password'], 'repass', 25, 20));
		if($adminlevel >= 2){
			$group_select = new JieqiFormSelect($jieqiLang['system']['table_users_groupid'],'groupid', $user->getVar('groupid', 'e'));
			foreach($jieqiGroups as $key => $val){
				$group_select->addOption($key, $val);
			}
			$edit_form->addElement($group_select, true);
		}
		$edit_form->addElement(new JieqiFormText($jieqiLang['system']['table_users_experience'], 'experience', 25, 11, $user->getVar('experience','e')));
		$edit_form->addElement(new JieqiFormText($jieqiLang['system']['table_users_score'], 'score', 25, 11, $user->getVar('score','e')));

		if($adminlevel>=3){
			$edit_form->addElement(new JieqiFormText(JIEQI_EGOLD_NAME, 'egold', 25, 11, $user->getVar('egold','e')));
			$edit_form->addElement(new JieqiFormText($jieqiLang['system']['table_users_esilver'], 'esilver', 25, 11, $user->getVar('esilver','e')));
			$isvip=new JieqiFormRadio($jieqiLang['system']['table_users_isvip'], 'isvip', $user->getVar('isvip', 'e'));
			$isvip->addOption(0, $jieqiLang['system']['user_no_vip']);
			$isvip->addOption(1, $jieqiLang['system']['user_is_vip']);
			$isvip->addOption(2, $jieqiLang['system']['user_super_vip']);
			$edit_form->addElement($isvip);
		}
		if($adminlevel>=4){
			$yesno=new JieqiFormRadio($jieqiLang['system']['delete_user'], 'deluser', 0);
			$yesno->addOption(0, LANG_NO);
			$yesno->addOption(1, LANG_YES);
			$edit_form->addElement($yesno);
		}
		$edit_form->addElement(new JieqiFormTextArea($jieqiLang['system']['user_change_reason'], 'reason', '', 6, 60), true);
		$edit_form->addElement(new JieqiFormHidden('action', 'update'));
		$edit_form->addElement(new JieqiFormHidden('id',$_REQUEST['id']));
		$edit_form->addElement(new JieqiFormButton('&nbsp;', 'submit', $jieqiLang['system']['user_save_change'], 'submit'));
		$jieqiTpl->assign('jieqi_contents', '<br />'.$edit_form->render(JIEQI_FORM_MIDDLE).'<br />');
		include_once(JIEQI_ROOT_PATH.'/admin/footer.php');
		break;
}
?>