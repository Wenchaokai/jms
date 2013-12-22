<?php 
/**
 * 退出登录
 *
 * 已登录用户退出处理
 * 
 * 调用模板：无
 * 
 * @category   jieqicms
 * @package    system
 * @copyright  Copyright (c) Hangzhou Jieqi Network Technology Co.,Ltd. (http://www.jieqi.com)
 * @author     $Author: juny $
 * @version    $Id: logout.php 344 2009-06-23 03:06:07Z juny $
 */

define('JIEQI_MODULE_NAME', 'system');
define('JIEQI_ADMIN_LOGIN', 1);
require_once('global.php');

if(empty($_REQUEST['jumpurl'])) $_REQUEST['jumpurl']=empty($_REQUEST['forward']) ? JIEQI_URL.'/' : $_REQUEST['forward'];
jieqi_useraction('logout', $_REQUEST);
?>