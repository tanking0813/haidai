<?php
namespace App\Domain\Suppliers\Haidai;

class Goods extends Common {
	function __construct(){

	}
	function getCategory($pid=0){
           $categoryapiname = "/api/v2/goods/getTopCategory";
	   $catearr = [
			"appkey"   =>$this->appkey,
			"timestamp"=>$this->getMillisecond(),
			"accountId"=>$this->accountid,
			"memberId"=>$this->memberid,
			"token"=>$this->token,
			"catId"=>$pid,
			"listShow"=>0,
	   ];
	   return json_encode($this->getApiResult($catearr,$categoryapiname));
	   //var_dump($this->getApiResult($catearr,$categoryapiname));
	}
	function getGoodsList($pageNum=1){
		$goodslistapiname = "/api/v2/goods/searchingGoods";	
		$goodslistarr =[
			"appkey"   =>$this->appkey,
			"timestamp"=>$this->getMillisecond(),
			"accountId"=>$this->accountid,
			"memberId"=>$this->memberid,
			"token"=>$this->token,
			//"catName"=>'',
			//"catId"=>'91a6441067d64c39a17d3bdc44f42e41',
			//"isFreepost"=>'',
			//"direct"=>'',
			//"sort"=>'',
			"pageSize"=>50,
			"pageNum"=>$pageNum,
			//"highPrice"=>'',
			#"brandName"=>"",
			#"brandId"=>"",
			#"tradeType"=>"",
			#"countryId"=>"",
			#"countryName"=>"",
			#"lowPrice"=>"",
			#"tradeTypeName"=>"",
			#"ignoreStock"=>"",
			#"needFacet"=>"",
			#"keyword"=>"",

		];
		return json_encode($this->getApiResult($goodslistarr,$goodslistapiname));
	    //var_dump($this->getApiResult($goodslistarr,$goodslistapiname));
	}
	function getGoodsInfo($gid){
		$goodsinfoapiname = "/api/v2/goods/getGoodsInfo";
		$goodsinfoarr = [
			"appkey"   =>$this->appkey,
			"timestamp"=>$this->getMillisecond(),
			"accountId"=>$this->accountid,
			"memberId"=>$this->memberid,
			"token"=>$this->token,
			#"goodsId"=>"bf1e307a90e64c2197e7fe262ff999ac",
			"goodsId"=>$gid,
			#"needCats"=>"",
			#"needIntro"=>"",
			#"needFocusImages"=>"",
			#"goodsSn"=>"",
			#"needSEO"=>"",
			#"needImages"=>"",
			#"onlyEnableStore"=>"",
			#"needActivity"=>"",
			#"needParams"=>"",
		];	
	   return json_encode($this->getApiResult($goodsinfoarr,$goodsinfoapiname));
	}
	function getGoodsPrice($goods=[]){
		$goodspriceapiname="/api/v2/goods/getGoodsPrice";
		$goodspricearr = [
			"appkey"   =>$this->appkey,
			"timestamp"=>$this->getMillisecond(),
			"accountId"=>$this->accountid,
			"memberId"=>$this->memberid,
			"token"=>$this->token,
			"goodsId"=>"c050b46c3ff6411fb4f7f424b5c86d88",
			#"goodsSn"=>"",
			"num"=>"1",
			"productNum"=>"1",
			"cityId"=>"330200",
			"life"=>"2019-02",

		];
		$retobj = $this->getApiResult($goodspricearr,$goodspriceapiname);
		if($retobj->code){
			return false ;
		}
		if($retobj->data->enableStore===0){
			return false ;
		}
		if($retobj->data->maxPrice!=$retobj->data->minPrice){
			return false ;
		}
		var_dump($retobj->data);
	}
}
