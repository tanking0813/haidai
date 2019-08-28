<?php
// error_reporting(0);
// ini_set("display_errors", "Off");
// set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
// ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。


// ini_set('memory_limit','800M');    // 临时设置最大内存占用为3G
// header('Cache-Control:no-cache,must-revalidate');   
// header('Pragma:no-cache');   
// header("Expires:0"); 
 
// require_once dirname(__FILE__) . '/../init.php';
 
// use App\Domain\Category as DomainCategory;//引入商品
// use App\Domain\Goods as DomainGoods;//引入商品
// use App\Domain\Suppliers\GlobalShop as GlobalShopApi;//引入行云类
// use App\Domain\Country as DomainCountry;//引入国家
// $t1 = microtime(true);
// // $list = $domain->getAllskuNo();
// //  同步品牌名称
// $GlobalShopApi = new GlobalShopApi();  


// var_export($GlobalShopApi->categoryQuery());


// <?php
// header("Content-Type: application/json;charset=utf-8");
// $method = $_SERVER['HTTP_INTERFACENAME'];
// $token = $_SERVER['HTTP_TOKEN'];

require_once dirname(__FILE__) . '/../init.php';
use PhalApi\Logger;
use PhalApi\Logger\FileLogger;
use PhalApi\CUrl;

$notice_json_data = array ( 
 'shopKey' => 'tianzong', 
 'request' => '{"pageIndex":"1","pageSize":"3"}', 
 'sign' => 'bf0e18e954c62cec7dbd366f47795dca', 
 '_mt' => 'buyer.productPageQuery', 
 '_sm' => 'MD5', 
 ) ;

    $headers = array(
        // 'Content-Type'=>'application/json; charset=utf-8',            
        // 'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36'

    );

   $curl = new \PhalApi\CUrl();

   // $curl->setHeader($headers);

    // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
    // $notice_json_data = json_encode($json_data);
    $rs = $curl->post('http://106.75.191.107/m.api?', $notice_json_data, 3000);
    echo '<pre>';
    var_export(json_decode($rs,true));
    echo '</pre>';
// 	// echo $rs;
// 	ex
// $rsa = $curl->post('https://runjia366.com/app/index.php?i=3&c=entry&m=ewei_shopv2&do=mobile',$notice_json_data, 3000);


// array ( 
//  'shopKey' => 'tianzong', 
//  'request' => '{"pageIndex":"1","pageSize":"3"}', 
//  'sign' => 'bf0e18e954c62cec7dbd366f47795dca', 
//  '_mt' => 'buyer.productPageQuery', 
//  '_sm' => 'MD5', 
//  ) 

// http://106.75.191.107/m.api?
