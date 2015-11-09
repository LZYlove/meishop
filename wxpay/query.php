<?php
define('IN_ECS', true);
include('../includes/cls_json.php');
require_once "lib/WxPay.Api.php";
require_once "example/log.php";


if($_REQUEST){
	$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
	$log = Log::Init($logHandler, 15);
		$json = new JSON;
		$out_trade_no = $_POST['order_sn'];
		$input = new WxPayOrderQuery();
		$input->SetOut_trade_no($out_trade_no);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS"
			&& $result["trade_state"] == "SUCCESS")
		{


			echo json_encode($result["trade_state"]);
			//echo "<script type='text/javascript'>query_result($sign);</script>"; 
		
		}
		else{
			echo json_encode($result["trade_state"]);

		}
	}













?>