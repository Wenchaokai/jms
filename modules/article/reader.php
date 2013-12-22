<?php 
/**
 * 动态文章阅读
 *
 * 显示章节目录或者一个章节内容
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: reader.php 339 2009-06-23 03:03:24Z juny $
 */

define('JIEQI_MODULE_NAME', 'article');
require_once('../../global.php');
if(empty($_REQUEST['aid'])) jieqi_printfail(LANG_ERROR_PARAMETER);
include_once(JIEQI_ROOT_PATH.'/header.php');
include_once($jieqiModules['article']['path'].'/class/package.php');
$package=new JieqiPackage($_REQUEST['aid']);
if($package->loadOPF()){
	if(!empty($_REQUEST['cid'])){
		if(!$package->showChapter($_REQUEST['cid'])) $package->showIndex();
	}else{
		$package->showIndex();
	}
}else{
	jieqi_loadlang('article', JIEQI_MODULE_NAME);
	jieqi_printfail($jieqiLang['article']['article_not_exists']);
}

?>