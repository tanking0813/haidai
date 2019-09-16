<?php
namespace App\Domain\Suppliers;//声明命名空间
// use App\Model\Brand as ModelBrand;//引入model层

class Weini
{
	protected $parenter;
	
	protected $key;
	
	// protected $apiUrl = 'http://121.41.84.251:9090';
	protected $apiUrl = 'http://vip.nysochina.com';
	
	protected $port;
	
	protected $customs;
	
	protected $defaultCustoms = '3305967562';
	
	public function __construct()
	{
		// $this->parenter = '218148_7755';
		// $this->key = '021e89780e3011e99b50008cfae57840';
		$this->parenter = '11640_5075';
		$this->key = 'd2455cf025494fe791adb8cb3f09800c';		

		$this->port = array(
			'25' => 'HANGZHOU_ZS',
			'26' => 'CHONGQING',
			'31' => 'NINGBO',
			'50' => 'HANGZHOU_ZS', // 微信方没有福州海关,根据1号仓库技术BillYoung的说法，福州也用杭州的海关标识
			'67' => 'ZHENGZHOU_BS',
			'28' => 'SHANGHAI_ZS'
		);
		$this->customs = array(
			'25' => '330596701M'
		);
	}
	/**
	 * 生成token
	 *
	 * @param $interfaceName
	 * @param $params
	 * @return string
	 */
	public function getToken($interfaceName, $params)
	{
		$str = $this->key . date('Y-m-d', time()) . $interfaceName . $params;
		return strtoupper(md5($str));
	}
	
	/**
	 * HTTP请求
	 *
	 * @param $interfaceName
	 * @param $params
	 * @return mixed|string
	 */
	protected function http($interfaceName, $params, $url)
	{
		$signParams = json_encode($params);
		
		$headers = array(
			'Content-Type:application/json; charset=utf-8',
			// 'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36',
			'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36',
			'interfacename:' . $interfaceName,
			'parenter:' . $this->parenter,
			'token:' . $this->getToken($interfaceName, $signParams)
		);
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			// @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			// curl_setopt($ch, CURLOPT_HEADER, 1);
			@curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($ch, CURLOPT_RESOLVE, ["vip.nysochina.com:80:121.43.36.204"]);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3600000); //超时毫秒，cURL 7.16.2中被加入。从PHP 5.2.3起可使用
			// curl_setopt($ch, CURLOPT_HTTPHEADER, array(''Expect: '')); //头部要送出'Expect: '
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名	


 			// curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 120); 
 			// curl_setopt($curl, CURLOPT_TIMEOUT, 120);



			curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3600000);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3600000);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $signParams);
			$data = curl_exec($ch);
			$code = curl_errno($ch);
			if ($code) {
				return "请求出错,错误信息:" . curl_error($ch) . "错误编码:" . $code;
            }
			curl_close($ch);
			return json_decode($data, true);
		} catch (\Exception $exception) {
			return $exception->getMessage();
		}
	}
	
	/**
	 * 运单同步接口
	 *
	 * @param $orderNoList
	 * @param int $isSearchByChildNo
	 * @return mixed|string
	 */
	public function PostSynchro($orderNoList,  $isSearchByChildNo = 0)
	{
		$data = [
			'OrderNosReqs' => is_array($orderNoList) ? $orderNoList : [$orderNoList]
		];
		if($isSearchByChildNo){
			$data['IsSearchByChildNo'] = 1;
		}
		return $this->http('PostSynchro', $data, '/api/PostSynchro.shtml');
	}
	
	/**
	 * 商品同步接口
	 * 
	 * @param $skuList
	 * @return mixed|string
	 */
	public function SkuSynchro($skuList)
	{
		$data = [
			'SkuReqs' => is_array($skuList) ? $skuList : [$skuList]
		];
		return $this->http('SkuSynchro', $data, '/api/SkuSynchro.shtml');
	}
	
	/**
	 * 库存同步接口
	 *
	 * @param $skuList
	 * @return mixed|string
	 */
	public function StockSynchro($skuList)
	{
		$data = [
			'StockReqs' => is_array($skuList) ? $skuList : [$skuList]
		];
		return $this->http('StockSynchro', $data, '/api/StockSynchro.shtml');
	}
	
	/**
	 * 异步订单新增接口
	 * 
	 * @param $data
	 * @return mixed|string
	 */
	public function AddOrderAsync($data)
	{
		return $this->http('AddOrderAsync', $data, '/api/AddOrderAsync.shtml');
	}
	
	/**
	 * 支付单结果上传接口
	 *
	 * @param $data
	 * @return mixed|string
	 */
	public function PayAsynNotify($data)
	{
		return $this->http('PayAsynNotify', $data, '/api/PayAsynNotify.shtml');
	}
	
	/**
	 * 获取海关标识
	 *
	 * @param $port
	 * @return mixed|string
	 */
	public function getCustomsTag($port)
	{
		return isset($this->port[$port]) ? $this->port[$port] : 'NO';
	}
	
	/**
	 * 获取报关企业代码
	 *
	 * @param $port
	 * @return mixed|string
	 */
	public function getMchCustomsNo($port)
	{
		return isset($this->customs[$port]) ? $this->customs[$port] : $this->defaultCustoms;
	}
	
	/**
	 * 获取微信报关地址
	 *
	 * @param $isRePush
	 * @return string
	 */
	public function getCustomDeclareUrl($isRePush)
	{
		return $isRePush
			? 'https://api.mch.weixin.qq.com/cgi-bin/mch/newcustoms/customdeclareredeclare'
			: 'https://api.mch.weixin.qq.com/cgi-bin/mch/customs/customdeclareorder';
	}

	/*
	 * @purpose: 使用curl并行处理url
	 * @return: array 每个url获取的数据
	 * @param: $urls array url列表
	 * @param: $callback string 需要进行内容处理的回调函数。示例：func(array)
	 */
	public	function get_img_curls($urls = array(), $callback = '')
	{
	    $response = array();
	    if (empty($urls)) {
	        return $response;
	    }
	    $chs = curl_multi_init();
	    $map = array();
	    foreach($urls as $url){
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($ch, CURLOPT_HEADER, 0);
	        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
	        curl_multi_add_handle($chs, $ch);
	        $map[strval($ch)] = $url;
	    }
	    do{
	        if (($status = curl_multi_exec($chs, $active)) != CURLM_CALL_MULTI_PERFORM) {
	            if ($status != CURLM_OK) { break; } //如果没有准备就绪，就再次调用curl_multi_exec
	            while ($done = curl_multi_info_read($chs)) {
	                $info = curl_getinfo($done["handle"], CURLINFO_HTTP_CODE);
	                // $error = curl_error($done["handle"]);
	                // $result = curl_multi_getcontent($done["handle"]);
	                $url = $map[strval($done["handle"])];
	                $rtn = compact('info');
	                if (trim($callback)) {
	                    $callback($rtn);
	                }
	                $response[$url] = $rtn;
	                curl_multi_remove_handle($chs, $done['handle']);
	                curl_close($done['handle']);
	                //如果仍然有未处理完毕的句柄，那么就select
	                if ($active > 0) {
	                    curl_multi_select($chs, 0.5); //此处会导致阻塞大概0.5秒。
	                }
	            }
	        }
	    }
	    while($active > 0); //还有句柄处理还在进行中
	    curl_multi_close($chs);
	    return $response;
	}

	
}