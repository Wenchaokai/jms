<?php 
/**
 * 根据动态程序生成静态文件
 *
 * 根据动态程序生成静态文件
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: makestatic.php 330 2009-02-09 16:07:35Z juny $
 */



define('JIEQI_MODULE_NAME', 'article');
require_once('../../global.php');

if(empty($_REQUEST['id'])) exit('error id');
$_REQUEST['id'] = intval($_REQUEST['id']);
if($_REQUEST['id'] <= 0) exit('error id');

//检查密钥
if(empty($_REQUEST['key'])) exit('no key');
elseif(defined('JIEQI_SITE_KEY') && $_REQUEST['key'] != JIEQI_SITE_KEY) exit('error key');
elseif($_REQUEST['key'] != md5(JIEQI_DB_USER.JIEQI_DB_PASS.JIEQI_DB_NAME)) exit('error key');

@set_time_limit(0);
@session_write_close();

jieqi_getcachevars('article', 'articleuplog');
if(!is_array($jieqiArticleuplog)) $jieqiArticleuplog=array('articleuptime'=>0, 'chapteruptime'=>0);
$jieqiArticleuplog['articleuptime']=JIEQI_NOW_TIME;
$jieqiArticleuplog['chapteruptime']=JIEQI_NOW_TIME;
jieqi_setcachevars('articleuplog', 'jieqiArticleuplog', $jieqiArticleuplog, 'article');


//更新静态页
include_once($jieqiModules['article']['path'].'/include/funstatic.php');
switch($_REQUEST['action']){
	case 'articlenew':
		article_make_sinfo($_REQUEST['id']);
		article_make_ptoplist('lastupdate', 1);
		article_make_psort(intval($_REQUEST['sortid']), 1);
		article_make_psort(0, 1);
		break;
	case 'articledel':
		article_delete_sinfo($_REQUEST['id']);
		break;
	case 'articleedit':
		article_make_sinfo($_REQUEST['id']);
		break;
	case 'chapternew':
		article_make_sinfo($_REQUEST['id']);
		article_make_ptoplist('lastupdate', 1);
		article_make_psort(intval($_REQUEST['sortid']), 1);
		article_make_psort(0, 1);
		break;
	case 'reviewnew':
		article_make_sinfo($_REQUEST['id']);
		break;
	default:
		article_make_sinfo($_REQUEST['id']);
		break;
}

?>