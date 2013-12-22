<?php
/**
 * ��ǩ������غ���
 *
 * ��ǩ������غ���
 * 
 * ����ģ�壺��
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: funtag.php 286 2008-12-23 03:04:17Z juny $
 */

/**
 * ����������ַ��������ع��˺�ı�ǩ����
 * 
 * @param      mixed      $tags Ĭ�ϱ�ǩ��������ַ���
 * @param      mixed      $split �ַ����ķָ��������������ӳ���ϵ
 * @access     public
 * @return     array
 */
function jieqi_tag_clean($tags, $split = ' '){
	$tagary = array();
	if(is_array($tags)){
		foreach($tags as $v){
			$v = trim($v);
			if(is_array($split)) $v = isset($split[$v]) ? $split[$v] : '';
			if(strlen($v) > 0 && !in_array($v, $tagary)) $tagary[] = $v;
		}
	}else{
		$tmpary = explode($split, $tags);
		foreach($tmpary as $v){
			if(strlen($v) > 0 && !in_array($v, $tagary)) $tagary[] = $v;
		}
	}
	return $tagary;
}

/**
 * �����ǩ��Ϣ
 * 
 * @param      array      $tags ��ǩ����
 * @param      int        $articleid ����ID
 * @param      array      $tables ��ǩ������ݱ� array('tag'=>'', 'taglink'=>'', 'tagcache'=>'')
 * @access     public
 * @return     bool
 */
function jieqi_tag_save($tags, $articleid, $tables){
	global $query;
	if(!is_a($query, 'JieqiQueryHandler')){
		jieqi_includedb();
		$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
	}
	$articleid = intval($articleid);
	foreach($tags as $tag){
		$sql = "SELECT * FROM ".$tables['tag']." WHERE tagname = '".jieqi_dbslashes($tag)."' LIMIT 0,1";
		$upflag = true;
		$query->execute($sql);
		if($row = $query->getRow()){
			$tagid = intval($row['tagid']);
			$sql = "INSERT INTO ".$tables['taglink']." (`tagid` ,`articleid` ,`linktime`)VALUES ('".$tagid."', '".intval($articleid)."', '".intval(JIEQI_NOW_TIME)."');";
			if($query->execute($sql)){
				$sql = "UPDATE ".$tables['tag']." SET linknum = linknum + 1 WHERE tagid = ".$tagid;
				$query->execute($sql);
			}else{
				$upflag = false;
			}
		}else{
			$sql = "INSERT INTO ".$tables['tag']."(`tagid` ,`tagname` ,`addtime` ,`tagsort` ,`userid` ,`username` ,`linknum` ,`views` ,`display` )VALUES (0 , '".jieqi_dbslashes($tag)."', '".intval(JIEQI_NOW_TIME)."', '0', '".jieqi_dbslashes($_SESSION['jieqiUserId'])."', '".jieqi_dbslashes($_SESSION['jieqiUserName'])."', '1', '0', '0');";
			$query->execute($sql);
			$tagid = intval($query->db->getInsertId());
			$sql = "INSERT INTO ".$tables['taglink']." (`tagid` ,`articleid` ,`linktime`)VALUES ('".$tagid."', '".intval($articleid)."', '".intval(JIEQI_NOW_TIME)."');";
			$query->execute($sql);
		}
		if($upflag){
			$sql = "SELECT * FROM ".$tables['tagcache']." WHERE tagid = ".$tagid." LIMIT 0,1";
			$query->execute($sql);
			if($row = $query->getRow()){
				$lids = explode(',', $row['linkids']);
				if(!in_array($articleid, $lids)){
					$lids[] = $articleid;
					$sql = "UPDATE ".$tables['tagcache']." SET linkids = '".jieqi_dbslashes(implode(',', $lids))."' WHERE tagid = ".$tagid;
					$query->execute($sql);
				}
			}else{
				$sql = "INSERT INTO ".$tables['tagcache']."(`tagid` ,`tagname` ,`uptime` ,`linkids`)VALUES ('".$tagid."', '".jieqi_dbslashes($tag)."', '".intval(JIEQI_NOW_TIME)."', '".$articleid."');";
				$query->execute($sql);
			}
		}
	}
	return true;
}

/**
 * ���±�ǩ��Ϣ
 * 
 * @param      array      $oldtags ԭ��ǩ����
 * @param      array      $newtags �±�ǩ����
 * @param      int        $articleid ����ID
 * @param      array      $tables ��ǩ������ݱ� array('tag'=>'', 'taglink'=>'', 'tagcache'=>'')
 * @access     public
 * @return     bool
 */
function jieqi_tag_update($oldtags, $newtags, $articleid, $tables){
	global $query;

	$deltags = array(); //��Ҫɾ���ı�ǩ
	$addtags = array(); //��Ҫ���ӵı�ǩ
	foreach($oldtags as $v){
		if(!in_array($v, $newtags) && !in_array($v, $deltags)) $deltags[] = $v;
	}
	foreach($newtags as $v){
		if(!in_array($v, $oldtags) && !in_array($v, $addtags)) $addtags[] = $v;
	}
	if(!empty($deltags)) jieqi_tag_delete($deltags, $articleid, $tables);
	if(!empty($addtags)) jieqi_tag_save($addtags, $articleid, $tables);
	return true;
}

/**
 * ɾ����ǩ������Ϣ
 * 
 * @param      array      $tags ��ǩ����
 * @param      int        $articleid ����ID
 * @param      array      $tables ��ǩ������ݱ� array('tag'=>'', 'taglink'=>'', 'tagcache'=>'')
 * @access     public
 * @return     bool
 */
function jieqi_tag_delete($tags, $articleid, $tables){
	global $query;
	if(!is_a($query, 'JieqiQueryHandler')){
		jieqi_includedb();
		$query=JieqiQueryHandler::getInstance('JieqiQueryHandler');
	}
	$articleid = intval($articleid);
	foreach($tags as $tag){
		$sql = "SELECT * FROM ".$tables['tag']." WHERE tagname = '".jieqi_dbslashes($tag)."' LIMIT 0,1";
		$query->execute($sql);
		if($row = $query->getRow()){
			$tagid = intval($row['tagid']);
			$sql = "DELETE FROM ".$tables['taglink']." WHERE tagid = ".$tagid." AND articleid = ".$articleid;
			$query->execute($sql);
			if($query->db->getAffectedRows() > 0){
				$uptag = true;
				//���һ��������¼
				if($row['linknum'] <= 1){
					$sql = "SELECT count(*) as cot FROM ".$tables['taglink']." WHERE tagid = ".$tagid;
					$query->execute($sql);
					if($row1 = $query->getRow()){
						if($row1['cot'] == 0){
							$uptag = false;
							$sql = "DELETE FROM ".$tables['tag']." WHERE tagid = ".$tagid;
							$query->execute($sql);
							$sql = "DELETE FROM ".$tables['tagcache']." WHERE tagid = ".$tagid;
							$query->execute($sql);
						}
					}
				}
				if(!$uptag){
					$sql = "UPDATE ".$tables['tag']." SET linknum = linknum - 1 WHERE tagid = ".$tagid;
					$query->execute($sql);
					$sql = "SELECT * FROM ".$tables['tagcache']." WHERE tagid = ".$tagid." LIMIT 0,1";
					$query->execute($sql);
					if($row2 = $query->getRow()){
						$lids = explode(',', $row2['linkids']);
						if(!in_array($articleid, $lids)){
							$lids[] = $articleid;
							$sql = "UPDATE ".$tables['tagcache']." SET linkids = '".jieqi_dbslashes(implode(',', $lids))."' WHERE tagid = ".$tagid;
							$query->execute($sql);
						}
					}
				}
			}
		}
	}
	return true;
}
?>