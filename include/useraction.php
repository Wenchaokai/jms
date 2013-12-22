<?php
/**
 * �û�������غ���
 *
 * �û�������غ���
 * 
 * ����ģ�壺��
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
 * �û�ע��
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����,$params['email'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_user_register(&$params){
	return jieqi_uregister_lprepare($params) && //����Ԥ����
	jieqi_uregister_iprepare($params) && //�ӿ�Ԥ����
	jieqi_uregister_lprocess($params) && //���ش���
	jieqi_uregister_iprocess($params);  //�ӿڴ���
}

/**
 * �û���¼
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_user_login(&$params){
	return jieqi_ulogin_lprepare($params) && //����Ԥ����
	jieqi_ulogin_iprepare($params) && //�ӿ�Ԥ����
	jieqi_ulogin_lprocess($params) && //���ش���
	jieqi_ulogin_iprocess($params); //�ӿڴ���
}

/**
 * �û��˳�
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_user_logout(&$params){
	return jieqi_ulogout_lprepare($params) && //����Ԥ����
	jieqi_ulogout_iprepare($params) && //�ӿ�Ԥ����
	jieqi_ulogout_lprocess($params) && //���ش���
	jieqi_ulogout_iprocess($params);  //�ӿڴ���
}

/**
 * �û�ɾ��
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_user_delete(&$params){
	return jieqi_udelete_lprepare($params) && //����Ԥ����
	jieqi_udelete_iprepare($params) && //�ӿ�Ԥ����
	jieqi_udelete_lprocess($params) && //���ش���
	jieqi_udelete_iprocess($params);  //�ӿڴ���
}

/**
 * �û�����
 * 
 * @param      array       $params ��������
 * ��������� $params['username'] - �û���,$params['password'] - ����
 * @access     public
 * @return     int    
 */
function jieqi_user_edit(&$params){
	return jieqi_uedit_lprepare($params) && //����Ԥ����
	jieqi_uedit_iprepare($params) && //�ӿ�Ԥ����
	jieqi_uedit_lprocess($params) && //���ش���
	jieqi_uedit_iprocess($params);  //�ӿڴ���
}

?>