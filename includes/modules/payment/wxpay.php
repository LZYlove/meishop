<?php

/**
 * ECSHOP 微信支付插件
 * $Author: shanmao $
 * $Id: wxpay.php 17217 2014-11-22 http://shanmao.me $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

@$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/wxpay.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'wxpay_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'TIANXIAKE Team';

    /* 网址 */
    $modules[$i]['website'] = 'http://wx.qq.com';

    /* 版本号 */
    $modules[$i]['version'] = '0.0.1';

	/* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'wxpay_app_id',           'type' => 'text',   'value' => ''),
        array('name' => 'wxpay_app_secret',       'type' => 'text',   'value' => ''),
        array('name' => 'wxpay_mchid',        'type' => 'text',   'value' => ''),
        array('name' => 'wxpay_key',       'type' => 'text',   'value' => ''),
        array('name' => 'notifyurl',       'type' => 'text',   'value' => ''),
        array('name' => 'successurl',       'type' => 'text',   'value' => '')
    );

    return;
}


class WxPayConf_pub
{
	public $wxpay_app_id;
	public $wxpay_app_secret;
	public $wxpay_mchid;
	public $wxpay_key;
	public $notifyurl='http://121.40.148.177/xm9914/wxpay/demo/notify_url.php';
	public $successurl='http://121.40.148.177/xm9914/mobile/user.php?act=order_info&id=';
	public $curltimeout=30;
	
	function __construct() {
		$payment    = get_payment('wxpay');
		//var_dump($payment);
		if(isset($payment)){
			$this->wxpay_app_id		=       $payment['wxpay_app_id'];
			$this->wxpay_app_secret	=       $payment['wxpay_app_secret'];
			$this->wxpay_mchid	=       $payment['wxpay_mchid'];
			$this->wxpay_key	=       $payment['wxpay_key'];
			$this->notifyurl	=       $payment['notifyurl'];
			$this->successurl	=       $payment['successurl'];
		}
		//var_dump($this->wxpay_app_id);
	}
	
	/*
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	const APPID = 'wx4b56d1cfaa3a5574';  //wx4b56d1cfaa3a5574   //wx7bdd4eebef11c1a5  mp.wx 
	//受理商ID，身份标识
	const MCHID = '10018826';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	const KEY = 'zxsaqwedfcvgthg1247875414771fads';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	const APPSECRET = 'fcc5c94e31c6cd4d588195468d27f96f'; //15e36043bab60d5645b368cc7b9299f9
	
	//=======【JSAPI路径设置】===================================
	//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
	//const JS_API_CALL_URL = 'http://mp.shanmao.me/xm9902/mobile/order.php?act=done';
	
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	const SSLCERT_PATH = 'D:\wnmp\www\xm9914\wxpay/cacert/apiclient_cert.pem';
	const SSLKEY_PATH = 'D:\wnmp\www\xm9914\wxpay/cacert/apiclient_key.pem';
	
	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	const NOTIFY_URL = 'http://121.40.148.177/xm9914/wxpay/demo/notify_url.php';
	//支付成功后跳转网址：
	const ZFSUCCESS_URL = 'http://121.40.148.177/xm9914/mobile/user.php?act=order_info&id=';

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;*/
}

//include_once(ROOT_PATH."wxpay/WxPay.pub.config.php"); //支付信息配置文件。
ini_set('date.timezone','Asia/Shanghai');

require_once ROOT_PATH."wxpay/lib/WxPay.Api.php";
require_once ROOT_PATH."wxpay/example/WxPay.NativePay.php";
require_once ROOT_PATH."wxpay/example/log.php";


/**
 * 类
 */
class wxpay
{
	
	

	function get_code($order)
    {
		$notify = new NativePay();
		$input = new WxPayUnifiedOrder();
		$input->SetBody($order['order_sn']);
		$input->SetAttach($order['order_sn']);
		$input->SetOut_trade_no($order['order_sn']);
		$input->SetTotal_fee(intval($order['order_amount'] * 100));
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag($order['order_sn']);
		$input->SetNotify_url("./wxpay/query.php");
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($order['order_sn']);
		
		$result = $notify->GetPayUrl($input);
		
		$url2 = $result["code_url"];

		$img = './wxpay/example/qrcode.php?data='.urlencode($url2);
		$botton= "<div>您将为您的订单号'".$order['order_sn']."'支付'".$order['order_amount']."'元，扫描下方二维码支付</div><img src='".$img."' style='width:150px;height:150px;'/>";

		return $botton;


	}
   
	
	 /**
	 * 是否支持微信支付
	 * @return bool
	 */
	public function is_show_pay($agent) {
		$ag1  = strstr($agent,"MicroMessenger");
		$ag2 = explode("/",$ag1);
		$ver = floatval($ag2[1]);
		if ( $ver < 5.0 || empty($aid) ){
			return false;
    	}else{
    		return true;
    	}
	}   
	
	
	/**
	* 接受通知处理订单。
	* @param undefined $log_id
	* 20141125
*/
	
	
}


?>