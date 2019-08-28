<?php

namespace App\Domain\Suppliers;//声明命名空间

/**
 * 全球购接口
 */ 
 
class GlobalShop{

    private $key = 'tianzong';

    private $secret ='f67d2deaa2bf906d1f5a3fd108575d77';

    private $url;

    public  function __construct(){
       // $this->url = 'http://openapi.oceanz.cn/m.api?';
      $this->url = 'http://106.75.191.107/m.api?';
    } 

	/**
	 * 发送请求
	 */
	private function curlRequest($url, $data) {
      $headers = array('Content-Type: application/x-www-form-urlencoded');
      $curl = curl_init(); // 启动一个CURL会话
      curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
      curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
      curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
      curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Post提交的数据包
      curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
      curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      $result = curl_exec($curl); // 执行操作
		  return  $result;
	}

  /**
   * 公共 参数
   *  $requestRes请求数据
   *  $InterfaceName 接口名称

   */
  private function CommonRequest($InterfaceName,$requestRes = [])
  {
    $http_request['shopKey']  = $this->key;
    $http_request['request'] = empty($requestRes)?'':json_encode($requestRes);
    $http_request['sign']  = $this->encryption($http_request['request']);
    $http_request['_mt'] = $InterfaceName;
    $http_request['_sm'] = 'MD5';
    return  $http_request;
  }


	//加密
	private function encryption($request = '')
	{
	 	return MD5($this->key.$request.$this->secret);
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

		return json_encode($return);
	}
  /**
   *  @param  $InterfaceName 请求接口地址
   *  @param  array  $InterfaceName
   */

  public  function  RequestMethod($InterfaceName,$requestRes=[]){
    $requestreturn = $this->CommonRequest($InterfaceName,$requestRes);
    $return = json_decode($this->curlRequest($this->url,$requestreturn),true);
    return   $this->request_return(
                                  $return['returnCode'],
                                  empty($return['success'])?$return['errorMsg']:'成功',
                                  empty($return['success'])?'':$return['content'] 
                                  ) ;
  }


  //全球购获取所有商品
    public function getGoodsAll($page=1,$pageSize=6,$return_list=[])
    {
        $InterfaceName = 'buyer.productPageQuery';
        $requestRes =['pageIndex'=>$page,'pageSize'=>$pageSize]; 
        $return_res =  json_decode( $this->RequestMethod($InterfaceName,$requestRes),true);
        $return_list[] = $return_res['data']['items'];
        $num = $return_res['data']['pageInfo']['totalCount']/$pageSize;
        $page++;
        if(!empty($return_res['data']['items']) && $page < $num +1 ){
           return  $this->getGoodsAll($page,$pageSize,$return_list);
        }
        return  $return_list;
    }

    //全球购分类
    public function categoryList()
    {   
        $InterfaceName = 'buyer.categoryQuery';
        $return_res = json_decode($this->RequestMethod($InterfaceName),true)['data']['openCategoryTree']['children'];
        if(!empty($return_res)){
             foreach ($return_res as $k => $v) {
                            $new_return_res[] = [
                                            'category_id'=>$v['categoryId'],
                                            'category_name'=>$v['categoryName'],
                                            'pid'=>0,
                                            'level'=>1,
                                        ];
                    foreach ($v['children'] as $h => $j) {
                            $new_return_res[] = [
                                            'category_id'=>$j['categoryId'],
                                            'category_name'=>$j['categoryName'],
                                            'pid'=>$v['categoryId'],
                                            'level'=>2,
                                        ];
                        foreach ($j['children'] as $m => $n) {
                            $new_return_res[] = [
                                            'category_id'=>$n['categoryId'],
                                            'category_name'=>$n['categoryName'],
                                            'pid'=>$j['categoryId'],
                                            'level'=>3,
                                        ];
                            
                            }   
                    }
             }
        }
         return  $new_return_res;
    }
   


    //全球购商品详情
    public function goodsDetail($itemId='')
    {   
        if(empty($itemId)){
                return   $this->request_return('1004','参数错误',[]);
        }
        $return_res= [];
        $InterfaceName = 'buyer.getProductDetail';
        $requestRes  = ['itemId'=>$itemId];
        $detail =  json_decode($this->RequestMethod($InterfaceName,$requestRes),true) ;
        if($detail['code'] == '00000000'){
            $return_res = $detail['data'];
        }
        return $return_res;
    }
    //全球购品牌
    public function brandGlobal()
    {
        $return_res= [];
        $InterfaceName = 'buyer.brandQuery';
        $brand =  json_decode($this->RequestMethod($InterfaceName),true);
        if($brand['code'] == '00000000'){
            $return_res = $brand['data']['brands'];
        }
        return $return_res;
    }

    //订单创建接口
    // merchantOrderNo 是 string  订单号
    // orderSource 否 string  订单来源
    // channelCode 是 string  渠道标示，店铺key
    // gmtCreate 是 string  下单时间timestamp
    // payType 是 string  支付方式 (如果通过平台提供的支付方式，请填写huiju_pay)
    // payTransactionId  是 string  支付流水号
    // gmtPaid 是 string  支付完成时间timestamp
    // totalPrice  是 string  订单总金额，单位：元
    // actualPrice 是 string  单位元(订单原价 + 运费 - 优惠金额==>totalPrice + shippingFee - discountPrice)
    // shippingFee 是 string  运费
    // discountPrice 是 string  优惠金额，单位：元
    // discountDesc  是 string  优惠信息描述
    // orderBuyerInfo  是 BuyerInfo 订购人信息
    // orderAddressItem  是 BuyerOrderAddressItem 订单地址信息
    // orderGoodsItems 是 List<BuyerOrderGoodsItem> 订单商品信息
    // initalRequest 是 string  支付原始请求数据
    // initalResponse  是 string  支付原始响应数据
    public function creationOrder()
    {
        //商品名称
        $orderGoodsItems = [
                              [
                              "itemId"=>"CC16BE0AE2DCB8338",
                              "count"=>"2",
                              "price"=>"13", //商品单价
                              "gnum"=>"1"
                              ],[
                              "itemId"=>"CC16A3759EB8F9687",
                              "count"=>"1",
                              "price"=>"14",
                              "gnum"=>"2"
                              ]
                            ];
        //订货地址
        $orderAddressItem = [
                              'provinceName'=>'陕西省',
                              'cityName'    =>'西安市',
                              'regionName'  =>'未央区',
                              'detail'      =>'凤城十路',
                              'mobile'      =>'17729095965',
                              'recName'     =>'严'
                            ];
        //订购人信息
        $orderBuyerInfo = [
                            'buyerName' =>'天纵网络',
                            'buyerIdNo' =>'610423199207020000'   //身份证
                          ];
        $shippingFee = 0;  //运费
        $totalPrice  = 40; //订单总金额
        $discountPrice =   0; //优惠金额
        $discountDesc = '';               
        $InterfaceName = 'buyer.createOrder';
        $requestRes  = [
                        'merchantOrderNo'=>'ms'.time().rand(1111,9999),
                        'channelCode'=>$this->key,
                        'gmtCreate'=>time(),
                        'payType'=>'wechat_intl_app',
                        'payTransactionId'=>'2323232323',
                        'gmtPaid'=>time(),
                        'totalPrice'=>$totalPrice,
                        'actualPrice'=>$totalPrice + $shippingFee - $discountPrice,
                        'shippingFee'=>$shippingFee,
                        'discountPrice'=>$discountPrice,
                        'discountDesc'=>$itemId,
                        'orderBuyerInfo'=>$orderBuyerInfo,
                        'orderAddressItem'=>$orderAddressItem,
                        'orderGoodsItems'=>$orderGoodsItems,
                        ];
      $return_order =  json_decode($this->RequestMethod($InterfaceName,$requestRes),true);  
      return  $return_order;
    }

    //取消订单接口
    public function cancellationOrder($merchantOrderNo)
    {
      if(empty($merchantOrderNo)){
          return  $this->request_return('1004','参数错误',[]);
      }
      $InterfaceName = 'buyer.cancelOrder';
      $requestRes  = [
                        'merchantOrderNo'=>$merchantOrderNo,
                      ];
      $return_order =  json_decode($this->RequestMethod($InterfaceName,$requestRes),true);
      return $return_order;
    }

}


