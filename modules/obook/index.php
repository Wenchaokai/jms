<?php 
/**
 * ��ģ����ҳ
 *
 * Ĭ����ʾ�������б�
 * 
 * ����ģ�壺��
 * 
 * @category   jieqicms
 * @package    obook
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: index.php 342 2009-06-23 03:04:10Z juny $
 */

define('JIEQI_MODULE_NAME', 'obook');  //���屾ҳ������ģ��
require_once('../../global.php');  //���������ļ�
jieqi_getconfigs(JIEQI_MODULE_NAME, 'indexblocks', 'jieqiBlocks'); //�����������
include_once(JIEQI_ROOT_PATH.'/header.php'); //����ҳͷ
$jieqiTset['jieqi_contents_template'] = '';  //����λ�ò���ֵ��ȫ��������
include_once(JIEQI_ROOT_PATH.'/footer.php'); //����ҳβ

?>