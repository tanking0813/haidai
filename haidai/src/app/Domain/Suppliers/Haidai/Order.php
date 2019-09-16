<?php
namespace App\Domain\Suppliers\Haidai;

class Order extends Common {
	function __construct(){

	}
	function creataOrder($orderarr){
		$createorderapiname = "/api/v2/order/createOrders";	
		$createorderarr = [
			"appkey"   =>$this->appkey,
			"timestamp"=>$this->getMillisecond(),
			"accountId"=>$this->accountid,
			"memberId"=>$this->memberid,
			"token"=>$this->token,
			"goodsIds"=>$orderarr["goodsIds"],
			//"goodsIds"=>"a36dc2d3d5cd444789faacf113d5ec02",
			"area"=>"110101",
			"address"=>$orderarr["address"],
			//"address"=>"eweeeee",
			"identification"=>"362502199106073246",
			"buyAmount"=>urlencode($orderarr["buyAmount"]),
			//"buyAmount"=>urlencode($orderarr["buyAmount"]"{\"a36dc2d3d5cd444789faacf113d5ec02\":{\"2021-06_1_1\":\"#,#\"}}"),
			
			#"paymentId"=>"",
			"nums"=>$orderarr["nums"],
			"name"=>"taomi",
			"productNums"=>"1",
			"mobile"=>"15122299780",
			#"customOrder"=>"",
			#"remarks"=>"",
		];
		return json_encode($this->getApiResult($createorderarr,$createorderapiname));
	}
	function listOrder(){
		$listorderapiname = "/api/v2/order/orderList";
		$listorderarr = [
			"appkey"   =>$this->appkey,
			"timestamp"=>$this->getMillisecond(),
			"accountId"=>$this->accountid,
			"memberId"=>$this->memberid,
			"token"=>$this->token,
			#"pageSize"=>"",
			#"pageNum"=>"",
			#"mobile"=>"",
			#"abnormal"=>"",
			#"goodsSn"=>"",
			#"hasShipInfo"=>"",
			#"endTime"=>"",
			#"orderSn"=>"",
			#"customOrder"=>"",
			#"status"=>"",
			#"timeType"=>"",
			#"tradeType"=>"",
			#"beginTime"=>"",
			#"receiver"=>"",
		];	
		 var_dump($this->getApiResult($listorderarr,$listorderapiname));
	}
	function toPayForBalanceWithoutCode(){
		$payorderapiname = "/api/v2/finance/toPayForBalanceWithoutCode";	
		$payorderarr = [
			"appkey"   =>$this->appkey,
			"timestamp"=>$this->getMillisecond(),
			"accountId"=>$this->accountid,
			"memberId"=>$this->memberid,
			"token"=>$this->token,
			"orderIds"=>"3bbfa5ee2ce249c482f38bd6b7a0bacf",	
		];
		var_dump($this->getApiResult($payorderarr,$payorderapiname));
	}
	function getOrderKuaidi(){
		$orderkuaidiapiname = "api/v2/order/orderKuaidi";
		$orderkuaidiarr = [];		
		var_dump($this->getApiResult($orderkuaidiarr,$orderkuaidiapiname));
	}

}
