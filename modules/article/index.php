<?php 
/**
 * ��ģ����ҳ
 * 
 * ����ģ�壺��
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: index.php 339 2009-06-23 03:03:24Z juny $
 */

define('JIEQI_MODULE_NAME', 'article');  //���屾ҳ������ģ��
require_once('../../global.php');  //���������ļ�

jieqi_getconfigs(JIEQI_MODULE_NAME, 'indexblocks', 'jieqiBlocks'); //�����������
include_once(JIEQI_ROOT_PATH.'/header.php'); //����ҳͷ
$jieqiTset['jieqi_contents_template'] = '';  //����λ�ò���ֵ��ȫ��������
include_once(JIEQI_ROOT_PATH.'/footer.php'); //����ҳβ
?>