<?php
/**
 * 小说内容赋值相关函数
 *
 * 小说内容赋值相关函数
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: funarticle.php 339 2009-06-23 03:03:24Z juny $
 */

/**
 * 传入小说实例对象，返回适合模板赋值的小说信息数组
 * 
 * @param      object      $article 论坛实例
 * @access     public
 * @return     array
 */
function jieqi_article_vars($article){
	global $jieqiModules;
	global $jieqiSort;
	global $jieqiConfigs;
	global $jieqiLang;
	global $article_static_url;
	global $article_dynamic_url;
	global $jieqiOption;
	if(!isset($jieqiSort['article'])) jieqi_getconfigs('article', 'sort');
	if(!isset($jieqiConfigs['article'])) jieqi_getconfigs('article', 'configs');
	if(!isset($jieqiLang['article'])) jieqi_loadlang('list', JIEQI_MODULE_NAME);
	if(!isset($article_static_url)) $article_static_url = (empty($jieqiConfigs['article']['staticurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
	if(!isset($article_dynamic_url)) $article_dynamic_url = (empty($jieqiConfigs['article']['dynamicurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];
	
	$ret = array();
	$ret['articleid']=$article->getVar('articleid');  //文章序号
	$ret['articlename']=$article->getVar('articlename');  //文章名称
	$ret['intro']=htmlspecialchars(jieqi_substr($article->getVar('intro', 'n'),0, 250));
	$ret['articlesubdir']=jieqi_getsubdir($article->getVar('articleid'));  //子目录
	
	$ret['url_articleinfo']=jieqi_geturl('article', 'article', $article->getVar('articleid'), 'info');
	if($article->getVar('lastchapter')==''){
		$ret['lastchapterid']=0;  //章节序号
		$ret['lastchapter']='';  //章节名称
		$ret['url_lastchapter']='';  //章节地址
	}else{
		$ret['lastchapterid']=$article->getVar('lastchapterid');
		$ret['lastchapter']=$article->getVar('lastchapter');
		$ret['url_articleindex']=jieqi_geturl('article', 'article', $article->getVar('articleid'), 'index');
		$ret['url_lastchapter']=jieqi_geturl('article', 'chapter', $article->getVar('lastchapterid'), $article->getVar('articleid'));
	}
	$ret['url_index'] = $ret['url_articleindex'];
	$ret['lastvolumeid']=$article->getVar('lastvolumeid');  //分卷序号
	$ret['lastvolume']=$article->getVar('lastvolume');  //分卷名称
	$ret['authorid']=$article->getVar('authorid');  //作者
	$ret['author']=$article->getVar('author');
	$ret['posterid']=$article->getVar('posterid');  //发表者
	$ret['poster']=$article->getVar('poster');
	$ret['agentid']=$article->getVar('agentid');  //代理者
	$ret['agent']=$article->getVar('agent');
	$ret['sortid']=$article->getVar('sortid');  //类别序号
	if(isset($jieqiSort['article'][$ret['sortid']]['caption'])) $ret['sort']=$jieqiSort['article'][$ret['sortid']]['caption'];  //类别
	else $ret['sort']='';
	$ret['typeid']=$article->getVar('typeid');  //字类别序号
	if($ret['typeid'] > 0 && isset($jieqiSort['article'][$ret['sortid']]['types'][$ret['typeid']])) $ret['type']=$jieqiSort['article'][$ret['sortid']]['types'][$ret['typeid']];  //类别
	else $ret['type']='';
	if(empty($ret['type'])) $ret['type'] = $ret['sort'];
	$ret['size']=$article->getVar('size');
	$ret['size_k']=ceil($article->getVar('size')/1024);
	$ret['size_c']=ceil($article->getVar('size')/2);
	$ret['dayvisit']=$article->getVar('dayvisit');
	$ret['weekvisit']=$article->getVar('weekvisit');
	$ret['monthvisit']=$article->getVar('monthvisit');
	$ret['allvisit']=$article->getVar('allvisit');
	$ret['dayvote']=$article->getVar('dayvote');
	$ret['weekvote']=$article->getVar('weekvote');
	$ret['monthvote']=$article->getVar('monthvote');
	$ret['allvote']=$article->getVar('allvote');
	$ret['goodnum']=$article->getVar('goodnum');
	$ret['badnum']=$article->getVar('badnum');
	$ret['display']=$article->getVar('display');
	
	$ret['lastupdate']=date('y-m-d',$article->getVar('lastupdate'));
	$ret['update']=date('m-d',$article->getVar('lastupdate'));
	$ret['postdate']=date('m-d',$article->getVar('postdate'));
	$ret['uptime']=$article->getVar('lastupdate');
	$ret['posttime']=$article->getVar('postdate');
	$ret['lastvote']=$article->getVar('lastvote');
	$ret['isfull'] = $article->getVar('fullflag');


	if(!isset($jieqiOption)) jieqi_getconfigs('article', 'option', 'jieqiOption');
	
	$tmpvar = $article->getVar('fullflag');
	if(isset($jieqiOption['article']['fullflag']['items'][$tmpvar])) $ret['fullflag']=$jieqiOption['article']['fullflag']['items'][$tmpvar];
	else $ret['fullflag']=$jieqiOption['article']['fullflag']['items'][$jieqiOption['article']['fullflag']['default']];
	
	$tmpvar = $article->getVar('permission');
	if(isset($jieqiOption['article']['permission']['items'][$tmpvar])) $ret['permission']=$jieqiOption['article']['permission']['items'][$tmpvar];
	else $ret['permission']=$jieqiOption['article']['permission']['items'][$jieqiOption['article']['permission']['default']];
		
	$tmpvar = $article->getVar('firstflag');
	if(isset($jieqiOption['article']['firstflag']['items'][$tmpvar])) $ret['firstflag']=$jieqiOption['article']['firstflag']['items'][$tmpvar];
	else $ret['firstflag']=$jieqiOption['article']['firstflag']['items'][$jieqiOption['article']['firstflag']['default']];
	
	$ret['imgflag']=$article->getVar('imgflag','n');
	$ret['url_image'] = jieqi_geturl('article', 'cover', $article->getVar('articleid'), 's', $article->getVar('imgflag','n'));
	
	return $ret;
}

?>