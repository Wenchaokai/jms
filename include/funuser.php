<?php 
/**
 * �û��ӿڣ�����ע�ᡢ��¼���˳���ش�����
 *
 * �û��ӿڣ�����ע�ᡢ��¼���˳���ش�����
 * 
 * ����ģ�壺��
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: funuser.php 344 2009-06-23 03:06:07Z juny $
 */


/**
 * �û��ӿڣ�ע��Ԥ����
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_uregister_iprepare(&$params){
	return true;
}

/**
 * �û��ӿڣ�ע�ᴦ��
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_uregister_iprocess(&$params){
	global $jieqiLang;
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	if(defined('JIEQI_WAP_PAGE')) jieqi_wapgourl($params['jumpurl']);
	elseif($_REQUEST['jumphide']) header('Location: '.$params['jumpurl']);
	else jieqi_jumppage($params['jumpurl'], $jieqiLang['system']['registered_title'], $jieqiLang['system']['register_success']);
	return true;
}
//*****************************************************************
//*****************************************************************

/**
 * �û��ӿڣ���¼Ԥ����
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_ulogin_iprepare(&$params){
	return true;
}

/**
 * �û��ӿڣ���¼����
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_ulogin_iprocess(&$params){
	global $jieqiLang;
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	if(defined('JIEQI_WAP_PAGE')) jieqi_wapgourl($params['jumpurl']);
	elseif($_REQUEST['jumphide']) header('Location: '.$params['jumpurl']);
	else jieqi_jumppage($params['jumpurl'], $jieqiLang['system']['logon_title'], sprintf($jieqiLang['system']['login_success'], jieqi_htmlstr($_REQUEST['username'])));
	return true;
}

//*****************************************************************
//*****************************************************************

/**
 * �û��ӿڣ��˳�Ԥ����
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_ulogout_iprepare(&$params){
	return true;
}

/**
 * �û��ӿڣ��˳�����
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_ulogout_iprocess(&$params){
	global $jieqiLang;
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	if(defined('JIEQI_WAP_PAGE')) jieqi_wapgourl($params['jumpurl']);
	elseif($_REQUEST['jumphide']) header('Location: '.$params['jumpurl']);
	else jieqi_jumppage($params['jumpurl'], $jieqiLang['system']['logout_title'], $jieqiLang['system']['logout_success']);
	return true;
}

//*****************************************************************
//*****************************************************************

/**
 * �û��ӿڣ�ɾ��Ԥ����
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_udelete_iprepare(&$params){
	return true;
}

/**
 * �û��ӿڣ�ɾ������
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_udelete_iprocess(&$params){
	global $jieqiLang;
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	if(defined('JIEQI_WAP_PAGE')) jieqi_wapgourl($params['jumpurl']);
	elseif($_REQUEST['jumphide']) header('Location: '.$params['jumpurl']);
	else jieqi_jumppage($params['jumpurl'], LANG_DO_SUCCESS, $jieqiLang['system']['delete_user_success']);
	return true;
}

//*****************************************************************
//*****************************************************************

/**
 * �û��ӿڣ��༭Ԥ����
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_uedit_iprepare(&$params){
	return true;
}

/**
 * �û��ӿڣ��༭����
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_uedit_iprocess(&$params){
	global $jieqiLang;
	if(!isset($jieqiLang['system'])) jieqi_loadlang('users', 'system');
	$lang_success = empty($_REQUEST['lang_success']) ? $jieqiLang['system']['change_user_success'] : $_REQUEST['lang_success'];
	if(defined('JIEQI_WAP_PAGE')) jieqi_wapgourl($params['jumpurl']);
	elseif($_REQUEST['jumphide']) header('Location: '.$params['jumpurl']);
	else jieqi_jumppage($params['jumpurl'], LANG_DO_SUCCESS, $lang_success);
	return true;
}

?>