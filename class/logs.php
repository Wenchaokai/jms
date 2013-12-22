<?php
/**
 * ���ݱ���(jieqi_system_logs - ϵͳ��־)
 *
 * ���ݱ���(jieqi_system_logs - ϵͳ��־)
 * 
 * ����ģ�壺��
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: logs.php 344 2009-06-23 03:06:07Z juny $
 */

jieqi_includedb();
//�����û�
class JieqiLogs extends JieqiObjectData
{
	function JieqiLogs()
	{
		$this->JieqiObject();
		$this->initVar('logid', JIEQI_TYPE_INT, 0, '��־���', false, 11);
		$this->initVar('siteid', JIEQI_TYPE_INT, 0, '��վ���', false, 6);
		$this->initVar('logtype', JIEQI_TYPE_INT, 0, '��־����',false, 3);
		$this->initVar('loglevel', JIEQI_TYPE_INT, 0, '��־�ȼ�',false, 3);
		$this->initVar('logtime', JIEQI_TYPE_INT, 0, '��¼ʱ��', false, 11);
		$this->initVar('userid', JIEQI_TYPE_INT, 0, '���������', false, 11);
		$this->initVar('username', JIEQI_TYPE_TXTBOX, '', '������',false, 30);
		$this->initVar('userip', JIEQI_TYPE_TXTBOX, '', '������IP',false, 25);
		$this->initVar('targetname', JIEQI_TYPE_TXTBOX, '', '����������',false, 30);
		$this->initVar('targetid', JIEQI_TYPE_INT, 0, '�����������', false, 11);
		$this->initVar('targettitle', JIEQI_TYPE_TXTBOX, '', '�����������',false, 30);
		$this->initVar('logurl', JIEQI_TYPE_TXTBOX, '', '������URL',false, 100);
		$this->initVar('logcode', JIEQI_TYPE_INT, 0, '��־���', false, 11);
		$this->initVar('logtitle', JIEQI_TYPE_TXTBOX, '', '��־����',false, 100);
		$this->initVar('logdata', JIEQI_TYPE_TXTAREA, '', '��־����', false, NULL);
		$this->initVar('lognote', JIEQI_TYPE_TXTAREA, '', '��־��ע', false, NULL);
		$this->initVar('fromdata', JIEQI_TYPE_TXTAREA, '', '����ǰ����', false, NULL);
		$this->initVar('todata', JIEQI_TYPE_TXTAREA, '', '����������', false, NULL);
	}
}


//�û�����
class JieqiLogsHandler extends JieqiObjectHandler
{
	function JieqiLogsHandler($db='')
	{
		$this->JieqiObjectHandler($db);
		$this->basename='logs';
		$this->autoid='logid';
		$this->dbname='system_logs';
	}

	function addlog($data){
		global $jieqiLsort;
		jieqi_getconfigs('system', 'lsort', 'jieqiLsort');
		if(empty($data) || !is_array($data)) return false;
		$newLogs=& $this->create();
		$logtype = isset($data['logtype']) ? intval($data['logtype']) : 0;
		$newLogs->setVar('logtype', $logtype);
		
		isset($data['siteid']) ? $newLogs->setVar('siteid', intval($data['siteid'])) : $newLogs->setVar('siteid', JIEQI_SITE_ID);	
		isset($data['loglevel']) ? $newLogs->setVar('loglevel', intval($data['loglevel'])) : $newLogs->setVar('loglevel', intval($jieqiLsort[$logtype]['loglevel']));
		isset($data['logtime']) ? $newLogs->setVar('logtime', intval($data['logtime'])) : $newLogs->setVar('logtime', JIEQI_NOW_TIME);
		isset($data['userid']) ? $newLogs->setVar('userid', intval($data['userid'])) : $newLogs->setVar('userid', intval($_SESSION['jieqiUserId']));
		isset($data['username']) ? $newLogs->setVar('username', $data['username']) : $newLogs->setVar('username', $_SESSION['jieqiUserName']);
		isset($data['userip']) ? $newLogs->setVar('userip', $data['userip']) : $newLogs->setVar('userip', jieqi_userip());
		isset($data['targetname']) ? $newLogs->setVar('targetname', $data['targetname']) : $newLogs->setVar('targetname', $jieqiLsort[$logtype]['targetname']);
		isset($data['targetid']) ? $newLogs->setVar('targetid', intval($data['targetid'])) : $newLogs->setVar('targetid', 0);
		isset($data['targettitle']) ? $newLogs->setVar('targettitle', $data['targettitle']) : $newLogs->setVar('targettitle', '');
		isset($data['logurl']) ? $newLogs->setVar('logurl', $data['logurl']) : $newLogs->setVar('logurl', jieqi_addurlvars(array(),false,false));
		isset($data['logcode']) ? $newLogs->setVar('logcode', intval($data['logcode'])) : $newLogs->setVar('logcode', 0);
		isset($data['logtitle']) ? $newLogs->setVar('logtitle', $data['logtitle']) : $newLogs->setVar('logtitle', $jieqiLsort[$logtype]['logtitle']);
		isset($data['logdata']) ? $newLogs->setVar('logdata', $data['logdata']) : $newLogs->setVar('logdata', '');
		isset($data['lognote']) ? $newLogs->setVar('lognote', $data['lognote']) : $newLogs->setVar('lognote', '');
		isset($data['fromdata']) ? $newLogs->setVar('fromdata', $data['fromdata']) : $newLogs->setVar('fromdata', '');
		isset($data['todata']) ? $newLogs->setVar('todata', $data['todata']) : $newLogs->setVar('todata', '');
		return $this->insert($newLogs);
	}
}


?>