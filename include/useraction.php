<?php
/**
 * 用户处理相关函数
 *
 * 用户处理相关函数
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: funuser.php 317 2009-01-06 09:03:33Z juny $
 */

if(defined('JIEQI_USER_INTERFACE') && preg_match('/^\w^$/is', JIEQI_USER_INTERFACE)) include_once(dirname(__FILE__).'/funuser_'.JIEQI_USER_INTERFACE.'.php');
else include_once(dirname(__FILE__).'/funuser.php');

include_once(dirname(__FILE__).'/userlocal.php');
/**
 * 用户注册
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码,$params['email'] - 邮箱
 * @access     public
 * @return     int    
 */
function jieqi_user_register(&$params){
	return jieqi_uregister_lprepare($params) && //本地预处理
	jieqi_uregister_iprepare($params) && //接口预处理
	jieqi_uregister_lprocess($params) && //本地处理
	jieqi_uregister_iprocess($params);  //接口处理
}

/**
 * 用户登录
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_user_login(&$params){
	return jieqi_ulogin_lprepare($params) && //本地预处理
	jieqi_ulogin_iprepare($params) && //接口预处理
	jieqi_ulogin_lprocess($params) && //本地处理
	jieqi_ulogin_iprocess($params); //接口处理
}

/**
 * 用户退出
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_user_logout(&$params){
	return jieqi_ulogout_lprepare($params) && //本地预处理
	jieqi_ulogout_iprepare($params) && //接口预处理
	jieqi_ulogout_lprocess($params) && //本地处理
	jieqi_ulogout_iprocess($params);  //接口处理
}

/**
 * 用户删除
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_user_delete(&$params){
	return jieqi_udelete_lprepare($params) && //本地预处理
	jieqi_udelete_iprepare($params) && //接口预处理
	jieqi_udelete_lprocess($params) && //本地处理
	jieqi_udelete_iprocess($params);  //接口处理
}

/**
 * 用户更新
 * 
 * @param      array       $params 参数数组
 * 必须参数： $params['username'] - 用户名,$params['password'] - 密码
 * @access     public
 * @return     int    
 */
function jieqi_user_edit(&$params){
	return jieqi_uedit_lprepare($params) && //本地预处理
	jieqi_uedit_iprepare($params) && //接口预处理
	jieqi_uedit_lprocess($params) && //本地处理
	jieqi_uedit_iprocess($params);  //接口处理
}

?>