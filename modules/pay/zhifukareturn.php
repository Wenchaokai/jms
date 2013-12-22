<?php 
/**
 * zhifuka支付-返回处理
 *
 * zhifuka支付-返回处理 (http://www.zhifuka.com)
 * 
 * 调用模板：无
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

$payid=$jieqiPayset[JIEQI_PAY_TYPE]['payid']; //商户编号
$paykey=$jieqiPayset[JIEQI_PAY_TYPE]['paykey']; //密钥

//1-----------接收回的信息--------------------------------------------------------------------
$state = trim($_REQUEST['state']);	//订单状态 1成功,2失败
$customerid = trim($_REQUEST['customerid']);	//商户编号
$sd51no = trim($_REQUEST['sd51no']);			//51支付网关的订单号
$sdcustomno = trim($_REQUEST['sdcustomno']);	//商户系统订单号
$orderid = $sdcustomno;
$ordermoney = trim($_REQUEST['ordermoney']);	//商户订单金额(注意：可能和商户请求支付提交过来的ordermoney不一样)
$cardno = trim($_REQUEST['cardno']);			//用户实际支付方式
$mark = trim($_REQUEST['mark']);		//商户自定义
$sign = trim($_REQUEST['sign']);               //MD5签名

//2-----------重新计算md5的值---------------------------------------------------------------------------
//注意正确的参数串拼凑顺序
$text="pay_result=".$succeed."&bargainor_id=".$merchant_id."&sp_billno=".$orderid."&total_fee=" . $amount ."&attach=" . $attach ."&key=".$key;
$text='customerid='.$customerid.'&sd51no='.$sd51no.'&sdcustomno='.$sdcustomno.'&mark='.$mark.'&key='.$paykey;
$mac = md5($text);

//3-----------判断返回信息，如果支付成功，并且支付结果可信，则做进一步的处理----------------------------

if($payid != $customerid){
	echo '<result>1</result>';
	//jieqi_printfail($jieqiLang['pay']['customer_id_error']);
}elseif($state != 1){
	echo '<result>1</result>';
	//jieqi_printfail($jieqiLang['pay']['pay_return_error']);
}elseif (strtoupper($mac)==strtoupper($sign)){     	//---------如果签名验证成功！
	include_once(JIEQI_ROOT_PATH.'/modules/pay/class/paylog.php');
	$paylog_handler=JieqiPaylogHandler::getInstance('JieqiPaylogHandler');
	$orderid=intval($orderid);
	$paylog=$paylog_handler->get($orderid);
	if(is_object($paylog)){
		$buyname=$paylog->getVar('buyname');
		$buyid=$paylog->getVar('buyid');
		$payflag=$paylog->getVar('payflag');
		//金额和虚拟币对应关系(重新计算)
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
			$uservip=1; //默认的vip等级

			//统计用户总的购买虚拟币，确认vip等级
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