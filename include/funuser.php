<?php 
/**
 * 用户接口，处理注册、登录、退出相关处理函数
 *
 * 用户接口，处理注册、登录、退出相关处理函数
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: funuser.php 344 2009-06-23 03:06:07Z juny $
 */


/**
 * 用户接口，注册预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
 * @access     public
 * @return     int    
 */
function jieqi_uregister_iprepare(&$params){
	return true;
}

/**
 * 用户接口，注册处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
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
 * 用户接口，登录预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
 * @access     public
 * @return     int    
 */
function jieqi_ulogin_iprepare(&$params){
	return true;
}

/**
 * 用户接口，登录处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
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
 * 用户接口，退出预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
 * @access     public
 * @return     int    
 */
function jieqi_ulogout_iprepare(&$params){
	return true;
}

/**
 * 用户接口，退出处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
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
 * 用户接口，删除预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
 * @access     public
 * @return     int    
 */
function jieqi_udelete_iprepare(&$params){
	return true;
}

/**
 * 用户接口，删除处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
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
 * 用户接口，编辑预处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
 * @access     public
 * @return     int    
 */
function jieqi_uedit_iprepare(&$params){
	return true;
}

/**
 * 用户接口，编辑处理
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
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