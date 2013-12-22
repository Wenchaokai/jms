<?php 
/**
 * 后台WAP书评管理
 *
 * 显示最近WAP书评列表
 * 
 * 调用模板：/modules/article/templates/admin/review.html
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: review.php 339 2009-06-23 03:03:24Z juny $
 */

define('JIEQI_MODULE_NAME', 'article');
require_once('../../../global.php');
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
jieqi_checkpower($jieqiPower['article']['manageallreview'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
$article_static_url = (empty($jieqiConfigs['article']['staticurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = (empty($jieqiConfigs['article']['dynamicurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
include_once($jieqiModules['article']['path'].'/class/review.php');
$review_handler =& JieqiReviewHandler::getInstance('JieqiReviewHandler');
//处理置顶、加精、删除
if(isset($_REQUEST['action']) && !empty($_REQUEST['rid'])){
	$actreview=$review_handler->get($_REQUEST['rid']);
	if(is_object($actreview)){
		$criteria=new CriteriaCompo(new Criteria('reviewid', $_REQUEST['rid']));
		switch($_REQUEST['action']){
			case 'top':
			$review_handler->updatefields(array('topflag'=>1), $criteria);
			break;
			case 'untop':
			$review_handler->updatefields(array('topflag'=>0), $criteria);
			break;
			case 'good':
			$review_handler->updatefields(array('goodflag'=>1), $criteria);
			//精华积分
			include_once(JIEQI_ROOT_PATH.'/class/users.php');
			$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
			if($actreview->getVar('userid') == $_SESSION['jieqiUserId']){
				$users_handler->changeScore($_SESSION['jieqiUserId'], $jieqiConfigs['article']['scoregoodreview'], true);
			}else{
				$users_handler->changeScore($actreview->getVar('userid'), $jieqiConfigs['article']['scoregoodreview'], true);
			}
			break;
			case 'normal':
			if($actreview->getVar('goodflag')==1){
				$review_handler->updatefields(array('goodflag'=>0), $criteria);
				//精华积分
				include_once(JIEQI_ROOT_PATH.'/class/users.php');
				$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
				if($actreview->getVar('userid') == $_SESSION['jieqiUserId']){
					$users_handler->changeScore($_SESSION['jieqiUserId'], $jieqiConfigs['article']['scoregoodreview'], false);
				}else{
					$users_handler->changeScore($actreview->getVar('userid'), $jieqiConfigs['article']['scoregoodreview'], false);
				}
			}

			break;
			case 'del':
			$review_handler->delete($criteria);
			//删除书评减少积分
			include_once(JIEQI_ROOT_PATH.'/class/users.php');
			$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
			if($actreview->getVar('userid') == $_SESSION['jieqiUserId']){
				$users_handler->changeScore($_SESSION['jieqiUserId'], $jieqiConfigs['article']['scorereview'], false);
			}else{
				$users_handler->changeScore($actreview->getVar('userid'), $jieqiConfigs['article']['scorereview'], false);
			}
			break;
		}
		unset($criteria);
	}
}elseif(isset($_REQUEST['batchdel']) && $_REQUEST['batchdel']==1 && is_array($_REQUEST['checkid']) && count($_REQUEST['checkid'])>0){
	//批量删除

	include_once(JIEQI_ROOT_PATH.'/class/users.php');
	$users_handler =& JieqiUsersHandler::getInstance('JieqiUsersHandler');
	$where='';
	foreach($_REQUEST['checkid'] as $v){
		if(is_numeric($v)){
			$v=intval($v);
			if(!empty($where)) $where.=' OR ';
			$where.=$review_handler->autoid.'='.$v;
		}
	}
	if(!empty($where)){
		$sql='SELECT reviewid, userid FROM '.jieqi_dbprefix('article_review').' WHERE '.$where;
		$res=$review_handler->db->query($sql);
		while($actreview = $review_handler->getObject($res)){
		//删除书评减少积分
			if($actreview->getVar('userid') == $_SESSION['jieqiUserId']){
				$users_handler->changeScore($_SESSION['jieqiUserId'], $jieqiConfigs['article']['scorereview'], false);
			}else{
				$users_handler->changeScore($actreview->getVar('userid'), $jieqiConfigs['article']['scorereview'], false);
			}
		}
		$sql='DELETE FROM '.jieqi_dbprefix('article_review').' WHERE '.$where;
		$review_handler->db->query($sql);
	}
}
include_once(JIEQI_ROOT_PATH.'/admin/header.php');
$jieqiTpl->assign('article_static_url',$article_static_url);
$jieqiTpl->assign('article_dynamic_url',$article_dynamic_url);
include_once(JIEQI_ROOT_PATH.'/lib/text/textfunction.php');
$jieqiTpl->assign('url_review', $article_dynamic_url.'/admin/review.php');
$jieqiTpl->assign('checkall', '<input type="checkbox" id="checkall" name="checkall" value="checkall" onclick="javascript: for (var i=0;i<this.form.elements.length;i++){ if (this.form.elements[i].name != \'checkkall\') this.form.elements[i].checked = form.checkall.checked; }">');

$criteria=new CriteriaCompo();
if(!empty($_REQUEST['keyword'])){
	$_REQUEST['keyword']=trim($_REQUEST['keyword']);
	if($_REQUEST['keytype']==1) $criteria->add(new Criteria('username', $_REQUEST['keyword'], '='));
	else $criteria->add(new Criteria('articlename', $_REQUEST['keyword'], '='));
}

if(isset($_REQUEST['type']) && $_REQUEST['type']=='good'){
	//精华书评
	$criteria->add(new Criteria('goodflag', 1));
}else{
	$_REQUEST['type']='all';
}

//页码
if (empty($_REQUEST['page']) || !is_numeric($_REQUEST['page'])) $_REQUEST['page']=1;
$criteria->setSort('reviewid');
$criteria->setOrder('DESC');
$criteria->setLimit($jieqiConfigs['article']['reviewnum']);
$criteria->setStart(($_REQUEST['page']-1) * $jieqiConfigs['article']['reviewnum']);
$review_handler->queryObjects($criteria);
$reviewrows=array();
$k=0;
while($v = $review_handler->getObject()){
	$start=3;
	if($v->getVar('topflag')==1) {
		$reviewrows[$k]['topflag']=1;
		$start+=4;
	}else{
		$reviewrows[$k]['topflag']=0;
	}
	if($v->getVar('goodflag')==1) {
		$reviewrows[$k]['goodflag']=1;
		$start+=4;
	}else{
		$reviewrows[$k]['goodflag']=0;
	}
	$reviewrows[$k]['postdate']=date(JIEQI_DATE_FORMAT.' '.JIEQI_TIME_FORMAT, $v->getVar('postdate'));
	$reviewrows[$k]['userid']=$v->getVar('userid');
	$reviewrows[$k]['username']=$v->getVar('username');
	if($jieqiConfigs['article']['reviewenter']=='0'){
		$reviewrows[$k]['content']=jieqi_htmlstr(jieqi_limitwidth(str_replace(array("\r", "\n"), array('', ' '), $v->getVar('reviewtext', 'n')), $jieqiConfigs['article']['reviewwidth'], $start));
	}else{
		$reviewrows[$k]['content']=jieqi_htmlstr(jieqi_limitwidth($v->getVar('reviewtext', 'n'), $jieqiConfigs['article']['reviewwidth'], $start));
	}
	$reviewrows[$k]['url_top']=jieqi_addurlvars(array('action'=>'top', 'rid'=>$v->getVar('reviewid')));
	$reviewrows[$k]['url_untop']=jieqi_addurlvars(array('action'=>'untop', 'rid'=>$v->getVar('reviewid')));
	$reviewrows[$k]['url_good']=jieqi_addurlvars(array('action'=>'good', 'rid'=>$v->getVar('reviewid')));
	$reviewrows[$k]['url_normal']=jieqi_addurlvars(array('action'=>'normal', 'rid'=>$v->getVar('reviewid')));
	$reviewrows[$k]['url_delete']=jieqi_addurlvars(array('action'=>'del', 'rid'=>$v->getVar('reviewid')));
	$reviewrows[$k]['checkbox']='<input type="checkbox" id="checkid[]" name="checkid[]" value="'.$v->getVar('reviewid').'">';
	$reviewrows[$k]['articleid']=$v->getVar('articleid');
	$reviewrows[$k]['articlename']=$v->getVar('articlename');
	if($jieqiConfigs['article']['fakeinfo'] > 0){
		$reviewrows[$k]['articlesubdir']=jieqi_getsubdir($v->getVar('articleid'));  //子目录
		if(!empty($jieqiConfigs['article']['fakeprefix'])) $tmpvar='/'.$jieqiConfigs['article']['fakeprefix'].'info';
		else $tmpvar='/files/article/info';
		$reviewrows[$k]['url_articleinfo']=$article_dynamic_url.$tmpvar.$reviewrows[$k]['articlesubdir'].'/'.$v->getVar('articleid').$jieqiConfigs['article']['fakefile'];  //子目录
	}else{
		$reviewrows[$k]['articlesubdir']='';
		$reviewrows[$k]['url_articleinfo']=$article_dynamic_url.'/articleinfo.php?id='.$v->getVar('articleid');  //子目录
	}
	$k++;
}
$jieqiTpl->assign_by_ref('reviewrows', $reviewrows);
//处理页面跳转
include_once(JIEQI_ROOT_PATH.'/lib/html/page.php');
$jumppage = new JieqiPage($review_handler->getCount($criteria),$jieqiConfigs['article']['reviewnum'],$_REQUEST['page']);
$jumppage->setlink('', true, true);
$jieqiTpl->assign('url_jumppage',$jumppage->whole_bar());

$jieqiTpl->setCaching(0);
$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'].'/templates/admin/review.html';
include_once(JIEQI_ROOT_PATH.'/admin/footer.php');

?>