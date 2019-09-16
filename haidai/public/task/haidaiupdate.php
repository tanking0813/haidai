<?php
error_reporting(1);
ini_set("display_errors", "On");
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。
ini_set("xdebug.overload_var_dump", "0");

ini_set('memory_limit','800M');    // 临时设置最大内存占用为3G
header('Cache-Control:no-cache,must-revalidate');   
header('Pragma:no-cache');   
header("Expires:0"); 
 
require_once dirname(__FILE__) . '/../init.php';

use App\Domain\Category as DomainCategory;//分类
use App\Domain\Goods as DomainGoods;//引入商品
use App\Domain\Suppliers\Haidai\Goods as HaidaiGoods;
use App\Domain\Brand as DomainBrand;//引入品牌
use App\Model\CategoryHaidai as CategoryHaidai;//引入model层

$t1 = microtime(true);
$hdgoods = new HaidaiGoods();
$hdcategory = new CategoryHaidai();
/*$g = $hdgoods->getGoodsInfo("bf1e307a90e64c2197e7fe262ff999ac");
var_dump($g);
exit;*/
$type = $_GET["type"];
//update haidai category info
if($type == 1){
	$ret= $hdgoods->getCategory("0");
	$arr = json_decode($ret,true);
	$sql = "INSERT INTO `ns_goods_category` ( `category_id`, `category_name`, `pid`, `category_pic`,`level`,`haidai_id`) VALUES";
	$tmp = "";
	function loopcategory($pid=0,$level=0,$oldCatId=null,$arrfor=null){
		global $hdgoods;
		global $tmp;
		$level +=1;
		if($arrfor!=null){
			$arr["data"]["result"] = $arrfor;
		}else{
			$ret= $hdgoods->getCategory($pid);
			$arr = json_decode($ret,true);
		}
		if($pid=="110201"||$pid=="110202"||$pid=="110203"){
			return;
		}
		if(count($arr["data"]["result"])!=0){
			foreach ($arr["data"]["result"] as $key => $value) {
				$ncid = $value['oldCatId'] + rand(10000,99999);
				$tmp .= '(
	            "'.$ncid.'",                                    
	            "'.$value['name'].'",                                                 
	            "'.$oldCatId.'",
	            "'.$value['image'].'",
	            "'.$level.'",
	        	"'.$value['id'].'"),';
				loopcategory($value["id"],$level,$ncid);
			}
		}
		return ;
	}
	loopcategory(0,0);	
}

//update haiai goods info
if($type == 2){
	$goods = $hdgoods->getGoodsList();
	$arr = json_decode($goods,true);
	/*$brandidarr = [];
	$brandarr = [];*/
	$totalPageCount=$arr["data"]["totalPageCount"];
	$sql = "INSERT INTO `ns_goods` ( `goods_no`, `category_id`, `status`, `title`, `content`, `stock`,  `sku_no`, `supplier_id`,`sale_type`, `create_time`,`main_picture`,`product_price`,`settle_price`,`tax_rate`,`haidai_id`) VALUES";
	for ($i=1;$i<=$totalPageCount;$i++){
		$goods = $hdgoods->getGoodsList($i);
		$arr = json_decode($goods,true);
		foreach ($arr["data"]["result"] as $key => $value) {
			/*if(!in_array($value["brandId"], $brandidarr)){
				array_push($brandidarr, $value["brandId"]);
				$arr["haidai_id"] = $value["brandId"];
				$arr["brand_name"] = $value["brandName"];
				array_push($brandarr, $arr);
			}*/
			$cids = $hdcategory->getCategoryIdByHaidaiId($value["catId"]);
			if (count($value["specList"])==0){
				$tmp .= '(
				            "'.$value['sn'].'",                                    
				            "'.$cids["category_id"].'",
				            "1",
				            "'.str_replace('"', "'", $value['name']).'", 
				            "",
				            "'.$value['enableStore'].'",
				            "'.$value['sn'].'",                                 
				            "1",                                                  
				            "'.$value["tradeType"]+2.'",  
				            "'.time().'",
				            "'.str_replace('"', "'", json_encode($value['big'])).'",
				            "'.$value['originalPrice'].'",
				            "'.$value['price'].'",
				            "'.$value['tax'].'",
				            "'.$value['goodsId'].'"),';
			}else{
				foreach ($value["specList"] as $k => $v) {
					$tmp .= '(
				            "'.$value['sn'].'",                                    
				            "'.$cids["category_id"].'",
				            "1",
				            "'.str_replace('"', "'", $value['name']).'", 
				            "",
				            "'.$value['enableStore'].'",
				            "'.$value['sn']."*".$v["num"].'",                                 
				            "1",                                                  
				            "'.$value["tradeType"]+2.'", 
				            "'.time().'", 
				            "'.str_replace('"', "'", json_encode($value['big'])).'",
				            "'.$value['originalPrice'].'",
				            "'.$v['price'].'",
				            "'.$value['tax'].'",
				            "'.$value['goodsId'].'"),';
				}
				
			}
		}
	}	
}

if(!empty($tmp)){
    $tmp = rtrim($tmp,',');
    $tmp .= ";\n";
    $sql .= $tmp;
    echo $hdcategory->executeSql($sql);
}