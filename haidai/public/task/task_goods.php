<?php
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。
ini_set('memory_limit','1600M');    // 临时设置最大内存占用为3G
header('Cache-Control:no-cache,must-revalidate');   
header('Pragma:no-cache');   
header("Expires:0");

require_once dirname(__FILE__) . '/../init.php';

use App\Domain\Goods as DomainGoods;//引入商品
use App\Domain\Brand as DomainBrand;//引入品牌
use App\Domain\Category as DomainCategory;//引入分类
use App\Domain\Country as DomainCountry;//引入国家
// use App\Domain\GoodsSync as DomainGoodsSync;//引入商品同步
use App\Domain\Suppliers\Weini as WeiniApi;//引入domain层
use PhalApi\Logger;
use PhalApi\Logger\FileLogger;
use App\Model\GoodsSync as ModelGoodsSync;//引入domain层


$DomainGoods = new DomainGoods();//实例化商品domain层的对象
$old_goods_array = $DomainGoods->getAllskuNo();//获取全部商品编码
$old_goods_ids = array_flip($old_goods_array);                                                                                

$txtfile = __DIR__."/tmp/newarr.php";
$file_status =  file_exists($txtfile);

// 如果文件存在执行
if($file_status){
	$file = @fopen($txtfile,'r');
	$new_goods_array = array();

	if(!$file){
		return 'file open fail';
	}else{
		$i = 0;

		while (!feof($file)){
			$new_goods_array[$i] = trim(fgets($file),"\0\t\n\r \x0B");
			$i++ ;
			usleep(20);
		}

		fclose($file);
		$new_goods_array = array_filter($new_goods_array); //数组去空
	}

	foreach ($new_goods_array as $key=>$value){
	    if (in_array($value,$old_goods_array)){
	        unset($new_goods_array[$key]);
	    }else{
	        unset($old_goods_array[array_search($value,$old_goods_array)]);
	    }
	}

	if($new_goods_array){
		foreach ($new_goods_array as $k => $v) {
			$goods_data['sku_no']=$v;
			$goods_data['status']=0;
			$goods_data['supplier_id']=1;
			$DomainGoods->insert($goods_data);
			# code...
		}
	}
}


$old_goods_array = $DomainGoods->getAllskuNo();//获取全部商品编码
$old_goods_ids = array_flip($old_goods_array);

$weiNiSkuNoList = array_chunk($old_goods_array, 2000); 
$WeiniApi = new WeiniApi();

$t1 = microtime(true);

if (is_array($weiNiSkuNoList) ) {
    foreach($weiNiSkuNoList as $lists){


        $goods_list = $WeiniApi->SkuSynchro($lists);
		$new_suk_no_data = array_column($goods_list, null,'SkuNo');
		$new_suk_no_array = array_keys($new_suk_no_data);

        foreach ($lists as $key=>$sku_no){
            if (!in_array($sku_no,$new_suk_no_array)){
                // unset($lists[$key]);
                $update_goods_array['status'] = 0;
                $rs = $DomainGoods->updateData($update_goods_array,$old_goods_ids[$sku_no]);
// echo '<pre>';
// echo count($rs);
// var_export($rs);
// echo '</pre>';
// exit();                
            }
            // else{
            //     unset($new_suk_no_array[array_search($sku_no,$new_suk_no_array)]);
            // }
        }


	    $goods_count = count($goods_list);
        $total_page = ceil($goods_count / 100);

        for ($i = 0; $i < $total_page; $i++) {

            $goods = array_slice($goods_list, $i * 100, 100);

            foreach($goods as $update_goods) {

				
				$main_picture_array = explode(";", $update_goods['displayImgUrls']);
				$main_picture_json = json_encode($main_picture_array,true);

				$content_pic_array = explode(";", $update_goods['detailImgUrls']);
				$goods_content_array['text']=$update_goods['Details'];
				$goods_content_array['pic']=$content_pic_array;
				$goods_content = json_encode($goods_content_array,true);

				$ret_sku_arr[] =$update_goods['SkuNo'];
				$update_goods_array =[
					'sku_no'			=>$update_goods['SkuNo'],
					'title'				=>$update_goods['SkuName'],
					'spec_option_name'	=>$update_goods['Spec'],
					'bar_code'			=>$update_goods['BarCode'],
					'goods_no'			=>$update_goods['goodsNo'],
					'limit_num'			=>$update_goods['LimitNum'],
					'settle_price'		=>$update_goods['SettlePrice'],
					'retail_price'		=>$update_goods['RetailPrice'],
					'tax_rate'			=>$update_goods['Rate'],
					'delivery_type'		=>$update_goods['DeliveryCode'],
					'delivery_city'		=>$update_goods['DeliveryCity'],
					'sale_type'			=>$update_goods['SaleType'],
					'weight'			=>$update_goods['Weight'],
					'content'			=>$goods_content,
					'main_picture'		=>$main_picture_json
				];


				$DomainBrand = new DomainBrand();
				$brands = $DomainBrand->getBrandId($update_goods['Brand']);
				$update_goods_array['brand_id'] = $brands['brand_id'];

				$DomainCategory = new DomainCategory();
				$categorys = $DomainCategory->getCategoryId($update_goods['ThreeCategory']);
				$update_goods_array['category_id'] = $categorys['category_id'];

				// if($update_goods['Country']){
				// 	$DomainCountry = new DomainCountry();
				// 	$countrys = $DomainCountry->getCountryId($update_goods['Country']);
				// 	$update_goods_array['country_id'] = $countrys['country_id'];
				// }			


                $rs = $DomainGoods->updateData($update_goods_array,$old_goods_ids[$update_goods['SkuNo']]);

                if ($rs >= 1) {
                    // 成功
                    // $DomainGoods->updateData()

                    // echo $old_goods_sync_ids[$update_goods['SkuNo']];
                    // status
                    // echo '成功 <br>';
                } else if ($rs === 0) {
                    // 相同数据，无更新
                    // echo $old_goods_sync_ids[$update_goods['SkuNo']];
                    // echo '相同数据，无更新 <br>';
                } else if ($rs === false) {
                    // 更新失败
                    // echo $old_goods_sync_ids[$update_goods['SkuNo']];
                    // echo '更新失败 <br>';                    
                }


            }
            usleep(50000);
        }
    }

}



$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒<br>';
echo 'Now memory_get_usage: ' . memory_get_usage() . '<br />';