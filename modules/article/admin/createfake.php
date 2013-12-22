<?php 
/**
 * 执行生成伪静态页面
 *
 * 执行生成伪静态页面
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: createfake.php 339 2009-06-23 03:03:24Z juny $
 */

define('JIEQI_USE_GZIP','0');
define('JIEQI_MODULE_NAME', 'article');
require_once('../../../global.php');
//检查权限
jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
//管理别人文章权限
jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, false, true);
@set_time_limit(0);
@session_write_close();
jieqi_loadlang('manage', JIEQI_MODULE_NAME);
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
echo '                                                                                                                                                                                                                                                                ';
//if(!is_numeric($_REQUEST['startid']) || !is_numeric($_REQUEST['stopid'])) jieqi_printfail($jieqiLang['article']['create_id_neednum']);
if(empty($_REQUEST['startid']) || !is_numeric($_REQUEST['startid'])) $_REQUEST['startid'] = 1;
else $_REQUEST['startid']=intval($_REQUEST['startid']);
if(empty($_REQUEST['stopid']) || !is_numeric($_REQUEST['stopid'])) $_REQUEST['stopid'] = 0;
else $_REQUEST['stopid']=intval($_REQUEST['stopid']);


switch($_REQUEST['action']){
	case 'makeinfo':
		if(empty($_REQUEST['stopid'])){
			jieqi_includedb();
			$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
			$sql = "SELECT MAX(articleid) AS mid FROM ".jieqi_dbprefix('article_article')." WHERE 1";
			$query->execute($sql);
			if($row = $query->getRow()) $_REQUEST['stopid'] = intval($row['mid']);
		}
		if($_REQUEST['startid'] > $_REQUEST['stopid']) jieqi_printfail($jieqiLang['article']['create_id_numerror']);

		echo sprintf($jieqiLang['article']['create_info_doing'], $_REQUEST['startid'], $_REQUEST['stopid']);
		ob_flush();
		flush();
		if(is_numeric($jieqiConfigs['article']['fakeinfo'])){
			if(!empty($jieqiConfigs['article']['fakeprefix'])) $jieqiConfigs['article']['fakeinfo']='/'.$jieqiConfigs['article']['fakeprefix'].'info<{$id|subdirectory}>/<{$id}>'.$jieqiConfigs['article']['fakefile'];
			else $jieqiConfigs['article']['fakeinfo']='/files/article/info<{$id|subdirectory}>/<{$id}>'.$jieqiConfigs['article']['fakefile'];
		}
		$jieqiConfigs['article']['fakeinfo'] = preg_replace('/https?:\/\/[^\/]+/is', '', $jieqiConfigs['article']['fakeinfo']);
		if(substr($jieqiConfigs['article']['fakeinfo'], 0, 1) != '/') $jieqiConfigs['article']['fakeinfo'] = '/'.$jieqiConfigs['article']['fakeinfo'];

		$tmpary = explode('/', $jieqiConfigs['article']['fakeinfo']);
		$tmpcot = count($tmpary) - 2;
		if(strpos($jieqiConfigs['article']['fakeinfo'], '<{$id|subdirectory}>') > 0) $tmpcot++;
		$globalfile = str_repeat('../', $tmpcot).'global.php';

		for($i=$_REQUEST['startid'];$i<=$_REQUEST['stopid'];$i++){
			$repfrom = array('<{$id|subdirectory}>', '<{$id}>');
			$repto = array(jieqi_getsubdir($i), $i);
			$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinfo']));
			jieqi_checkdir(dirname($fname), true);
			if($_REQUEST['filetype']=='static'){
				$content = file_get_contents($jieqiModules['article']['url'].'/articleinfo.php?id='.$i);
			}else{
				$content='<?php
define(\'JIEQI_MODULE_NAME\', \'article\');
$jieqi_fake_state = 1;
include_once(\''.$globalfile.'\');
$_REQUEST[\'id\'] = '.$i.';
include_once($jieqiModules[\'article\'][\'path\'].\'/articleinfo.php\');
?>';
			}
			jieqi_writefile($fname, $content);
			if(($i-$_REQUEST['startid'])%100==0){
				echo $i.'...';
				ob_flush();
				flush();
			}
		}
		echo $_REQUEST['stopid'];
		jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['create_info_success']);
		break;
	case 'makesort':
		if(empty($_REQUEST['stopid']) && JIEQI_MAX_PAGES > 0) $_REQUEST['stopid'] = JIEQI_MAX_PAGES;
		$default_stopid = $_REQUEST['stopid'];
		if(empty($_REQUEST['stopid'])){
			jieqi_includedb();
			$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
			$sql = "SELECT count(*) AS cot FROM ".jieqi_dbprefix('article_article')." WHERE display=0 AND size>0";
			$query->execute($sql);
			if($row = $query->getRow()){
				$cot = intval($row['cot']);
				$pnum = intval($jieqiConfigs['article']['pagenum']);
				if(empty($pnum)) $pnum = 10;
				$_REQUEST['stopid'] = ceil($cot / $pnum);
			}
		}
		if($_REQUEST['startid'] > $_REQUEST['stopid']) jieqi_printfail($jieqiLang['article']['create_id_numerror']);

		echo sprintf($jieqiLang['article']['create_sort_doing'], $_REQUEST['startid'], $_REQUEST['stopid']);
		ob_flush();
		flush();
		jieqi_getconfigs(JIEQI_MODULE_NAME, 'sort');

		if(is_numeric($jieqiConfigs['article']['fakesort'])){
			if(!empty($jieqiConfigs['article']['fakeprefix'])) $jieqiConfigs['article']['fakesort']='/'.$jieqiConfigs['article']['fakeprefix'].'sort<{$class}><{$page|subdirectory}>/<{$page}>'.$jieqiConfigs['article']['fakefile'];
			else $jieqiConfigs['article']['fakesort']='/files/article/sort<{$class}><{$page|subdirectory}>/<{$page}>'.$jieqiConfigs['article']['fakefile'];
		}

		$jieqiConfigs['article']['fakesort'] = preg_replace('/https?:\/\/[^\/]+/is', '', $jieqiConfigs['article']['fakesort']);
		if(substr($jieqiConfigs['article']['fakesort'], 0, 1) != '/') $jieqiConfigs['article']['fakesort'] = '/'.$jieqiConfigs['article']['fakesort'];

		$tmpary = explode('/', $jieqiConfigs['article']['fakesort']);
		$tmpcot = count($tmpary) - 2;
		if(strpos($jieqiConfigs['article']['fakesort'], '<{$page|subdirectory}>') > 0) $tmpcot++;
		$globalfile = str_repeat('../', $tmpcot).'global.php';

		for($i=$_REQUEST['startid'];$i<=$_REQUEST['stopid'];$i++){
			$repfrom = array('<{$class}>', '<{$page|subdirectory}>', '<{$page}>');
			$repto = array('', jieqi_getsubdir($i), $i);
			$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakesort']));
			jieqi_checkdir(dirname($fname), true);
			if($_REQUEST['filetype']=='static'){
				$content = file_get_contents($jieqiModules['article']['url'].'/articlelist.php?class=0&page='.$i);
			}else{
				$content='<?php
define(\'JIEQI_MODULE_NAME\', \'article\');
$jieqi_fake_state = 1;
include_once(\''.$globalfile.'\');
$_REQUEST[\'class\'] = 0;
$_REQUEST[\'page\'] = '.$i.';
include_once($jieqiModules[\'article\'][\'path\'].\'/articlelist.php\');
?>';
			}
			jieqi_writefile($fname, $content);
			if(($i-$_REQUEST['startid'])%100==0){
				echo $i.'...';
				ob_flush();
				flush();
			}
		}
		echo $_REQUEST['stopid'];
		foreach($jieqiSort['article'] as $k=>$v){

			$_REQUEST['stopid'] = $default_stopid;
			if(empty($_REQUEST['stopid'])){
				jieqi_includedb();
				$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
				$sql = "SELECT count(*) AS cot FROM ".jieqi_dbprefix('article_article')." WHERE display=0 AND size>0 AND sortid=".intval($k);
				$query->execute($sql);
				if($row = $query->getRow()){
					$cot = intval($row['cot']);
					$pnum = intval($jieqiConfigs['article']['pagenum']);
					if(empty($pnum)) $pnum = 10;
					$_REQUEST['stopid'] = ceil($cot / $pnum);
				}
			}


			echo sprintf($jieqiLang['article']['create_sort_info'], $v['caption']);
			ob_flush();
			flush();

			for($i=$_REQUEST['startid'];$i<=$_REQUEST['stopid'];$i++){
				$repfrom = array('<{$class}>', '<{$page|subdirectory}>', '<{$page}>');
				$repto = array($k, jieqi_getsubdir($i), $i);
				$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakesort']));
				jieqi_checkdir(dirname($fname), true);

				if($_REQUEST['filetype']=='static'){
					$content = file_get_contents($jieqiModules['article']['url'].'/articlelist.php?class='.$k.'&page='.$i);
				}else{
					$content='<?php
define(\'JIEQI_MODULE_NAME\', \'article\');
$jieqi_fake_state = 1;
include_once(\''.$globalfile.'\');
$_REQUEST[\'class\'] = '.$k.';
$_REQUEST[\'page\'] = '.$i.';
include_once($jieqiModules[\'article\'][\'path\'].\'/articlelist.php\');
?>';
				}
				jieqi_writefile($fname, $content);
				if(($i-$_REQUEST['startid'])%100==0){
					echo $i.'...';
					ob_flush();
					flush();
				}
			}
			echo $_REQUEST['stopid'];
		}
		jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['create_sort_success']);
		break;
	case 'makeinitial':
		if(empty($_REQUEST['stopid']) && JIEQI_MAX_PAGES > 0) $_REQUEST['stopid'] = JIEQI_MAX_PAGES;
		echo sprintf($jieqiLang['article']['create_initial_doing'], $_REQUEST['startid'], $_REQUEST['stopid']);
		ob_flush();
		flush();
		$initary['1']='1';
		for($i=65; $i<=90; $i++){
			$tmpvar=chr($i);
			$initary[$tmpvar]=$tmpvar;
		}
		$initary['0']='0';

		if(is_numeric($jieqiConfigs['article']['fakeinitial'])){
			if(!empty($jieqiConfigs['article']['fakeprefix'])) $jieqiConfigs['article']['fakeinitial']='/'.$jieqiConfigs['article']['fakeprefix'].'initial<{$initial}><{$page|subdirectory}>/<{$page}>'.$jieqiConfigs['article']['fakefile'];
			else $jieqiConfigs['article']['fakeinitial']='/files/article/initial<{$initial}><{$page|subdirectory}>/<{$page}>'.$jieqiConfigs['article']['fakefile'];
		}
		$jieqiConfigs['article']['fakeinitial'] = preg_replace('/https?:\/\/[^\/]+/is', '', $jieqiConfigs['article']['fakeinitial']);
		if(substr($jieqiConfigs['article']['fakeinitial'], 0, 1) != '/') $jieqiConfigs['article']['fakeinitial'] = '/'.$jieqiConfigs['article']['fakeinitial'];

		$tmpary = explode('/', $jieqiConfigs['article']['fakeinitial']);
		$tmpcot = count($tmpary) - 2;
		if(strpos($jieqiConfigs['article']['fakeinitial'], '<{$page|subdirectory}>') > 0) $tmpcot++;
		$globalfile = str_repeat('../', $tmpcot).'global.php';

		$default_stopid = $_REQUEST['stopid'];
		foreach($initary as $k=>$v){
			$_REQUEST['stopid'] = $default_stopid;
			if(empty($_REQUEST['stopid'])){
				jieqi_includedb();
				$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
				$sql = "SELECT count(*) AS cot FROM ".jieqi_dbprefix('article_article')." WHERE display=0 AND size>0 AND initial ='".jieqi_dbslashes(strtoupper($v))."'";
				$query->execute($sql);
				if($row = $query->getRow()){
					$cot = intval($row['cot']);
					$pnum = intval($jieqiConfigs['article']['pagenum']);
					if(empty($pnum)) $pnum = 10;
					$_REQUEST['stopid'] = ceil($cot / $pnum);
				}
			}
			echo sprintf($jieqiLang['article']['create_initial_info'], $v);
			ob_flush();
			flush();

			for($i=$_REQUEST['startid'];$i<=$_REQUEST['stopid'];$i++){
				$repfrom = array('<{$initial}>', '<{$page|subdirectory}>', '<{$page}>');
				$repto = array($v, jieqi_getsubdir($i), $i);
				$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinitial']));
				jieqi_checkdir(dirname($fname), true);
				if($_REQUEST['filetype']=='static'){
					$content = file_get_contents($jieqiModules['article']['url'].'/articlelist.php?initial='.$v.'&page='.$i);
				}else{
					$content='<?php
define(\'JIEQI_MODULE_NAME\', \'article\');
$jieqi_fake_state = 1;
include_once(\''.$globalfile.'\');
$_REQUEST[\'initial\'] = "'.$v.'";
$_REQUEST[\'page\'] = '.$i.';
include_once($jieqiModules[\'article\'][\'path\'].\'/articlelist.php\');
?>';
				}
				jieqi_writefile($fname, $content);
				if(($i-$_REQUEST['startid'])%100==0){
					echo $i.'...';
					ob_flush();
					flush();
				}
			}
			echo $_REQUEST['stopid'];
		}
		jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['create_initial_success']);
		break;
	case 'maketoplist':
		if(empty($_REQUEST['stopid']) && JIEQI_MAX_PAGES > 0) $_REQUEST['stopid'] = JIEQI_MAX_PAGES;
		echo sprintf($jieqiLang['article']['create_toplist_doing'], $_REQUEST['startid'], $_REQUEST['stopid']);
		ob_flush();
		flush();
		$topary=array('allvisit'=>$jieqiLang['article']['top_allvisit'], 'monthvisit'=>$jieqiLang['article']['top_monthvisit'], 'weekvisit'=>$jieqiLang['article']['top_weekvisit'], 'dayvisit'=>$jieqiLang['article']['top_dayvisit'], 'allauthorvisit'=>$jieqiLang['article']['top_avall'], 'monthauthorvisit'=>$jieqiLang['article']['top_avmonth'], 'weekauthorvisit'=>$jieqiLang['article']['top_avweek'], 'dayauthorvisit'=>$jieqiLang['article']['top_avday'], 'allvote'=>$jieqiLang['article']['top_voteall'], 'monthvote'=>$jieqiLang['article']['top_votemonth'], 'weekvote'=>$jieqiLang['article']['top_voteweek'], 'dayvote'=>$jieqiLang['article']['top_voteday_titile'], 'postdate'=>$jieqiLang['article']['top_postdate'], 'toptime'=>$jieqiLang['article']['top_toptime'], 'goodnum'=>$jieqiLang['article']['top_goodnum'], 'size'=>$jieqiLang['article']['top_size'], 'authorupdate'=>$jieqiLang['article']['top_authorupdate'], 'masterupdate'=>$jieqiLang['article']['top_masterupdate'], 'lastupdate'=>$jieqiLang['article']['top_lastupdate']);

		if(is_numeric($jieqiConfigs['article']['faketoplist'])){
			if(!empty($jieqiConfigs['article']['fakeprefix'])) $jieqiConfigs['article']['faketoplist']='/'.$jieqiConfigs['article']['fakeprefix'].'top<{$sort}><{$page|subdirectory}>/<{$page}>'.$jieqiConfigs['article']['fakefile'];
			else $jieqiConfigs['article']['faketoplist']='/files/article/top<{$sort}><{$page|subdirectory}>/<{$page}>'.$jieqiConfigs['article']['fakefile'];
		}
		$jieqiConfigs['article']['faketoplist'] = preg_replace('/https?:\/\/[^\/]+/is', '', $jieqiConfigs['article']['faketoplist']);
		if(substr($jieqiConfigs['article']['faketoplist'], 0, 1) != '/') $jieqiConfigs['article']['faketoplist'] = '/'.$jieqiConfigs['article']['faketoplist'];

		$tmpary = explode('/', $jieqiConfigs['article']['faketoplist']);
		$tmpcot = count($tmpary) - 2;
		if(strpos($jieqiConfigs['article']['faketoplist'], '<{$page|subdirectory}>') > 0) $tmpcot++;
		$globalfile = str_repeat('../', $tmpcot).'global.php';

		$default_stopid = $_REQUEST['stopid'];
		foreach($topary as $k=>$v){
			$_REQUEST['stopid'] = $default_stopid;
			if(empty($_REQUEST['stopid'])){
				jieqi_includedb();
				$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
				$sql = "SELECT count(*) AS cot FROM ".jieqi_dbprefix('article_article')." WHERE display=0 AND size>0";

				$tmpvar=explode('-',date('Y-m-d',JIEQI_NOW_TIME));
				$daystart=mktime(0,0,0,(int)$tmpvar[1],(int)$tmpvar[2],(int)$tmpvar[0]);
				$monthstart=mktime(0,0,0,(int)$tmpvar[1],1,(int)$tmpvar[0]);
				$tmpvar=date('w',JIEQI_NOW_TIME);
				if($tmpvar==0) $tmpvar=7; //星期天是0，国人习惯作为作为一星期的最后一天
				$weekstart=$daystart;
				if($tmpvar>1) $weekstart-=($tmpvar-1) * 86400;
				switch($k){
					case 'monthvisit':
					case 'mouthvisit':
						$sql .= " AND lastvisit >= ".$monthstart;
						break;
					case 'weekvisit':
						$sql .= " AND lastvisit >= ".$weekstart;
						break;
					case 'dayvisit':
						$sql .= " AND lastvisit >= ".$daystart;
						break;
					case 'allauthorvisit':
						$sql .= " AND authorid > 0";
						break;
					case 'monthauthorvisit':
					case 'mouthauthorvisit':
						$sql .= " AND authorid > 0 AND lastvisit >= ".$monthstart;
						break;
					case 'weekauthorvisit':
						$sql .= " AND authorid > 0 AND lastvisit >= ".$weekstart;
						break;
					case 'dayauthorvisit':
						$sql .= " AND authorid > 0 AND lastvisit >= ".$daystart;
						break;
					case 'monthvote':
					case 'mouthvote':
						$sql .= " AND lastvote >= ".$monthstart;
						break;
					case 'weekvote':
						$sql .= " AND lastvote >= ".$weekstart;
						break;
					case 'dayvote':
						$sql .= " AND lastvote >= ".$daystart;
						break;
					case 'authorupdate':
						$sql .= " AND authorid > 0";
						break;
					case 'masterupdate':
						$sql .= " AND authorid = 0";
						break;
				}

				$query->execute($sql);
				if($row = $query->getRow()){
					$cot = intval($row['cot']);
					$pnum = intval($jieqiConfigs['article']['toppagenum']);
					if(empty($pnum)) $pnum = 10;
					$_REQUEST['stopid'] = ceil($cot / $pnum);
				}
			}
			echo sprintf($jieqiLang['article']['create_toplist_info'], $v);
			ob_flush();
			flush();

			for($i=$_REQUEST['startid'];$i<=$_REQUEST['stopid'];$i++){
				$repfrom = array('<{$sort}>', '<{$page|subdirectory}>', '<{$page}>');
				$repto = array($k, jieqi_getsubdir($i), $i);
				$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['faketoplist']));
				jieqi_checkdir(dirname($fname), true);
				if($_REQUEST['filetype']=='static'){
					$content = file_get_contents($jieqiModules['article']['url'].'/toplist.php?sort='.$k.'&page='.$i);
				}else{
					$content='<?php
define(\'JIEQI_MODULE_NAME\', \'article\');
$jieqi_fake_state = 1;
include_once(\''.$globalfile.'\');
$_REQUEST[\'sort\'] = "'.$k.'";
$_REQUEST[\'page\'] = '.$i.';
include_once($jieqiModules[\'article\'][\'path\'].\'/toplist.php\');
?>';
				}
				jieqi_writefile($fname, $content);
				if(($i-$_REQUEST['startid'])%100==0){
					echo $i.'...';
					ob_flush();
					flush();
				}
			}
			echo $_REQUEST['stopid'];
		}
		jieqi_msgwin(LANG_DO_SUCCESS, $jieqiLang['article']['create_toplist_success']);
		break;
	default:
		jieqi_printfail($jieqiLang['article']['create_para_error']);
}

//取得文件保存目录
function getsubdir($dirname, $id)
{
	global $jieqiConfigs;
	$retdir='';
	if(!empty($dirname) && is_numeric($id)){
		$retdir .= jieqi_getsubdir($id);
		if (!file_exists($retdir)) jieqi_createdir($retdir);
	}
	return $retdir;
}
?>