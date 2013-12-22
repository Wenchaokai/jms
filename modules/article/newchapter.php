<?php 
/**
 * 增加章节
 *
 * 增加一个新章节
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: newchapter.php 339 2009-06-23 03:03:24Z juny $
 */

define('JIEQI_MODULE_NAME', 'article');
require_once('../../global.php');
if(empty($_REQUEST['aid'])) jieqi_printfail(LANG_ERROR_PARAMETER);
jieqi_loadlang('article', JIEQI_MODULE_NAME);
include_once($jieqiModules['article']['path'].'/class/article.php');
$article_handler =& JieqiArticleHandler::getInstance('JieqiArticleHandler');
$article=$article_handler->get($_REQUEST['aid']);
if(!$article) jieqi_printfail($jieqiLang['article']['article_not_exists']);

jieqi_getconfigs(JIEQI_MODULE_NAME, 'power');
//管理别人文章权限
$canedit=jieqi_checkpower($jieqiPower['article']['manageallarticle'], $jieqiUsersStatus, $jieqiUsersGroup, true);
if(!$canedit && !empty($_SESSION['jieqiUserId'])){
	//除了斑竹，作者、发表者和代理人可以修改文章
	$tmpvar=$_SESSION['jieqiUserId'];
	if($tmpvar>0 && ($article->getVar('authorid')==$tmpvar || $article->getVar('posterid')==$tmpvar || $article->getVar('agentid')==$tmpvar)){
		$canedit=true;
	}
}
if(!$canedit) jieqi_printfail($jieqiLang['article']['noper_manage_article']);
//检查上传权限
$canupload = jieqi_checkpower($jieqiPower['article']['articleupattach'], $jieqiUsersStatus, $jieqiUsersGroup, true);


jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');

$article_static_url = (empty($jieqiConfigs['article']['staticurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
$article_dynamic_url = (empty($jieqiConfigs['article']['dynamicurl'])) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['dynamicurl'];

if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'chapter';

switch($_REQUEST['action']){
	case 'newchapter':
		@session_write_close();
		$volumeid = $_POST['volumeid'];
		$_POST['chaptername'] = trim($_POST['chaptername']);
		$errtext='';
		if(empty($_POST['chaptertype'])) $_POST['chaptertype']=0;
		//检查标题
		if (strlen($_POST['chaptername'])==0) $errtext .= $jieqiLang['article']['need_chapter_title'].'<br />';
		
		//检查标题和内容有没有违禁单词
		if(!isset($jieqiConfigs['system'])) jieqi_getconfigs('system', 'configs');
		if(!empty($jieqiConfigs['system']['postdenywords'])){
			include_once(JIEQI_ROOT_PATH.'/include/checker.php');
			$checker = new JieqiChecker();
			$matchwords1 = $checker->deny_words($_POST['chaptername'], $jieqiConfigs['system']['postdenywords'], true);
			$matchwords2 = $checker->deny_words($_POST['chaptercontent'], $jieqiConfigs['system']['postdenywords'], true);
			if(is_array($matchwords1) || is_array($matchwords2)){
				if(!isset($jieqiLang['system']['post'])) jieqi_loadlang('post', 'system');
				$matchwords=array();
				if(is_array($matchwords1)) $matchwords = array_merge($matchwords, $matchwords1);
				if(is_array($matchwords2)) $matchwords = array_merge($matchwords, $matchwords2);
				$errtext .= sprintf($jieqiLang['system']['post_words_deny'], implode(' ', jieqi_funtoarray('htmlspecialchars',$matchwords)));
			}
		}

		//检查附件
		$attachary=array();
		$infoary=array();
		$attachnum=0;
		$attachinfo='';
		//检查上传文件
		if($canupload && is_numeric($jieqiConfigs['article']['maxattachnum']) && $jieqiConfigs['article']['maxattachnum']>0){
			$maxfilenum=intval($jieqiConfigs['article']['maxattachnum']);
			$typeary=explode(' ',trim($jieqiConfigs['article']['attachtype']));
			foreach($typeary as $k=>$v){
				if(substr($v,0,1) == '.') $typeary[$k]=substr($typeary[$k],1);
			}
			foreach($_FILES['attachfile']['name'] as $k=>$v){
				if(!empty($v)){
					$tmpary=explode('.', $v);
					$tmpint=count($tmpary)-1;
					$tmpary[$tmpint]=strtolower(trim($tmpary[$tmpint]));
					$denyary=array('htm', 'html', 'shtml', 'php', 'asp', 'aspx', 'jsp', 'pl', 'cgi');
					if(empty($tmpary[$tmpint]) || !in_array($tmpary[$tmpint], $typeary)){
						$errtext .= sprintf($jieqiLang['article']['upload_filetype_error'], $v).'<br />';
					}elseif(in_array($tmpary[$tmpint], $denyary)){
						$errtext .= sprintf($jieqiLang['article']['upload_filetype_limit'], $tmpary[$tmpint]).'<br />';
					}
					if(eregi("\.(gif|jpg|jpeg|png|bmp)$",$v)){
						$fclass='image';
						if($_FILES['attachfile']['size'][$k] > (intval($jieqiConfigs['article']['maximagesize']) * 1024)) $errtext .=sprintf($jieqiLang['article']['upload_filesize_toolarge'], $v, intval($jieqiConfigs['article']['maximagesize'])).'<br />';
					}else{
						$fclass='file';
						if($_FILES['attachfile']['size'][$k] > (intval($jieqiConfigs['article']['maxfilesize']) * 1024)) $errtext .= sprintf($jieqiLang['article']['upload_filesize_toolarge'], $v, intval($jieqiConfigs['article']['maxfilesize'])).'<br />';
					}
					if(!empty($errtext)){
						jieqi_delfile($_FILES['attachfile']['tmp_name'][$k]);
					}else{
						$attachary[$attachnum]=$k;
						$infoary[$attachnum]=array('name'=>$v, 'class'=>$fclass, 'postfix'=>$tmpary[$tmpint], 'size'=>$_FILES['attachfile']['size'][$k]);
						$attachnum++;
					}
				}
			}
		}

		//有附件的话允许章节没内容，否则必须有
		if ($attachnum == 0 && strlen($_POST['chaptercontent']) == 0) $jieqiLang['article']['need_chapter_content'].'<br />';

		if(empty($errtext)) {
			//附件入库
			if($attachnum>0){
				include_once($jieqiModules['article']['path'].'/class/articleattachs.php');
				$attachs_handler =& JieqiArticleattachsHandler::getInstance('JieqiArticleattachsHandler');
				foreach($attachary as $k=>$v){
					$newAttach = $attachs_handler->create();
					$newAttach->setVar('articleid', $_REQUEST['aid']);
					$newAttach->setVar('chapterid', 0);
					$newAttach->setVar('name', $infoary[$k]['name']);
					$newAttach->setVar('class', $infoary[$k]['class']);
					$newAttach->setVar('postfix', $infoary[$k]['postfix']);
					$newAttach->setVar('size', $infoary[$k]['size']);
					$newAttach->setVar('hits', 0);
					$newAttach->setVar('needexp', 0);
					$newAttach->setVar('uptime', JIEQI_NOW_TIME);
					if($attachs_handler->insert($newAttach)){
						$attachid=$newAttach->getVar('attachid');
						$infoary[$k]['attachid']=$attachid;
					}else{
						$infoary[$k]['attachid']=0;
					}
				}
				$attachinfo=serialize($infoary);
			}

			//文字过滤
			if(!empty($jieqiConfigs['article']['hidearticlewords'])){
				$articlewordssplit = (strlen($jieqiConfigs['article']['articlewordssplit'])==0) ? ' ' : $jieqiConfigs['article']['articlewordssplit'];
				$filterary=explode($articlewordssplit, $jieqiConfigs['article']['hidearticlewords']);
				$_POST['chaptercontent']=str_replace($filterary, '', $_POST['chaptercontent']);
			}
			//内容排版
			if($jieqiConfigs['article']['authtypeset']==2 || ($jieqiConfigs['article']['authtypeset']==1 && $_POST['typeset']==1)){
				include_once(JIEQI_ROOT_PATH.'/lib/text/texttypeset.php');
				$texttypeset=new TextTypeset();
				$_POST['chaptercontent']=$texttypeset->doTypeset($_POST['chaptercontent']);
			}
			//增加章节
			$from_draft=false;
			include_once($jieqiModules['article']['path'].'/include/addchapter.php');
		}else{
			jieqi_printfail($errtext);
		}
		break;
	case 'chapter':
	default:
		//包含区块参数(定制区块)
		jieqi_getconfigs('article', 'authorblocks', 'jieqiBlocks');
		include_once(JIEQI_ROOT_PATH.'/header.php');
		$jieqiTpl->assign('article_static_url',$article_static_url);
		$jieqiTpl->assign('article_dynamic_url',$article_dynamic_url);
		$jieqiTpl->assign('url_newchapter',$article_static_url.'/newchapter.php?do=submit');
		
		$jieqiTpl->assign('articleid',$_REQUEST['aid']);
		$jieqiTpl->assign('articlename',$article->getVar('articlename'));
		
		$volumerows = array();
		$volumeid=$article->getVar('chapters')+1;
		include_once($jieqiModules['article']['path'].'/class/chapter.php');
		$chapter_handler=& JieqiChapterHandler::getInstance('JieqiChapterHandler');
		$criteria = new CriteriaCompo(new Criteria('articleid', $_REQUEST['aid']));
		$criteria->setSort('chapterorder');
		$criteria->setOrder('DESC');
		$chapter_handler->queryObjects($criteria);
		//显示的分卷序号是本分卷最后章节＋1
		$tmpvar=$volumeid;
		$k=0;
		while($v = $chapter_handler->getObject()){
			if($v->getVar('chaptertype')==1){
				$volumerows[]=array('volumeid'=>$tmpvar, 'volumename'=>$v->getVar('chaptername'));
				$tmpvar=$volumeid-$k-1;
			}
			$k++;
		}
		$jieqiTpl->assign_by_ref('volumeid', $volumeid);
		$jieqiTpl->assign_by_ref('volumerows', $volumerows);
		
		
		
		$chaptername='';
		$chaptercontent='';
		$userchappid=0;
		if(!empty($_REQUEST['draftid'])){
			include_once($jieqiModules['article']['path'].'/class/draft.php');
			$draft_handler =& JieqiDraftHandler::getInstance('JieqiDraftHandler');
			$draft=$draft_handler->get($_REQUEST['draftid']);
			if(is_object($draft)){
				$chaptername=$draft->getVar('draftname', 'e');
				$chaptercontent=$draft->getVar('content', 'e');
			}
		}elseif(!empty($_REQUEST['userchapid'])){
			include_once($jieqiModules['article']['path'].'/class/userchap.php');
			$userchap_handler =& JieqiUserchapHandler::getInstance('JieqiUserchapHandler');
			$userchap=$userchap_handler->get($_REQUEST['userchapid']);
			if(is_object($userchap)){
				$chaptername=$userchap->getVar('chaptername', 'e');
				$chaptercontent=$userchap->getVar('content', 'e');
				$userchappid=$userchap->getVar('posterid');
			}
		}elseif(!empty($_REQUEST['ochapterid'])){
			include_once($jieqiModules['obook']['path'].'/class/ochapter.php');
			$chapter_handler =& JieqiOchapterHandler::getInstance('JieqiOchapterHandler');
			$ochapter=$chapter_handler->get($_REQUEST['ochapterid']);
			if(is_object($ochapter)){
				$obookid=$ochapter->getVar('obookid', 'n');
				include_once($jieqiModules['obook']['path'].'/class/obook.php');
				$obook_handler =& JieqiObookHandler::getInstance('JieqiObookHandler');
				$obook=$obook_handler->get($obookid);
				if(is_object($obook)){
					//检查权限
					jieqi_getconfigs('obook', 'power');
					$canedit=jieqi_checkpower($jieqiPower['obook']['delallobook'], $jieqiUsersStatus, $jieqiUsersGroup, true);
					if(!$canedit && !empty($_SESSION['jieqiUserId'])){
						//除了斑竹，作者、发表者和代理人可以删除电子书
						$tmpvar=$_SESSION['jieqiUserId'];
						if($tmpvar>0 && ($obook->getVar('authorid')==$tmpvar || $chapter->getVar('posterid')==$tmpvar || $obook->getVar('agentid')==$tmpvar)){
							$canedit=true;
						}
					}

					if($canedit){
						$chaptername=$ochapter->getVar('chaptername', 'e');
						$userchappid=$ochapter->getVar('posterid');

						include_once($jieqiModules['obook']['path'].'/class/ocontent.php');
						$content_handler =& JieqiOcontentHandler::getInstance('JieqiOcontentHandler');
						$criteria=new CriteriaCompo(new Criteria('ochapterid', $_REQUEST['ochapterid']));
						$criteria->setLimit(1);
						$content_handler->queryObjects($criteria);
						unset($criteria);
						$content=$content_handler->getObject();
						if(is_object($content)) $chaptercontent=$content->getVar('ocontent', 'e');
					}
				}
			}
		}

		$jieqiTpl->assign_by_ref('chaptername',$chaptername);
		$jieqiTpl->assign_by_ref('chaptercontent',$chaptercontent);
		$jieqiTpl->assign_by_ref('userchappid',$userchappid);
		$jieqiTpl->assign('authtypeset',$jieqiConfigs['article']['authtypeset']);
		$jieqiTpl->assign('canupload',$canupload);
		if($canupload && is_numeric($jieqiConfigs['article']['maxattachnum']) && $jieqiConfigs['article']['maxattachnum']>0) $maxattachnum = intval($jieqiConfigs['article']['maxattachnum']);
		else $maxattachnum = 0;
		$jieqiTpl->assign('maxattachnum',$maxattachnum);
		$jieqiTpl->assign('attachtype',$jieqiConfigs['article']['attachtype']);
		$jieqiTpl->assign('maximagesize',$jieqiConfigs['article']['maximagesize']);
		$jieqiTpl->assign('maxfilesize',$jieqiConfigs['article']['maxfilesize']);
		

		$jieqiTpl->assign('authorarea', 1);
		$jieqiTpl->setCaching(0);
		$jieqiTset['jieqi_contents_template'] = $jieqiModules['article']['path'].'/templates/newchapter.html';
		include_once(JIEQI_ROOT_PATH.'/footer.php');
		break;
}


?>