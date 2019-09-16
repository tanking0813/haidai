<?php
error_reporting(0);
ini_set("display_errors", "Off");
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。


ini_set('memory_limit','800M');    // 临时设置最大内存占用为3G
header('Cache-Control:no-cache,must-revalidate');   
header('Pragma:no-cache');   
header("Expires:0"); 
 
require_once dirname(__FILE__) . '/../init.php';

use App\Domain\CategoryGlobal as DomainCategory;//分类
use App\Domain\Goods as DomainGoods;//引入商品
use App\Domain\Suppliers\GlobalShop as GlobalShopApi;//引入全球购类
use App\Domain\Brand as DomainBrand;//引入品牌
$t1 = microtime(true);
// //  同步品牌名称
$GlobalShopApi = new GlobalShopApi();  
$type = (int)$_GET['type'];  // 1品牌  2分类  3 商品列表  4下单  5取消订单
//品牌接口
if($type == 1){
	$Table = new DomainBrand();
	$brandlist = $Table->getAllBrand();
	$AllBrand = array_column($brandlist['list'], null, 'brand_name'); //数据整合
	$return_res = $GlobalShopApi->brandGlobal(); //品牌接口  api
	if(!empty($return_res)){
		$sql = "INSERT INTO `ns_goods_brand` ( `brand_name`, `brand_logo`, `old_id`, `supplier_id`) VALUES";
		$count = count($return_res);
		$tmp = "";
		for($i=0;$i<$count;$i++){
		    if(empty($AllBrand[$return_res[$i]['brandName']])){
		    	$tmp .= '(
			            "'.$return_res[$i]['brandName'].'",                                    
			            "'.$return_res[$i]['brandLogo'].'",                                                 
			            "'.$return_res[$i]['brandId'].'",  
			            "4"),';	
		    }else{
		        unset($return_res[$i]);
		    }
		}
	}
}

//分类接口
if($type == 2){
	$Table = new  DomainCategory();
	$categorylist = $Table->getAllCategory();
	$categorylist = array_column($categorylist['list'], null, 'category_name');//数据整合
	$new_return_res = $GlobalShopApi->categoryList(); //分类接口  api
	if(!empty($new_return_res)){
		$sql = "INSERT INTO `ns_goods_category_global` ( `category_id`, `category_name`, `pid`, `level`) VALUES";
		$count = count($new_return_res);
		$tmp = "";
		for($i=0;$i<$count;$i++){
		    if(empty($categorylist[$new_return_res[$i]['category_name']])){
		    	$tmp .= '(
			            "'.$new_return_res[$i]['category_id'].'",                                    
			            "'.$new_return_res[$i]['category_name'].'",                                                 
			            "'.$new_return_res[$i]['pid'].'",  
			            "'.$new_return_res[$i]['level'].'"),';	
		    }else{
		        unset($new_return_res[$i]);
		    }
		}
	}
}

// 商品列表分页查询接口
if($type == 3){
	$BrandTable = new DomainBrand();
	$Table = new DomainGoods();//实例化domain层的对象
	$getgoodsALL = $GlobalShopApi->getGoodsAll(); // 获取全球购商品  api
	$getGlobal = $Table->getAllXy(4);  // 获取全球购商品   数据库
	$getGlobal = array_column($getGlobal, null, 'sku_no'); //数据整合
	$cat_goods_all  = array_reduce($getgoodsALL, function ($result, $value) {
                    return array_merge($result, array_values($value));
                }, array()); //全球购商品数据整合  api
	$new_return_res =  array_column($GlobalShopApi->categoryList(), null, 'category_name'); ;  //商品分类  api
	$brandGlobal =  array_column($BrandTable->getGlobalBrand(4), null, 'brand_name'); ; //品牌数据  数据库
	if(!empty($cat_goods_all)){
		$sql = "INSERT INTO `ns_goods` ( `goods_no`, `category_id`, `status`, `title`, `content`, `stock`,  `sku_no`, `supplier_id`,`sale_type`, `delivery_type`, `create_time`,`bar_code`,`main_picture`,`product_price`,`retail_price`,`tax_rate`,`brand_id`, `params`,`delivery_city`) VALUES";
		$count = count($cat_goods_all);
		$tmp = "";
		for($i=0;$i<$count;$i++){
		    if(empty($getGlobal[$cat_goods_all[$i]['itemId']])){
		    	$categoryName =	$new_return_res[$cat_goods_all[$i]['categoryName']]['category_id']; //商品分类转换
		    	$goodsDetail =  $GlobalShopApi->goodsDetail($cat_goods_all[$i]['itemId']);
		    	$brandid =  $brandGlobal[$goodsDetail['brandName']]['brand_id'];  //全球品牌id
		    	$tmp .= '(
			            "'.$cat_goods_all[$i]['itemId'].'",                                    
			            "'.$categoryName.'",
			            "1",
			            "'.str_replace('"', "'", $cat_goods_all[$i]['skuName']).'", 
			            "'.str_replace('"', "'", json_encode($goodsDetail['detailImages'])).'",
			            "'.$cat_goods_all[$i]['stockQuantity'].'",
			            "'.$cat_goods_all[$i]['itemId'].'",                                 
			            "4",                                                  
			            "2", 
			            "'.$goodsDetail['supplyType'].'", 
			            "'.time().'", 
			            "'.$goodsDetail['barCode'].'",
			            "'.str_replace('"', "'", json_encode($goodsDetail['mainImages'])).'",
			            "'.$goodsDetail['salePrice']/100 .'",
			            "'.$goodsDetail['gpSalePrice']/100 .'",
			            "'.$goodsDetail['tax'].'",
			            "'.$brandid.'",
			            "'.$goodsDetail['attr'].'",
			            "'.$cat_goods_all[$i]['deliverCity'].'"),';	
			    unset($goodsDetail);
			    unset($cat_goods_all[$i]);
		    }else{
		        unset($cat_goods_all[$i]);
		    }
		}
	}
}

if($type == 4){
	//下单接口
	var_export($GlobalShopApi->creationOrder());die;
}

if($type == 5){
	//取消订单
	var_export($GlobalShopApi->cancellationOrder('OC21909111555338445'));die;
}

if($type ==6){
	var_export($GlobalShopApi->requestPay());die;
}

if($type ==7){
	file_put_contents('huidiao.txt', var_export( file_get_contents("php://input") ));die;
}
if(!empty($tmp)){
     $tmp = rtrim($tmp,',');
     $tmp .= ";\n";
     $sql .= $tmp;
     $res =  $Table->insertData($sql);
}

$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒<br>';






