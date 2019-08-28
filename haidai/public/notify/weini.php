<?php
header("Content-Type: application/json;charset=utf-8");
$method = $_SERVER['HTTP_INTERFACENAME'];
$token = $_SERVER['HTTP_TOKEN'];

require_once dirname(__FILE__) . '/../init.php';
use PhalApi\Logger;
use PhalApi\Logger\FileLogger;
use PhalApi\CUrl;
use App\Domain\Orders as DomainOrders;//引入domain层
use App\Domain\OrderNotify as DomainOrderNotify;//引入domain层
use App\Domain\AppInfo as DomaiAppInfo;//引入domain层


$logger = new FileLogger(API_ROOT . '/runtime', Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);

$content = file_get_contents('php://input', 'r');
$content = json_decode($content, true);

$data = array(
	'sub_order_sn' => $content['OrderNo'],
	'message' => $content['Message'],
	'code' => $content['Code'],
	'source' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
	'create_time' => time()
);

$json_data = array(
    'sub_order_sn' => $content['OrderNo'],
    'message' => $content['Message'],
    'code' => $content['Code'],
    'create_time' => time()
);


$DomainOrderNotify = new DomainOrderNotify();

$id = $DomainOrderNotify->insert($data);
$logger->error('fail to insert DB', $data);
$order_sn = $content['OrderNo'];
    $domain_a = new DomaiAppInfo();//实例化domain层的对象
    

    $domain_o = new DomainOrders();//实例化domain层的对象
    $order_one = $domain_o->getSubOrders($order_sn);
    $shop_id = $order_one[0]['shop_id'];

    $app_info = $domain_a->get($shop_id);

    $notice_url = $app_info['notice_url'];
    // echo '<pre>';
    // var_export($app_info);
    // echo '</pre>';
    // exit();

    // $content['OrderNo']


// 先实例
// $curl = new \PhalApi\CUrl();

// 第二个参数，表示超时时间，单位为毫秒
// $rs = $curl->get('https://runjia366.com/app/index.php?i=3&c=entry&m=ewei_shopv2&do=mobile', 3000);

        $headers = array(
            'Content-Type'=>'application/json; charset=utf-8',            
            'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36'

        );
   $curl = new \PhalApi\CUrl(3);

   $curl->setHeader($headers);

    // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
    $notice_json_data = json_encode($json_data);
    $rs = $curl->post($notice_url, $notice_json_data, 3000);
	echo $rs;
$rsa = $curl->post('https://runjia366.com/app/index.php?i=3&c=entry&m=ewei_shopv2&do=mobile',$notice_json_data, 3000);
$logger->error('fail to insert rs_url', $rs);


// 描述 + 当时的上下文数据
// $data = array('name' => 'dogstar', 'password' => '123456');
$logger->error('fail to insert DB', $data);
