<?php
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。
ini_set('memory_limit','1600M');    // 临时设置最大内存占用为3G
header('Cache-Control:no-cache,must-revalidate');   
header('Pragma:no-cache');   
header("Expires:0");

require_once dirname(__FILE__) . '/../init.php';

use App\Domain\Goods as DomainGoods;//引入商品
use App\Domain\Suppliers\Weini as WeiniApi;//引入domain层

$DomainGoods = new DomainGoods();//实例化商品domain层的对象
$old_goods_array = $DomainGoods->getAllskuNo();//获取全部商品编码     
$WeiniApi = new WeiniApi();
$goodsCount = $DomainGoods->getAllCount(); //商品总数
$num   =  $goodsCount / 100;
$t1 = microtime(true);

for ($i=1; $i <$num ; $i++) { 
 	$page_goods = $DomainGoods->getAllGoods($i,100,1); // 分页数据
  	foreach($page_goods as $update_goods) {
		//图片
		$content = json_decode($update_goods['content'],true);  
		$main_picture = json_decode($update_goods['main_picture'],true);
		$all_img  = array_merge_recursive($content['pic'],$main_picture);
		$return_array =  $WeiniApi->get_img_curls($all_img);  // curl并行查看图片是否存在
		// echo '<pre>';
		// var_export($return_array);
		// exit();
		foreach ($return_array as $k => $v) {
			if($v['info'] == 404){
				$errorData[$update_goods['sku_no']][] = $k;
			}
		}
		// $return_main_picture_array =  $WeiniApi->get_img_curls($content['pic']);  // curl并行查看图片是否存在
		// foreach ($return_main_picture_array as $k => $v) {
		// 	if($v['info'] != 200){
		// 		$errorData[$update_goods['sku_no']]['displayImgUrls'][] = $k;
		// 	}
		// }
		// $return_content_pic_array =  $WeiniApi->get_img_curls($main_picture);
		// foreach ($return_content_pic_array as $k => $v) {
		// 	if($v['info'] != 200){
		// 		$errorData[$update_goods['sku_no']]['detailImgUrls'][] = $k;
		// 	}
		// }
		unset($content);
		unset($main_picture);
		unset($all_img);
		unset($return_array);
    	usleep(200);
    }
	if(!empty($errorData)){
		file_put_contents(date('Y-m-d',time()).'error_img.txt',  var_export($errorData,TRUE), FILE_APPEND);
	}

	unset($errorData);    
    unset($page_goods);
    usleep(200);
}

$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒<br>';
var_dump(1);die;
// $array = [
// 			'http://image.mihui365.com/bbc/middleImg/15271838258404494.jpg','http://image.mihui365.com/bbc/middleImg/15271838267566984.jpg','http://image.mihui365.com/bbc/middleImg/15271838301375836.jpg','http://image.mihui365.com/bbc/middleImg/15271838301375836111.jpg'
// 		];
// var_dump($WeiniApi->get_img_curls($array));die;
$t1 = microtime(true);
$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒<br>';
var_dump($DomainGoods->getAllCount());die;
if (is_array($weiNiSkuNoList) ) {
    foreach($weiNiSkuNoList  as $k=>$lists){
        $goods_list = $WeiniApi->SkuSynchro($lists);
        
		$goods_count = count($goods_list);
        $total_page = ceil($goods_count / 100);
        for ($i = 0; $i < $total_page; $i++) {
            $goods = array_slice($goods_list, $i * 100, 100);
            foreach($goods as $update_goods) {
				//图片
				$main_picture_array = explode(";", $update_goods['displayImgUrls']);
				$return_main_picture_array =  $WeiniApi->get_img_curls($main_picture_array);
				foreach ($return_main_picture_array as $k => $v) {
					if($v['info'] != 200){
						$errorData[$update_goods['SkuNo']]['displayImgUrls'][] = $k;
					}
				}
				$content_pic_array = explode(";", $update_goods['detailImgUrls']);
				$return_content_pic_array =  $WeiniApi->get_img_curls($content_pic_array);
				foreach ($return_content_pic_array as $k => $v) {
					if($v['info'] != 200){
						$errorData[$update_goods['SkuNo']]['detailImgUrls'][] = $k;
					}
				}
            }
            usleep(50000);
        }
    }
}
var_dump($errorData);
$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒<br>';
echo 'Now memory_get_usage: ' . memory_get_usage() . '<br />';





 
// //使用方法
// function deal($data){
//     if ($data["error"] == '') {
//         echo $data["url"]." -- ".$data["info"]["http_code"]."\n";
//     } else {
//         echo $data["url"]." -- ".$data["error"]."\n";
//     }
// }
// $urls = array();
// for ($i = 0; $i < 10; $i++) {
//     $urls[] = 'http://www.baidu.com/s?wd=etao_'.$i;
//     $urls[] = 'http://www.so.com/s?q=etao_'.$i;
//     $urls[] = 'http://www.soso.com/q?w=etao_'.$i;
// }
// curl($urls, "deal"); 