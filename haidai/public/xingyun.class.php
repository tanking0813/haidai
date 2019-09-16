<?php
/**
 * 行云接口
 */
class Xingyun{
    private $mch_id = '75041628';
    private $secret ='1BG847AC10007300A8C000000F634ED6';
    private $url;
    public  function __construct(){
        $this->url = 'http://120.76.191.121/api/service/business';
    } 
    /**
   * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
   */
  private function createLinkstring($para) {
      $arg  = "";
      while (list ($key, $val) = each ($para)) {
        if(is_array($val)){
          while (list ($h, $j) = each ($val)) {
            $arg.=$h."=".$j."&";
          }
          unset($val);
        }else{
          $arg.=$key."=".$val."&";
        }
      }
      //去掉最后一个&字符
      $arg = substr($arg,0,count($arg)-2);
      //如果存在转义字符，那么去掉转义
      if(get_magic_quotes_gpc()){
        $arg = stripslashes($arg);
      }
      return $arg;
    }
/**
 * 生成字符串参数
 * @param array $param 参数
 * @return  string        参数字符串
 */
public function getStr($param)
{
  $str = '';
  foreach ($param as $key => $value) {
    $str=$str.$key.'='.$value.'&';
  }
  $str = rtrim($str,'&');
  return $str;
}
  /**
   * 发送请求
   */
  private function curlRequest($url, $data) {
    $headers = ["Content-type: application/json;charset='utf-8'"];
      $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL,$url );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);    
      $respon = curl_exec($ch);
    return $respon;
  }

  //加密
  private function encryption($order = [])
  {
    return MD5($this->getStr($order).$this->secret);
  }
  
  //请求
  private function request_curl($order = [])
  {
    return  json_decode($this->curlRequest($this->url,json_encode($order)),true);
  }

  // 请求返回
  private function request_return($code='',$msg = '', $data = [])
  {
    $return['code'] = $code;
    $return['data'] = $data ;
    $return['msg'] = $msg;
    // return json_encode($return);
    return  $return;
  }


  /**
     * 增加销售订单
     * @param $field  = 'add_order';
     * @param $order(array) 添加订单参数    
     * 参数内容  
     * $order['merchant_order_no']    订单标识Id(一般是对接方的订单号)  
     * $order['pay_type']       付款方式:1余额支付,2信用支付
     * $order['accept_name']      收货人姓名 
     * $order['card_id']        身份证号码
     * $order['post_code']        邮政编码(6位长度)
     * $order['telphone']         联系电话
     * $order['mobile']         手机
     * $order['province']       所属区域：省
     * $order['city']         所属区域：市
     * $order['area']           所属区域：区
     * $order['address']        收货地址
     * $order['card_url_front']     身份证正面URL部分直邮必填
     * $order['card_url_back']      身份证背面URL部分直邮必填
     * $order['message']          订单留言
     * $order['items']['sku_id']      商品编码(SKU_ID)
   * $order['items']['quantity']    订购数量（int）
     */

  /**
   * 查看订单状态
   * @param $field  = 'get_order_info';
   * @param $order['order_no'] (string)  商品订单编号(分单)
   */

  /**
     * 获取产品类别
     * @param $field  = 'get_category_list';
     */


  /**
     * 获取产品列表
     * @param $field  = 'get_goods_list';
     * @param $order(array) 产品列表参数
     * $order['category_id']    产品类别编号  
     * $order['page_size']    每页显示多少条(最多50)
     * $order['page_index']   页码:从0开始     
     */

    /**
     * 获取产品详情
     * @param $field  = 'goods_detail';
   * @param $order['sku_id'] (string)  Skuid商品ID
     */

    /**
     * 增加待支付订单
     * @param $field  = 'add_unpaid_order';
     * @param $order(array) 添加订单参数    
     * 参数内容  
     * $order['merchant_order_no']    订单标识Id(一般是对接方的订单号)  
     * $order['accept_name']      收货人姓名
     * $order['card_id']        身份证号码
     * $order['post_code']        邮政编码(6位长度)
     * $order['telphone']         联系电话
     * $order['mobile']         手机
     * $order['province']       所属区域：省
     * $order['city']         所属区域：市
     * $order['area']           所属区域：区
     * $order['address']        收货地址
     * $order['card_url_front']     身份证正面URL部分直邮必填
     * $order['card_url_back']      身份证背面URL部分直邮必填
     * $order['message']          订单留言
     * $order['items']['sku_id']      商品编码(SKU_ID)
   * $order['items']['quantity']    订购数量（int）
     */

    /**
     * 获取商品库存
     * @param $field  = 'get_goods_stock';
   * @param $order['sku_list'] (string)  Skuid商品ID,用,号隔开
     */

    public function PublicRes($field = '',$order=[])
    { 
      $order['opcode'] = $field;
      $order['merchant_id'] = $this->mch_id;
      $return_field =  substr($order['opcode'], strpos($order['opcode'], '_')+1);
      ksort($order,SORT_STRING);
      // ksort($order);
      // echo '<pre>';
      // var_export($order);
      // exit();
      $order['sign'] = $this->encryption($order);
      $order['sign_type']  = 'MD5';
      $res = $this->request_curl($order);
      var_dump($res);die;
      switch ($field) {
        //产品详情
        case 'goods_detail':
           $data = $res;
          break;
        //获取商品库存
        case 'get_goods_stock':
           $data = json_decode($res['stock_info'],true);
          break;
        default:
          $data = $res[$return_field];
          break;
      }
      if($res['ret_code'] == '100002'){
        $return =  $this->request_return($res['ret_code'],$res['ret_msg'],$data);
      }else{
        $return =  $this->request_return($res['ret_code'],$res['ret_msg'],[]);
      }
      return  $return;
    }


}

/**
 * 产品列表测试
 */
// $order['category_id'] = 1;
// $order['page_size'] = 22;
// $order['page_index'] = 1;
// $field = 'get_goods_list';

 // ["sku_id"]=>
 //      string(10) "MBS04766-B"

/**
 * 添加订单号
 */
  $field = 'add_order'; 
  $order['merchant_order_no'] = time().rand(11111,99999);   
  $order['pay_type']  = 1 ;     
  $order['accept_name'] ='yanyan';    
  $order['card_id']   = '610423199207020019';     
  // $order['post_code']   = '713700';     
  // $order['telphone']  ='17729095965';     
  $order['mobile']    ='17729095965';   
  $order['province']  ='陕西省';     
  $order['city']    ='西安市';     
  $order['area']    ='未央区';     
  $order['address']   ='未央区';       
  // $order['items']=1235;    

  // $items[]['sku_id'] ='MBS04766-B';    
  // $items[]['quantity'] =1;
  // $items['sku_id'] ='MBS04766-B';    
  // $items['quantity'] =1;
  // $items[]['sku_id'] ='MBS04766-Ba';    
  // $items[]['quantity'] =12;
  // 
  echo $order['items']=json_encode($items);
  // die();


/**
 * 获取产品详情测试
 */
// $field = 'goods_detail';
// $order['sku_id'] =  'MBS04766-B';

/**
 * 获取商品库存
 */
// $field = 'get_goods_stock';
// $order['sku_list'] =  'MBS04766-B';

$Xingyun   = new Xingyun();
var_dump($Xingyun->PublicRes($field,$order));