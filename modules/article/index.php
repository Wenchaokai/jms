<?php 
/**
 * 本模块首页
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    article
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: index.php 339 2009-06-23 03:03:24Z juny $
 */

define('JIEQI_MODULE_NAME', 'article');  //定义本页面所属模块
require_once('../../global.php');  //包含公共文件

jieqi_getconfigs(JIEQI_MODULE_NAME, 'indexblocks', 'jieqiBlocks'); //包含区块参数
include_once(JIEQI_ROOT_PATH.'/header.php'); //包含页头
$jieqiTset['jieqi_contents_template'] = '';  //内容位置不赋值，全部用区块
include_once(JIEQI_ROOT_PATH.'/footer.php'); //包含页尾
?>