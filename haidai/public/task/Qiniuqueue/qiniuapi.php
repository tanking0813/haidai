<?php
require_once __DIR__ . '/php-sdk/autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
class uploadImage {

	public $access_key;
	public $secret_key;
	public $bucket;
        public $token;
	public $domain;
	public function __construct(){}
	/*
	* @ 上传远端图片
	* @ 2017/10/09
	* @ 雨醉风尘
	* */
	public function getAuthToken(){
		$auth  = new Auth($this->access_key, $this->secret_key); 
		$this->token = $auth->uploadToken($this->bucket);
	}
	public function uploadImg($imgUrl,$imgname)
	{
		$imageData = self::getImgData($imgUrl);
		/*
		$auth  = new Auth($this->access_key, $this->secret_key); 
		$token = $auth->uploadToken($this->bucket);
		 */
		$key   = 'imgUrl_'.$imgname.'.jpg';
		$up    = new UploadManager();
		$mime  = 'image/jpeg';

		list($rest, $err) = $up->put($this->token, $key, $imageData, null, $mime);
		if ($err) {
			//var_dump($err);
			return false;
		} else {
			$uploadUrl = $this->domain.$rest['key'];
			//var_dump($uploadUrl);
			//exit;
			return $uploadUrl;
			#echo "<img src=$uploadUrl>";
		}
	}

	protected function getImgData($imgUrl)
	{
		$ch = curl_init($imgUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		$rawData = curl_exec($ch);
		curl_close($ch);
		return $rawData;
	}
}
