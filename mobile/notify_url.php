<?php
/**
 * 文件名称：notify_url.php
 * ----------------------------------------------------------------------------
 * 手机支付宝支付异步通知处理
 * ----------------------------------------------------------------------------
 */

/* 访问控制 */
define('IN_ECTOUCH', true);
if(!isset($_POST['sign'])){
	header('location: ./index.php?'.$_SERVER['QUERY_STRING']);
  exit;
}
define('CONTROLLER_NAME', 'Respond');

$params['type'] = 1;
$params['code'] = 'alipay_wap';
$code = base64_encode(serialize($params));
$code = str_replace(array('+', '/', '='), array('-', '_', ''), $code);
$_GET['code'] = $code;
/* 加载核心文件 */
require ('include/EcTouch.php');

