<?php
namespace App\Api;//声明命名空间

use PhalApi\Api;//引入框架类
use App\Domain\Orders as DomainOrders;//引入domain层
use App\Domain\Suppliers\Weini as WeiniApi;//引入domain层
use App\Domain\AppInfo as DomaiAppInfo;//引入domain层
use App\Domain\OrderGoods as DomaiOrderGoods;//引入domain层
use App\Domain\PaymentListNotify as DomaiPaymentListNotify;//引入domain层

/**
 * 订单接口
 * 
 */
class Orders extends Api {
    protected function filterCheck()
    {
    } 

    public function getRules() {
        return array(
            'push' => array(
                'getOrders' => array('name' => 'orders', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => '参数：<a  href="https://api.zeropartin.com/json/order.json">点击查看'),

            ),
            'getExpressInfo' => array(
                
                'getExpressInfo' => array('name' => 'order_nos', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => 'order_nos={"order_nos":"SH20190302083750252289-19058,SH20190306154542894936-7761"}'),
            ),
            'PaymentlistNotify' => array(
                
                'PaymentlistNotify' => array('name' => 'payment_list', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => '参数：<a  href="https://api.zeropartin.com/json/payment_list_notify.json">点击查看'),
            ),                             
        );
    }

    /**
     * 推单接口
     * @desc 把订单数据推送到服务中心    异步通知，详细参数如下
     * @return string sub_order_sn 子订单号
     * @return string message 消息
     * @return int code  状态码  0000成功  9004数据已存在
     * @return string create_time 返回时间
     * @exception 400 非法请求，参数传递错误
     */

    public function push() {      

        $weini_data = $this->getOrders;
        $orders = $weini_data['orders'];
        $allParams = \PhalApi\DI()->request->getAll();

        if (empty($allParams)) {
            return;
        }

        $app_id = $allParams['AppId'];
        $domain = new DomaiAppInfo();//实例化domain层的对象
        $app_info = $domain->getInfo($app_id);

        $data['shop_id']= $app_info['id'];
        $data['order_sn']= $orders['order_sn'];//订单号
        $data['create_time']= $orders['create_time'];//订单创建时间
        $data['consignee_mobile']= $orders['consignee_mobile'];//收货人手机号码
        $data['consignee_name']= $orders['consignee_name'];//收货人姓名user_name
        $data['id_card']= $orders['id_card'];//收货人身份证号码
        $data['province']= $orders['province'];//省
        $data['city']= $orders['city'];//市
        $data['district']= $orders['district'];//区
        $data['address']= $orders['address'];//详细地址
        $data['tax']= $orders['tax'];//税费
        $data['postal_price']= $orders['postal_price'];//邮费
        $data['goods_price']= $orders['goods_price'];//商品价格
        $data['order_price']= $orders['order_price'];//订单价格
        $data['delivery_type']= $orders['delivery_type'];//发货类型
        $data['is_allow_split']= $orders['is_allow_split'];//发货类型

        $domain_o = new DomainOrders();//实例化domain层的对象

        $order_id = $domain_o->insert($data);//调用domain层的insert方法并接收返回数据        

        $order_goods=$orders['order_goods'];
        $domain_g = new DomaiOrderGoods();//实例化domain层的对象
        foreach ($order_goods as $k => $g) {
            $goods_data['order_id'] = $order_id;
            $goods_data['shop_id'] = $app_info['id'];
            $goods_data['sku_no'] = $g['sku_no'];
            $goods_data['buy_quantity'] = $g['buy_quantity'];
            $goods_data['tax'] = $g['tax'];
            $goods_data['buy_price'] = $g['buy_price'];
            $order_id = $domain_g->insert($goods_data);//调用domain层的insert方法并接收返回数据     
            $weini_goods[$k]['SkuNo'] =    $g['sku_no'];
            $weini_goods[$k]['BuyQuantity'] =    $g['buy_quantity'];
            $weini_goods[$k]['Tax'] =    $g['tax'];
            $weini_goods[$k]['BuyPrice'] =    $g['buy_price'];
        }


                $weiniData = array(
                    'OrderNo' => $data['order_sn'],
                    'OrderTime' => $data['create_time'],
                    'ConsigneeNumber' => $data['consignee_mobile'],
                    'ConsigneeName' => $data['consignee_name'],
                    'IdCard' => $data['id_card'],
                    'Province' => $data['province'],
                    'City' => $data['city'],
                    'District' => $data['district'],
                    'DetailedAddres' => $data['address'],
                    'Tax' => $data['tax'],
                    'PostalPrice' => $data['postal_price'],
                    'GoodsPrice' => $data['goods_price'],
                    'OrderPrice' => $data['order_price'],
                    'DeliveryType' => $data['delivery_type'],
                    'IsAllowSplit' => 1,
                    'OrderItems' => $weini_goods
                    
                );
        // return $weiniData;
        $WeiniApi = new WeiniApi();
        $infos = $WeiniApi->AddOrderAsync($weiniData);


        
return $infos;
// return $weini_goods;




} 




    /**
     * 获取快递信息
     * @desc 返回JSON格式的数组参数
     * @return json express_info 快递信息列表，详细参数如下
     * @return string order_no 订单号
     * @return string send_date 发货日期时间
     * @return string sku_no 商品编码
     * @return int num 发货数量
     * @return string post_code 快递公司代码
     * @return string post_no 快递公司运单号
     * @return string post_nos 快递公司运单号拆包
     * @return int is_send 是否发货
     * @exception 400 非法请求，参数传递错误
     */


    public function getExpressInfo() {
        // $domain = new DomainGoods();//实例化domain层的对象
        // $list = $domain->getAllskuNo();
        $order_nos_array = $this->getExpressInfo;


        $order_nos = $order_nos_array['order_nos'];

        $sub_order_no = explode(',',$order_nos);

        $WeiniApi = new WeiniApi();
        $infos = $WeiniApi->PostSynchro($sub_order_no);
        foreach ($infos as $k => $v) {
            $data[$k]['order_no'] = $v['OrderNo'];
            $data[$k]['send_date'] = $v['SendDate'];
            $data[$k]['sku_no'] = $v['SkuNo'];
            $data[$k]['num'] = $v['Num'];
            $data[$k]['post_code'] = $v['PostCode'];
            $data[$k]['post_no'] = $v['PostNo'];
            $data[$k]['post_nos'] = $v['PostNos'];
            $data[$k]['is_send'] = $v['IsSend'];
            # code...
        }
        // $a['express_info'] = $
        // $results = $instance->PostSynchro($weiNiOrderNo);
        return array('express_info' => $data);
    }

    /**
     * 支付单通知
     * @desc 返回JSON格式的数组参数
     * @return json notify 详细参数如下
     * @return Boolean success 处理结果true/false
     * @return string message 处理信息
     * @return string code 处理结果code
     * @exception 400 非法请求，参数传递错误
     */


    public function PaymentListNotify() {
        $weini_data = $this->PaymentlistNotify;
        $payment_list = $weini_data['payment_list'];
        $allParams = \PhalApi\DI()->request->getAll();

        if (empty($allParams)) {
            return;
        }

        $app_id = $allParams['AppId'];
        $domain = new DomaiAppInfo();//实例化domain层的对象
        $app_info = $domain->getInfo($app_id);


        
        $domain_pay = new DomaiPaymentListNotify();//实例化domain层的对象




        $WeiniApi = new WeiniApi();
        // 传到1号仓库的参数
        $weini_param = array(
            'tradeNo' => $payment_list['trade_no'],
            'chargeId' => $payment_list['charge_id'],
            'initalRequest' => $payment_list['inital_request'],
            'initalResponse' => $payment_list['inital_response'],
            'serviceTime' => $payment_list['service_time'],
            'verDept' => $payment_list['ver_dept'],
            'payTransactionId' => $payment_list['pay_transaction_id'],
            'payType' => $payment_list['pay_type'],
            'payWay' => $payment_list['pay_way'],
        );



        // 请求1号仓库接口
        $ret = $WeiniApi->PayAsynNotify($weini_param);
        $data = array(
            'sub_order_sn' => $weini_param['tradeNo'],
            'message' => $ret['Message'],
            'code' => $ret['Code'],
            'source' => $ret['Source'],
            'create_time' => time()
        );

        $id = $DomainOrderNotify->insert($data);






        return $payment_list;
    }





} 
