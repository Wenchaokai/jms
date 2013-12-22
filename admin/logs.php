<?php
/**
 * ������־
 *
 * ��¼��̨��������־
 * 
 * ����ģ�壺/templates/admin/logs.html
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: logs.php 344 2009-06-23 03:06:07Z juny $
 */

//�û���־
define('JIEQI_MODULE_NAME', 'system');
require_once('../global.php');
//���Ȩ��
include_once(JIEQI_ROOT_PATH.'/class/power.php');
$power_handler =& JieqiPowerHandler::getInstance('JieqiPowerHandler');
$power_handler->getSavedVars('system');
jieqi_checkpower($jieqiPower['system']['adminuserlog'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);//��ʱ�ù����û���־Ȩ������

include_once(JIEQI_ROOT_PATH.'/class/logs.php');
$logs_handler=JieqilogsHandler::getInstance('JieqilogsHandler');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
if(empty($_REQUEST['page']) || !is_numeric($_REQUEST['page'])) $_REQUEST['page']=1;
include_once(JIEQI_ROOT_PATH.'/admin/header.php');
$criteria=new CriteriaCompo();
if(!empty($_REQUEST['keyword'])){
	if($_REQUEST['keytype']==1) $criteria->add(new Criteria('toname', $_REQUEST['keyword'], '='));
	else $criteria->add(new Criteria('fromname', $_REQUEST['keyword'], '='));
}
if(!empty($_REQUEST['logtype'])){
	$jieqiTpl->assign('logtype', $_REQUEST['logtype']);
	$criteria->add(new Criteria('logtype', $_REQUEST['logtype'], '='));
}else{
	$jieqiTpl->assign('logtype', '');
}

if(!empty($_REQUEST['loglevel'])){
	$jieqiTpl->assign('loglevel', $_REQUEST['loglevel']);
	$criteria->add(new Criteria('loglevel', $_REQUEST['loglevel'], '='));
}else{
	$jieqiTpl->assign('loglevel', '');
}

$criteria->setSort('logid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiConfigs['system']['userlogpnum']);
$criteria->setStart(($_REQUEST['page']-1) * $jieqiConfigs['system']['userlogpnum']);
$logs_handler->queryObjects($criteria);
$logrows=array();
$k=0;
while($v = $logs_handler->getObject()){
	$logrows[$k]['logtime']=$v->getVar('logtime');
	$logrows[$k]['logid']=$v->getVar('logid');
	$logrows[$k]['siteid']=$v->getVar('siteid');
	$logrows[$k]['logtype']=$v->getVar('logtype');
	$logrows[$k]['loglevel']=$v->getVar('loglevel');
	$logrows[$k]['logtime']=$v->getVar('logtime');
	$logrows[$k]['userid']=$v->getVar('userid');
	$logrows[$k]['username']=$v->getVar('username');
	$logrows[$k]['userip']=$v->getVar('userip');
	$logrows[$k]['targetname']=$v->getVar('targetname');
	$logrows[$k]['targetid']=$v->getVar('targetid');
	$logrows[$k]['targettitle']=$v->getVar('targettitle');
	$logrows[$k]['logurl']=$v->getVar('logurl');
	$logrows[$k]['logcode']=$v->getVar('logcode');
	$logrows[$k]['logtitle']=$v->getVar('logtitle');
	$logrows[$k]['logdata']=$v->getVar('logdata');
	$logrows[$k]['lognote']=$v->getVar('lognote');
	//$logrows[$k]['fromdata']=$v->getVar('fromdata');
	//$logrows[$k]['todata']=$v->getVar('todata');
	$k++;
}
$jieqiTpl->assign_by_ref('logrows', $logrows);

/*������־����*/
jieqi_getconfigs(JIEQI_MODULE_NAME, 'lsort', 'jieqiLsort');
if(!isset($jieqiLsort)) $jieqiLsort=array();
$jieqiTpl->assign_by_ref('logsort',$jieqiLsort);


//����ҳ����ת
include_once(JIEQI_ROOT_PATH.'/lib/html/page.php');
$jumppage = new JieqiPage($logs_handler->getCount($criteria),$jieqiConfigs['system']['userlogpnum'],$_REQUEST['page']);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage',$jumppage->whole_bar());

$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = JIEQI_ROOT_PATH.'/templates/admin/logs.html';
include_once(JIEQI_ROOT_PATH.'/admin/footer.php');
?>