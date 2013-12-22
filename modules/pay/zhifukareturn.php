<?php 
/**
 * zhifuka֧��-���ش���
 *
 * zhifuka֧��-���ش��� (http://www.zhifuka.com)
 * 
 * ����ģ�壺��
 * 
 * @category   jieqicms
 * @package    pay
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: zhifukareturn.php 234 2008-11-28 01:53:06Z juny $
 */

define('JIEQI_MODULE_NAME', 'pay');
define('JIEQI_PAY_TYPE', 'zhifuka');
require_once('../../global.php');
jieqi_loadlang('pay', JIEQI_MODULE_NAME);
jieqi_loadlang('zhifuka', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, JIEQI_PAY_TYPE, 'jieqiPayset');

$payid=$jieqiPayset[JIEQI_PAY_TYPE]['payid']; //�̻����
$paykey=$jieqiPayset[JIEQI_PAY_TYPE]['paykey']; //��Կ

//1-----------���ջص���Ϣ--------------------------------------------------------------------
$state = trim($_REQUEST['state']);	//����״̬ 1�ɹ�,2ʧ��
$customerid = trim($_REQUEST['customerid']);	//�̻����
$sd51no = trim($_REQUEST['sd51no']);			//51֧�����صĶ�����
$sdcustomno = trim($_REQUEST['sdcustomno']);	//�̻�ϵͳ������
$orderid = $sdcustomno;
$ordermoney = trim($_REQUEST['ordermoney']);	//�̻��������(ע�⣺���ܺ��̻�����֧���ύ������ordermoney��һ��)
$cardno = trim($_REQUEST['cardno']);			//�û�ʵ��֧����ʽ
$mark = trim($_REQUEST['mark']);		//�̻��Զ���
$sign = trim($_REQUEST['sign']);               //MD5ǩ��

//2-----------���¼���md5��ֵ---------------------------------------------------------------------------
//ע����ȷ�Ĳ�����ƴ��˳��
$text="pay_result=".$succeed."&bargainor_id=".$merchant_id."&sp_billno=".$orderid."&total_fee=" . $amount ."&attach=" . $attach ."&key=".$key;
$text='customerid='.$customerid.'&sd51no='.$sd51no.'&sdcustomno='.$sdcustomno.'&mark='.$mark.'&key='.$paykey;
$mac = md5($text);

//3-----------�жϷ�����Ϣ�����֧���ɹ�������֧��������ţ�������һ���Ĵ���----------------------------

if($payid != $customerid){
	echo '<result>1</result>';
	//jieqi_printfail($jieqiLang['pay']['customer_id_error']);
}elseif($state != 1){
	echo '<result>1</result>';
	//jieqi_printfail($jieqiLang['pay']['pay_return_error']);
}elseif (strtoupper($mac)==strtoupper($sign)){     	//---------���ǩ����֤�ɹ���
	include_once(JIEQI_ROOT_PATH.'/modules/pay/class/paylog.php');
	$paylog_handler=JieqiPaylogHandler::getInstance('JieqiPaylogHandler');
	$orderid=intval($orderid);
	$paylog=$paylog_handler->get($orderid);
	if(is_object($paylog)){
		$buyname=$paylog->getVar('buyname');
		$buyid=$paylog->getVar('buyid');
		$payflag=$paylog->getVar('payflag');
		//��������Ҷ�Ӧ��ϵ(���¼���)
		if(!isset($jieqiPayset[JIEQI_PAY_TYPE]['cardegold'][$_REQUEST['cardno']])){
			echo '<result>1</result>';
			//jieqi_printfail($jieqiLang['pay']['need_card_nopass']);
		}
		if(isset($jieqiPayset[JIEQI_PAY_TYPE]['cardegold'][$_REQUEST['cardno']][$_REQUEST['ordermoney']])) $egold = $jieqiPayset[JIEQI_PAY_TYPE]['cardegold'][$_REQUEST['cardno']][$_REQUEST['ordermoney']];
		elseif (isset($jieqiPayset[JIEQI_PAY_TYPE]['cardegold'][$_REQUEST['cardno']][1])) $egold = floor($_REQUEST['ordermoney'] * $jieqiPayset[JIEQI_PAY_TYPE]['cardegold'][$_REQUEST['cardno']][1]);
		$money = intval($_REQUEST['ordermoney'] * 100);
		if($payflag == 0){
			include_once(JIEQI_ROOT_PATH.'/class/users.php');
			$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
			$uservip=1; //Ĭ�ϵ�vip�ȼ�

			//ͳ���û��ܵĹ�������ң�ȷ��vip�ȼ�
			/*
			jieqi_getconfigs('system', 'vips', 'jieqiVips');
			if(!empty($jieqiVips)){
			$sql="SELECT SUM(saleprice) as sumegold FROM ".jieqi_dbprefix('obook_osale')." WHERE accountid=".$buyid;
			$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
			$query->execute($sql);
			$res=$query->getObject();
			if(is_object($res)) $sumegold=intval($res->getVar('sumegold', 'n'));
			else $sumegold=0;
			$sumegold+=$egold;
			foreach($jieqiVips as $k=>$v){
			$k=intval($k);
			if($sumegold >= $v['minegold'] && $k > $uservip) $uservip = $k;
			}
			}
			*/

			$ret=$users_handler->income($buyid, $egold, $jieqiPayset[JIEQI_PAY_TYPE]['paysilver'], $jieqiPayset[JIEQI_PAY_TYPE]['payscore'][$egold], $uservip);
			if($ret) $note=sprintf($jieqiLang['pay']['add_egold_success'], $buyname, JIEQI_EGOLD_NAME, $egold);
			else $note=sprintf($jieqiLang['pay']['add_egold_failure'], $buyid, $buyname, JIEQI_EGOLD_NAME, $egold);
			$paylog->setVar('rettime', JIEQI_NOW_TIME);
			$paylog->setVar('money', $money);
			$paylog->setVar('egold', $egold);
			$paylog->setVar('note', $note);
			$paylog->setVar('retserialno', $sd51no);
			$paylog->setVar('payflag', 1);
			if(!$paylog_handler->insert($paylog)){
				echo '<result>1</result>';
				//jieqi_printfail($jieqiLang['pay']['save_paylog_failure']);
			}else{
				echo '<result>1</result>';
				//jieqi_msgwin(LANG_DO_SUCCESS,sprintf($jieqiLang['pay']['buy_egold_success'], $buyname, JIEQI_EGOLD_NAME, $egold));
			}
		}else{
			echo '<result>1</result>';
			//jieqi_msgwin(LANG_DO_SUCCESS,sprintf($jieqiLang['pay']['buy_egold_success'], $buyname, JIEQI_EGOLD_NAME, $egold));
		}
	}else{
		echo '<result>1</result>';
		//jieqi_printfail($jieqiLang['pay']['no_buy_record']);
	}
}
?>