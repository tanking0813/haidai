<?php
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。
 
require_once dirname(__FILE__) . '/../init.php';

use App\Domain\Suppliers\GlobalShop as GlobalShopApi;//引入全球购类
use App\Domain\Goods as DomainGoods;//引入商品
use App\Domain\AppInfo as AppInfo;//引入商品
$DomainGoods = new DomainGoods();//实例化domain层的对象
$GlobalShopApi = new GlobalShopApi(); 
// 查看商户回调地址
// $AppInfo = new AppInfo(); 
// var_export($AppInfo->getallinfo());die;
// $url = 'http://new.runjia366.com/task/GlobalShop.php';
// 查询商户的密钥
// $data = [
// 			'data'=>111,
// 			'res'=>1212,
// 			'csss'=>2222
// 		];
// var_dump(222,$GlobalShopApi->curlRequest($url,json_encode($data)));die;

$postStr = file_get_contents("php://input"); //获取POST数据
$httprequest = $GlobalShopApi->em_getallheaders(); 
$signature = md5($GlobalShopApi->secret.':'.$httprequest['Wh-Timestamp'].':'.$httprequest['Wh-Event-Code'].':'.$httprequest['Wh-Random-Str'].':'.$postStr.':'.$GlobalShopApi->secret);
file_put_contents('request_global.txt',$postStr.PHP_EOL, FILE_APPEND);
file_put_contents('request_global.txt', var_export($httprequest,true) .PHP_EOL, FILE_APPEND);
//验签
if($httprequest['Wh-Signature'] == $signature){
		$postStr = json_decode($postStr,true);
		$itemId = $postStr['itemId'];
		// 分销商品售卖状态变更
		if($httprequest['Wh-Event-Code'] == 'DS_ITEM_STATUS_CHANGE_NOTIFY'){
			// PUT_AWAY:上架, SOLD_OUT:下架
			$status = $postStr['status'] == 'PUT_AWAY'?1:0;
			$update_val['status'] = $status;
		}
		//分销商品价格变更通知
		if($httprequest['Wh-Event-Code'] == 'DS_ITEM_SALE_PRICE_CHANGE_NOTIFY'){
			$price = $postStr['currentPrice']/100;
			$update_val['product_price'] = $price;
		}
		//分销共享库存变更
		if($httprequest['Wh-Event-Code'] == 'DS_SHARED_STOCK_CHANGE_NOTIFY'){
			$num = $postStr['currentStockQuantity'];
			$update_val['stock'] = $num;
		}
		//渠道独占库存变更
		if($httprequest['Wh-Event-Code'] == 'CHANNEL_EXCLUSIVE_STOCK_CHANGE_NOTIFY'){
			$num = $postStr['currentStockQuantity'];// currentStockQuantity	int	库存全量信息
			$update_val['stock'] = $num;
		}
		//物流运单号回执通知 实现自动发货（待验证） 
		if($httprequest['Wh-Event-Code'] == 'LOGISTICS_WAYBILL_NUMBER_NOTIFY'){
			$update_val['waybillNo'] = $postStr['waybillNo']; //物流订单号
			$update_val['merchantOrderNo'] = $postStr['merchantOrderNo']; //商户唯一订单号
			$update_val['carrier'] = $postStr['carrier']; //快递公司编码
			
		}
		//支付成功后回执通知（待验证）
		if($httprequest['Wh-Event-Code'] == 'PAY_SUCCESS_NOTIFY'){
			$update_val['merchantOrderNo'] = $postStr['merchantOrderNo']; //商户唯一订单号
			$update_val['amount'] = $postStr['amount']; // 售卖状态  PUT_AWAY:上架, SOLD_OUT:下架
			$update_val['transactionNo'] = $postStr['transactionNo']; //交易流水号
			$update_val['time'] = $postStr['time']; //交易时间
			$update_val['status'] = $postStr['status']; //交易状态 0-成功 1-失败
			$update_val['desc'] = $postStr['desc']; //交易状态描述

		}
		//分账回执通知（待验证）
		if($httprequest['Wh-Event-Code'] == 'PROFIT_SHARE_SUCCESS_NOTIFY'){
			$update_val['merchantOrderNo'] = $postStr['merchantOrderNo']; //商户唯一订单号
			$update_val['amount'] = $postStr['amount']; // 售卖状态  PUT_AWAY:上架, SOLD_OUT:下架
			$update_val['taxFee'] = $postStr['taxFee']; //税费
			$update_val['supplyPrice'] = $postStr['supplyPrice']; //供货价
			$update_val['profitShareAmount'] = $postStr['profitShareAmount']; //分账佣金
			$update_val['serviceFeeAmount'] = $postStr['serviceFeeAmount']; //服务费佣金

		}
		
		//更改商品信息
		if(!empty($itemId)){
			$res = $DomainGoods->request_update($itemId,$update_val);
		}
		//约定
		if(!empty($res)){
			$return['returnCode'] = 'SUCCESS';
			$return['returnMsg'] = '处理成功';
			echo json_encode($return);
		}
}
$return['returnCode'] = 'FAIL';
$return['returnMsg'] = '处理失败';
echo json_encode($return);









