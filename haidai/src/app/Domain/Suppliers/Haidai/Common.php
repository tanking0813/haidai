<?php
namespace App\Domain\Suppliers\Haidai;

class Common {
	public $appkey="93039904";
	public $appsecret="c6c65ec0d6094d8cb34db6b5f9c1ef4c";
	public $apiurl="http://api.pre.seatent.com";
	public $username="13333013935";
	public $password="123456";
	public $accountid="03d2388a3b054022b042b7562ad8fea6";
	public $memberid="03d2388a3b054022b042b7562ad8fea6";
	public $token="abaa1d7ea2524491bf19c2b37a6ef532";
	function __construct(){

	}
	function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
	function Login(){
		$loginapiname = "/ssoapi/v2/login/login";
		$signarr=[
			"appkey"   =>$this->appkey,
			"timestamp"=>self::getMillisecond(),
			"username" =>$this->username,
			"password"=>md5($this->password),

		];
		$signestr = $this->makeSignature($signarr);
		$getloginurl = $this->apiurl.$loginapiname."?".$signestr;
		//var_dump($getloginurl);
		$ret = $this->httpGet($getloginurl);
		$retobj = json_decode($ret);
		if(isset($retobj->data->accountId)&&!empty($retobj->data->accountId))
		{
			$this->accountid = $retobj->data->accountId;
		}
		if(isset($retobj->data->memberId)&&!empty($retobj->data->memberId))
		{
			$this->memberid = $retobj->data->memberId;
		}
		if(isset($retobj->data->token)&&!empty($retobj->data->token))
		{
			$this->token = $retobj->data->token;
		}
		#var_dump($retarr);
		//var_dump($ret);
		return array("status"=>1,"info"=>"success","data"=>$ret);	
	} 
	function getApiResult($postdata,$apiname){
		$signestr = $this->makeSignature($postdata);
		$url = $this->apiurl.$apiname."?".$signestr;
		//var_dump($url);exit;
		$ret = $this->httpGet($url);
		$retobj = json_decode($ret,true);
		return $retobj;	
	}
	public function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($curl, CURLOPT_TIMEOUT, 500 );
		curl_setopt($curl, CURLOPT_URL, $url );
		$res = curl_exec($curl);
		curl_close($curl);
		return $res;
	}
	function makeSignature($args){
		ksort($args);
		#var_dump($args);
		#exit;
		$stringA = '';
		$stringSignTemp = '';
		foreach($args as $k => $v) {
			$stringA .= $k . '=' . $v . '&';
	        }
		$stringA=rtrim($stringA,"&");
		#var_dump($stringA);
		#exit;
		$stringSignTemp =  $this->appsecret.$stringA.$this->appsecret;
		#var_dump($stringSignTemp);
		$signature  = strtoupper(sha1($stringSignTemp));
		#var_dump($signature);
		$newString = $stringA.'&topSign='.$signature;
		#var_dump($newString);
		#exit;
		#$newSign = base64_encode($newString);
		return $newString;
	}
}
