<?php
namespace Handlediffgood;
class GoodsHandle {
	function __construct(){
	
	}
	function getAllGoods(){
		$txtfile = "/tmp/newarr.php";
		$file = @fopen($txtfile,'r');
		$content = array();
		if(!$file){
			return 'file open fail';
		}else{
				$i = 0;
				while (!feof($file)){
					$content[$i] = trim(fgets($file),"\0\t\n\r \x0B");
					$i++ ;
					usleep(20);
					/*if($i==2000){
						break;
					}*/
				}
				fclose($file);
				$content = array_filter($content); //数组去空
		}
		return $content; 
	}
}
$gh = new GoodsHandle();
var_dump($gh->getAllGoods());
