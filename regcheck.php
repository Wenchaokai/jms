<?php 
/**
 * ajax��ע����ϢУ��
 *
 * ע��ҳ�棬�����û�������email����Ϣ��ajax�ύ����ҳ�����Ƿ�����
 * 
 * ����ģ�壺��
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: regcheck.php 344 2009-06-23 03:06:07Z juny $
 */
define('JIEQI_MODULE_NAME', 'system');
require_once('global.php');
header('Content-Type:text/html;charset='.JIEQI_CHAR_SET);
include_once(JIEQI_ROOT_PATH.'/lib/text/textfunction.php');
include_once(JIEQI_ROOT_PATH.'/class/users.php');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
jieqi_loadlang('users', JIEQI_MODULE_NAME);

$users_handler=&JieqiUsersHandler::getInstance('JieqiUsersHandler');
$imageright=sprintf($jieqiLang['system']['register_check_right'], JIEQI_URL);
$imageerror=sprintf($jieqiLang['system']['register_check_error'], JIEQI_URL);
switch($_GET['item']){
	case 'u': 
		//����û���
		if(strlen($_GET['username'])==0){//�Ƿ�Ϊ��
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['need_username'];
		}elseif(preg_match('/^\s*$|^c:\\con\\con$|[@%,;:\.\|\*\"\'\\\\\/\s\t\<\>\&]|��/is', $_GET['username'])){//�Ƿ�ȫ�ַ�
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['error_user_format'];
		}elseif($jieqiConfigs[JIEQI_MODULE_NAME]['usernamelimit']==1 && !preg_match('/^[A-Za-z0-9]+$/',$_GET['username'])){//�Ƿ���������
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['username_need_engnum'];
		}elseif($users_handler->getByname($_GET['username'], 3) != false){//�Ƿ���ע��
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['user_has_registered'];
		}else{
			$htmlstring=$imageright;//�Ϸ����û���
		}
		echo $htmlstring;
		break;
	case 'n': 
		//����ǳ�
		if(strlen($_GET['nickname'])==0){//�Ƿ�Ϊ��
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['need_nickname'];
		}elseif(preg_match('/^\s*$|^c:\\con\\con$|[@%,;:\.\|\*\"\'\\\\\/\s\t\<\>\&]|��/is', $_GET['nickname'])){//�Ƿ�ȫ�ַ�
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['error_nick_format'];
		}elseif($users_handler->getByname($_GET['nickname'], 3) != false){//�Ƿ���ע��
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['nick_has_used'];
		}else{
			$htmlstring=$imageright;//�Ϸ����ǳ�
		}
		echo $htmlstring;
		break;
	case 'p': 
		//�������
		if(strlen($_GET['password'])==0){//�Ƿ�Ϊ��
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['need_pass_repass'];
		}else{
			$htmlstring=$imageright;
		}
		echo $htmlstring;
		break;
	case 'r': 
		//����ظ�����
		if(strlen($_GET['password'])==0 || strlen($_GET['repassword'])==0){//�Ƿ�Ϊ��
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['need_pass_repass'];
		}elseif($_GET['password']!=$_GET['repassword']){//�Ƿ����
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['password_not_equal'];
		}else{
			$htmlstring=$imageright;
		}
		echo $htmlstring;
		break;
	case 'm': 
		//���Email��ʽ
		if(strlen($_GET['email'])==0){//�Ƿ�Ϊ��
			$htmlstring=$imageerror.$jieqiLang['system']['need_email'];
		}elseif(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i",$_GET['email'])){
			$htmlstring=$imageerror.$jieqiLang['system']['error_email_format'];//�Ƿ�Ϸ���ʽ
		}elseif($users_handler->getCount(new Criteria('email', $_GET['email'], '='))>0){//�Ƿ���ע��
			$htmlstring=$imageerror.$jieqiLang[JIEQI_MODULE_NAME]['email_has_registered'];
		}else{
			$htmlstring=$imageright;//�Ϸ�Email��ַ
		}
		echo $htmlstring;
		break;
	default:
}
?>