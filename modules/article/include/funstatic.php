<?php
//生成静态页相关函数

//生成文章信息页静态（批量）
function article_make_binfo($fid=1, $toid=0, $static = true, $output = false){
	global $query;
	if(!is_a($query, 'JieqiQueryHandler')){
		jieqi_includedb();
		$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
	}
	$where = '';
	if($fid > 1) $where .= empty($where) ? ' articleid >= '.$fid : ' AND articleid >= '.$fid;
	if($toid > 0) $where .= empty($where) ? ' articleid <= '.$toid : ' AND articleid <= '.$toid;
	$sql = "SELECT articleid FROM ".jieqi_dbprefix('article_article');
	$sql .= empty($where) ? ' WHERE 1' : ' WHERE'.$where;
	$query->execute($sql);
	$aids = array();
	while($row = $query->getRow()){
		$aids[] = $row['articleid'];
	}
	foreach($aids as $aid){
		article_make_sinfo($aid, $static, $output);
	}
}

//生成文章信息页静态（单页）
function article_make_sinfo($id, $static = true, $output = false){
	global $jieqiConfigs;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
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

	$repfrom = array('<{$id|subdirectory}>', '<{$id}>');
	$repto = array(jieqi_getsubdir($id), $id);
	$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinfo']));
	jieqi_checkdir(dirname($fname), true);
	if($static){
		$content = file_get_contents($GLOBALS['jieqiModules']['article']['url'].'/articleinfo.php?id='.$id);
	}else{
		$content='<?php
define(\'JIEQI_MODULE_NAME\', \'article\');
$jieqi_fake_state = 1;
include_once(\''.$globalfile.'\');
$_REQUEST[\'id\'] = '.$id.';
include_once($jieqiModules[\'article\'][\'path\'].\'/articleinfo.php\');
?>';
	}
	jieqi_writefile($fname, $content);
	if($output){
		echo $id.'.    ';
		ob_flush();
		flush();
	}
}

//删除文章信息页静态（单页）
function article_delete_sinfo($id, $output = false){
	global $jieqiConfigs;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
	if(is_numeric($jieqiConfigs['article']['fakeinfo'])){
		if(!empty($jieqiConfigs['article']['fakeprefix'])) $jieqiConfigs['article']['fakeinfo']='/'.$jieqiConfigs['article']['fakeprefix'].'info<{$id|subdirectory}>/<{$id}>'.$jieqiConfigs['article']['fakefile'];
		else $jieqiConfigs['article']['fakeinfo']='/files/article/info<{$id|subdirectory}>/<{$id}>'.$jieqiConfigs['article']['fakefile'];
	}
	$jieqiConfigs['article']['fakeinfo'] = preg_replace('/https?:\/\/[^\/]+/is', '', $jieqiConfigs['article']['fakeinfo']);
	if(substr($jieqiConfigs['article']['fakeinfo'], 0, 1) != '/') $jieqiConfigs['article']['fakeinfo'] = '/'.$jieqiConfigs['article']['fakeinfo'];

	$repfrom = array('<{$id|subdirectory}>', '<{$id}>');
	$repto = array(jieqi_getsubdir($id), $id);
	$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinfo']));
	if(is_file($fname)) jieqi_delfile($fname);
	if($output){
		echo $id.' ';
		ob_flush();
		flush();
	}
}

//===================================================================

//生成分类列表（全部）
function article_make_asort($fid=1, $tid=0, $static = true, $output = false){
	global $jieqiSort;
	if(!isset($jieqiSort['article'])) jieqi_getconfigs('article', 'sort');
	article_make_ssort(0, $fid, $tid, $static, $output);
	foreach($jieqiSort['article'] as $k=>$v){
		if($output){
			echo '<br />'.$v['caption'].'<br />';
			ob_flush();
			flush();
		}
		article_make_ssort($k, $fid, $tid, $static, $output);
	}
}

//生成分类列表（单类）
function article_make_ssort($class=0, $fid=1, $tid=0, $static = true, $output = false){
	global $jieqiConfigs;
	global $query;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');

	if(empty($tid) && JIEQI_MAX_PAGES > 0) $tid = JIEQI_MAX_PAGES;
	if(empty($tid)){
		if(!is_a($query, 'JieqiQueryHandler')){
			jieqi_includedb();
			$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
		}
		$sql = "SELECT count(*) AS cot FROM ".jieqi_dbprefix('article_article')." WHERE display=0 AND size>0";
		if($class > 0) $sql .= " AND sortid=".intval($class);
		$query->execute($sql);
		if($row = $query->getRow()){
			$cot = intval($row['cot']);
			$pnum = intval($jieqiConfigs['article']['pagenum']);
			if(empty($pnum)) $pnum = 10;
			$tid = ceil($cot / $pnum);
		}
		if($tid < 1) $tid = 1;
	}
	if($fid > $tid) return false;
	for($page=$fid; $page<=$tid; $page++) article_make_psort($class, $page, $static, $output);

}

//生成分类列表（单页）
function article_make_psort($class=0, $page=1, $static = true, $output = false){
	global $jieqiConfigs;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
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


	$repfrom = array('<{$class}>', '<{$page|subdirectory}>', '<{$page}>');
	$class = intval($class);
	if(empty($class)) $repc='';
	else $repc=$class;
	$repto = array($repc, jieqi_getsubdir($page), $page);
	$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakesort']));
	jieqi_checkdir(dirname($fname), true);
	if($static){
		$content = file_get_contents($GLOBALS['jieqiModules']['article']['url'].'/articlelist.php?class='.$class.'&page='.$page);
	}else{
		$content='<?php
define(\'JIEQI_MODULE_NAME\', \'article\');
$jieqi_fake_state = 1;
include_once(\''.$globalfile.'\');
$_REQUEST[\'class\'] = '.$class.';
$_REQUEST[\'page\'] = '.$page.';
include_once($jieqiModules[\'article\'][\'path\'].\'/articlelist.php\');
?>';
	}
	jieqi_writefile($fname, $content);
	if($output){
		echo $page.' ';
		ob_flush();
		flush();
	}
}

//===================================================================

//生成首字母列表（全部）
function article_make_ainitial($fid=1, $tid=0, $static = true, $output = false){
	$initary['1']='1';
	for($i=65; $i<=90; $i++){
		$tmpvar=chr($i);
		$initary[$tmpvar]=$tmpvar;
	}
	$initary['0']='0';
	foreach($initary as $k=>$v){
		if($output){
			echo '<br />['.strtoupper($v).']<br />';
			ob_flush();
			flush();
		}
		article_make_sinitial($v, $fid, $tid, $static, $output);
	}
}

//生成首字母列表（单字母）
function article_make_sinitial($initial, $fid=1, $tid=0, $static = true, $output = false){
	global $jieqiConfigs;
	global $query;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');

	if(empty($tid) && JIEQI_MAX_PAGES > 0) $tid = JIEQI_MAX_PAGES;
	if(empty($tid)){
		if(!is_a($query, 'JieqiQueryHandler')){
			jieqi_includedb();
			$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
		}
		$sql = "SELECT count(*) AS cot FROM ".jieqi_dbprefix('article_article')." WHERE display=0 AND size>0 AND initial ='".jieqi_dbslashes(strtoupper($initial))."'";
		$query->execute($sql);
		if($row = $query->getRow()){
			$cot = intval($row['cot']);
			$pnum = intval($jieqiConfigs['article']['pagenum']);
			if(empty($pnum)) $pnum = 10;
			$tid = ceil($cot / $pnum);
		}
		if($tid < 1) $tid = 1;
	}
	if($fid > $tid) return false;
	for($page=$fid; $page<=$tid; $page++) article_make_pinitial($initial, $page, $static, $output);

}

//生成首字母列表（单页）
function article_make_pinitial($initial, $page=1, $static = true, $output = false){
	global $jieqiConfigs;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
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


	$repfrom = array('<{$initial}>', '<{$page|subdirectory}>', '<{$page}>');
	$repto = array($initial, jieqi_getsubdir($page), $page);
	$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['fakeinitial']));
	jieqi_checkdir(dirname($fname), true);
	if($static){
		$content = file_get_contents($GLOBALS['jieqiModules']['article']['url'].'/articlelist.php?initial='.$initial.'&page='.$page);
	}else{
		$content='<?php
define(\'JIEQI_MODULE_NAME\', \'article\');
$jieqi_fake_state = 1;
include_once(\''.$globalfile.'\');
$_REQUEST[\'initial\'] = "'.$initial.'";
$_REQUEST[\'page\'] = '.$page.';
include_once($jieqiModules[\'article\'][\'path\'].\'/articlelist.php\');
?>';
	}
	jieqi_writefile($fname, $content);
	if($output){
		echo $page.' ';
		ob_flush();
		flush();
	}
}

//===================================================================

//生成排行榜列表（全部）
function article_make_atoplist($fid=1, $tid=0, $static = true, $output = false){
	global $jieqiLang;
	jieqi_loadlang('manage', 'article');
	$topary=array('allvisit'=>$jieqiLang['article']['top_allvisit'], 'monthvisit'=>$jieqiLang['article']['top_monthvisit'], 'weekvisit'=>$jieqiLang['article']['top_weekvisit'], 'dayvisit'=>$jieqiLang['article']['top_dayvisit'], 'allauthorvisit'=>$jieqiLang['article']['top_avall'], 'monthauthorvisit'=>$jieqiLang['article']['top_avmonth'], 'weekauthorvisit'=>$jieqiLang['article']['top_avweek'], 'dayauthorvisit'=>$jieqiLang['article']['top_avday'], 'allvote'=>$jieqiLang['article']['top_voteall'], 'monthvote'=>$jieqiLang['article']['top_votemonth'], 'weekvote'=>$jieqiLang['article']['top_voteweek'], 'dayvote'=>$jieqiLang['article']['top_voteday_titile'], 'postdate'=>$jieqiLang['article']['top_postdate'], 'toptime'=>$jieqiLang['article']['top_toptime'], 'goodnum'=>$jieqiLang['article']['top_goodnum'], 'size'=>$jieqiLang['article']['top_size'], 'authorupdate'=>$jieqiLang['article']['top_authorupdate'], 'masterupdate'=>$jieqiLang['article']['top_masterupdate'], 'lastupdate'=>$jieqiLang['article']['top_lastupdate']);
	foreach($topary as $k=>$v){
		if($output){
			echo '<br />'.$v.'<br />';
			ob_flush();
			flush();
		}
		article_make_stoplist($k, $fid, $tid, $static, $output);
	}
}

//生成排行榜列表（单类）
function article_make_stoplist($sort, $fid=1, $tid=0, $static = true, $output = false){
	global $jieqiConfigs;
	global $query;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');

	if(empty($tid) && JIEQI_MAX_PAGES > 0) $tid = JIEQI_MAX_PAGES;
	if(empty($tid)){
		if(!is_a($query, 'JieqiQueryHandler')){
			jieqi_includedb();
			$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
		}
		$sql = "SELECT count(*) AS cot FROM ".jieqi_dbprefix('article_article')." WHERE display=0 AND size>0";
		$tmpvar=explode('-',date('Y-m-d',JIEQI_NOW_TIME));
		$daystart=mktime(0,0,0,(int)$tmpvar[1],(int)$tmpvar[2],(int)$tmpvar[0]);
		$monthstart=mktime(0,0,0,(int)$tmpvar[1],1,(int)$tmpvar[0]);
		$tmpvar=date('w',JIEQI_NOW_TIME);
		if($tmpvar==0) $tmpvar=7; //星期天是0，国人习惯作为作为一星期的最后一天
		$weekstart=$daystart;
		if($tmpvar>1) $weekstart-=($tmpvar-1) * 86400;
		switch($sort){
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
			$tid = ceil($cot / $pnum);
		}
		if($tid < 1) $tid = 1;
	}
	if($fid > $tid) return false;
	for($page=$fid; $page<=$tid; $page++) article_make_ptoplist($sort, $page, $static, $output);

}

//生成排行榜列表（单页）
function article_make_ptoplist($sort, $page=1, $static = true, $output = false){
	global $jieqiConfigs;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
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

	$repfrom = array('<{$sort}>', '<{$page|subdirectory}>', '<{$page}>');
	$repto = array($sort, jieqi_getsubdir($page), $page);
	$fname = JIEQI_ROOT_PATH.trim(str_replace($repfrom, $repto, $jieqiConfigs['article']['faketoplist']));
	jieqi_checkdir(dirname($fname), true);
	if($static){
		$content = file_get_contents($GLOBALS['jieqiModules']['article']['url'].'/toplist.php?sort='.$sort.'&page='.$page);
	}else{
		$content='<?php
define(\'JIEQI_MODULE_NAME\', \'article\');
$jieqi_fake_state = 1;
include_once(\''.$globalfile.'\');
$_REQUEST[\'sort\'] = "'.$sort.'";
$_REQUEST[\'page\'] = '.$page.';
include_once($jieqiModules[\'article\'][\'path\'].\'/toplist.php\');
?>';
	}
	jieqi_writefile($fname, $content);
	if($output){
		echo $page.' ';
		ob_flush();
		flush();
	}
}

//使用链接更新静态
function article_update_static($action, $id, $sortid){
	global $jieqiConfigs;
	global $jieqiModules;
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
	$article_static_url = (empty($jieqiConfigs['article']['staticurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
	$article_dynamic_url = (empty($jieqiConfigs['article']['dynamicurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
	
	$url=$article_dynamic_url.'/makestatic.php?key='.urlencode(md5(JIEQI_DB_USER.JIEQI_DB_PASS.JIEQI_DB_NAME)).'&action='.urldecode($action).'&id='.intval($id).'&sortid='.intval($sortid);
	$url=trim($url);
	if(strtolower(substr($url,0,7)) != 'http://') $url='http://'.$_SERVER['HTTP_HOST'].$url;
	$ret = jieqi_socket_url1($url);
	
	//阅读服务器也生成一遍
	/*
	$url=$article_static_url.'/makestatic.php?key='.urlencode(md5(JIEQI_DB_USER.JIEQI_DB_PASS.JIEQI_DB_NAME)).'&action='.urldecode($action).'&id='.intval($id).'&sortid='.intval($sortid);
	$url=trim($url);
	if(strtolower(substr($url,0,7)) == 'http://') jieqi_socket_url1($url);
	*/
	return $ret;
}
function jieqi_socket_url1($url){
	$ret = @file_get_contents($url);
}

function jieqi_socket_url2($url){
	if(!function_exists('fsockopen')) return false;
	$method = "GET";
	$url_array = parse_url($url);
	$port = isset($url_array['port'])? $url_array['port'] : 80;
	$fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30);
	if(!$fp) return false;
	$getPath = $url_array['path'];
	if(!empty($url_array['query'])) $getPath .= "?". $url_array['query'];
	$header = $method . " " . $getPath;
	$header .= " HTTP/1.1\r\n";
	$header .= "Host: ". $url_array['host'] . "\r\n"; //HTTP 1.1 Host域不能省略
	/*
	//以下头信息域可以省略
	$header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 \r\n";
	$header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5 \r\n";
	$header .= "Accept-Language: en-us,en;q=0.5 ";
	$header .= "Accept-Encoding: gzip,deflate\r\n";
	*/
	$header .= "Connection:Close\r\n\r\n";
	fwrite($fp, $header);
	if(!feof($fp)) fgets($fp, 8);
	//while(!feof($fp)) echo fgets($fp, 128);
	fclose($fp);
	return true;
}
?>