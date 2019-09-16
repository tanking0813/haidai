<?php
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。
$t1 = microtime(true);
ini_set('memory_limit','800M');    // 临时设置最大内存占用为3G
header('Cache-Control:no-cache,must-revalidate');   
header('Pragma:no-cache');   
header("Expires:0"); 
require_once dirname(__FILE__) . '/../init.php';
use App\Domain\Orders as DomainOrders;//引入domain层
use App\Domain\OrderNotify as DomainOrderNotify;//引入domain层
use App\Domain\Goods as DomainGoods;//引入domain层
use App\Domain\GoodsSync as DomainGoodsSync;//引入domain层
use App\Domain\Brand as DomainBrand;//引入domain层
use App\Domain\Category as DomainCategory;//引入domain层
use App\Domain\Suppliers\Weini as WeiniApi;//引入domain层
use App\Domain\AppInfo as DomaiAppInfo;//引入domain层
use PhalApi\Logger;
use PhalApi\Logger\FileLogger;
use App\Model\GoodsSync as ModelGoodsSync;//引入domain层
use App\Model\Goods as ModelGoods;//引入domain层
#use \Handlediffgood\GoodsHandle;
#$gh = new GoodsHandle();
#echo "test self class ";
#exit;
$domain = new DomainGoods();//实例化商品domain层的对象
$list = $domain->getAllskuNo();//获取全部商品编码
$old_ids = array_flip($list);

$goodsqueuename = "goodsqueue_".uniqid();
#var_dump($goodsqueuename);
#exit;
$redis = new Redis();
$redis->pconnect("127.0.0.1",6379);

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
$inputgoods = getAllGoods();
$inputgoodsV2K = array_flip($inputgoods);
$updatadiffgoods = function() use ($old_ids,$inputgoodsV2K,$logger){
	$mg = new ModelGoods();
	//获取已下架的商品
	$downgoods = $mg->getAllskuNo(0);
        foreach ($downgoods as $k => $v) {
            $data[$v['sku_no']] = $v['goods_id'];
	}
	$sql = "INSERT INTO `ns_goods` ( `sku_no`) VALUES";
	$tmpstr = "";
	foreach($inputgoodsV2K as $k=>$v){
		if(array_key_exists($k,$old_ids)){
			continue;
		}
		if(array_key_exists($k,$data)){
		}else{
			$tmpstr.="('".tostring($k)."'),";
		}
	}
	if(!empty($tmpstr)){
		$tmpstr = rtrim($tmpstr, ',');
		$tmpstr .= ";\n";
		$sql .= $tmpstr; 
		$rs = $mg->executeSql($sql);
		var_dump($rs);
		if(!$rs){
			$logger->error('insert diff goods data fail to insert DB',"");
			exit;
		}
	}else{
		echo "no data add goods 1";
		#$logger->error('no data add goods',"");
	
	}
};
$updatadiffgoods();
#exit;
#重新获取需要更新的商品
$list = $domain->getAllskuNo();//获取全部商品编码
$old_ids = array_flip($list);

$logger = new FileLogger(API_ROOT . '/runtime', Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);
$insertnum = 1000;
$all_goods_sku_no = array_chunk($list, $insertnum); 
$updateGoods = function() use ($old_ids,$logger,$redis,$goodsqueuename){
	$mgs = new ModelGoodsSync();
	$mg = new ModelGoods();
	$updatadowngoods =""; 
	$countnum = 0;
	foreach($old_ids as $v){
		$result = $mgs->getOneData($v);
		if(!$result){
			$data["status"] = 0;
			$isupdata = $mg->updateData($data,$v);
			continue;
		}
		$result["gid"] = $v;
		$redis->lpush($goodsqueuename,json_encode($result));
		unset($result["gid"]);
		if(isset($result["img"])){
			$content_pic = explode(";", $result['img']);
			unset($result["img"]);
			$content = "";
			foreach ($content_pic as $img) {
				$content .= "<p>< img src={$img} width=\"100%\"/></p >";
			}
		}
		if(isset($result["content"])){
			$result["content"].=$content;
		}else{
			$result["content"] = $content;	
		}
		$result["sort"] = $v;
		$result["tax"] = $result["tax_rate"] * $result['cost_price'];
		$isupdata = $mg->updateData($result,$v);
		if(!$isupdata){
		}
		$countnum+=1;
		if($countnum==500){
			//没500次跟新休眠50毫秒
			usleep(50000);	
			$countnum = 0;
		}
	}
	$redis->lpush($goodsqueuename,"over");
};
$WeiniApi = new WeiniApi();
$domain_goodssync = new DomainGoodsSync();//实例化domain层的对象
if($domain_goodssync->get(1)){
	$sql = "TRUNCATE TABLE `ns_goods_sync`";                                    //
	$deletesuccess= $domain_goodssync->insertData($sql);
	if($deletesuccess!==0){
		$logger->error('drop tables ns_goods_sync fail!', "");
		exit();
	}
}
function tostring ($str){
	return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $str);
}
if (is_array($all_goods_sku_no) ) {
    foreach($all_goods_sku_no as $lists){
        $goods_list = $WeiniApi->SkuSynchro($lists);
$sql = "INSERT INTO `ns_goods_sync` ( `SkuNo`, `SkuName`, `BarCode`, `SettlePrice`, `RetailPrice`, `Brand`, `Country`, `Category`, `TwoCategory`, `ThreeCategory`, `Details`, `Rate`, `DeliveryCode`, `DeliveryCity`, `SaleType`, `Weight`, `detailImgUrls`, `displayImgUrls`, `goods_id`, `goodsNo`) VALUES";
$tmp = "";
for($i=0;$i<count($goods_list);$i++){
	if(!isset($goods_list[$i]['Country'])){
		$goods_list[$i]['Country'] = "";	
	}
$tmp.= "(
'".tostring($goods_list[$i]['SkuNo'])."',                                    
'".tostring($goods_list[$i]['SkuName'])."',                                  
'".tostring($goods_list[$i]['BarCode'])."',
'".tostring($goods_list[$i]['SettlePrice'])."',                                                           
'".tostring($goods_list[$i]['RetailPrice'])."',                                                           
'".tostring($goods_list[$i]['Brand'])."',
'".$goods_list[$i]['Country']."', 
'".$goods_list[$i]['Category']."',
'".$goods_list[$i]['TwoCategory']."',                                                               
'".$goods_list[$i]['ThreeCategory']."',                                                             
'".tostring($goods_list[$i]['Details'])."', 
'".$goods_list[$i]['Rate']."', 
'".$goods_list[$i]['DeliveryCode']."', 
'".$goods_list[$i]['DeliveryCity']."',                                                        
'".$goods_list[$i]['SaleType']."',                                                                 
'".$goods_list[$i]['Weight']."',
'".$goods_list[$i]['detailImgUrls']."',
'".str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $goods_list[$i]['displayImgUrls'])."',
'".$old_ids[$goods_list[$i]['SkuNo']]."',
'".$goods_list[$i]['goodsNo']."'),";
}
$tmp = rtrim($tmp, ',');
$tmp .= ";\n";
$sql .= $tmp; 
$rs= $domain_goodssync->insertData($sql);
if(!$rs){
	$logger->error('insert data fail to insert DB',"");
	exit;
}
unset($tmp);
    }
}  
$logger->error('insert data success to insert DB',"");
$updateGoods();

$processqueue ="php ".__DIR__."/Qiniuqueue/phpcliqueue.php ".$goodsqueuename ."> /dev/null &"; 
var_dump($processqueue);
exec($processqueue,$ouput,$return);
#var_dump($return);
#exit;
