<?php
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。
$t1 = microtime(true);


ini_set('memory_limit','800M');    // 临时设置最大内存占用为3G

// function convert($size){ 
// $unit=array('b','kb','mb','gb','tb','pb'); 
// return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i]; 
// } 
// echo convert(memory_get_usage(true)); 



// exit();
header('Cache-Control:no-cache,must-revalidate');   
header('Pragma:no-cache');   
header("Expires:0"); 



require_once dirname(__FILE__) . '/../init.php';
// use PhalApi\Logger;
// use PhalApi\Logger\FileLogger;
// use PhalApi\CUrl;
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

$domain = new DomainGoods();//实例化商品domain层的对象
$list = $domain->getAllskuNo();//获取全部商品编码
$old_ids = array_flip($list);
$logger = new FileLogger(API_ROOT . '/runtime', Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);
$insertnum = 1000;
$all_goods_sku_no = array_chunk($list, $insertnum); 
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
//var_dump($all_goods_sku_no);
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
	#$sql = 'SELECT name FROM tbl_user WHERE age > :age LIMIT 1';
	#$params = array(':age' => 18);
	#        return $this->getORM()->queryAll($sql, $params);
	#for($i=0;$<10,$i++){
	#	
	#}
        #exit;
        // foreach ($goods_list as  &$data) {
        //     $data["goods_id"] = $old_ids[$data['SkuNo']];
                      
        // }
        // $t2 = microtime(true);
        // echo '耗时'.round($t2-$t1,3).'秒<br>';
        // echo 'Now memory_get_usage: ' . memory_get_usage() . '<br />';        
        // ignore_user_abort(); //终止脚本的执行
        // foreach ($lists as $key => $value) {
        //     echo $value;
        //     echo '<br>';
        // }

#echo '<pre>';
#var_export($goods_list);
#        $rs= $domain_goodssync->insert_multi($goods_list);
#        unset($data);
#        usleep(1);


  
        // echo '第' . $k.'批';
        // $goods_count = count($goods_list);
        // $total_page = ceil($goods_count / 50);
        // for ($i = 0; $i < $total_page; $i++) {

        //     $goods = array_slice($goods_list, $i * 50, 50);
 
        //     foreach($goods as $insert) {                   
        //         $insert["goods_id"] = $old_ids[$insert['SkuNo']];
        //         $data[]=$insert;
           



        //     }
        //     // $rs= $domain_goodssync->insert_multi($data);
        //     // unset($data);
        //     // usleep(1);
            
             
        // }
        // usleep(10);
    }
}  
$logger->error('insert data success to insert DB',"");

// echo '<pre>';
// var_export($data);
// echo '</pre>';
// exit();   


// return $rs;






