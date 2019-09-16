<?php
error_reporting(0);
ini_set("display_errors", "Off");
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。


ini_set('memory_limit','800M');    // 临时设置最大内存占用为3G
header('Cache-Control:no-cache,must-revalidate');   
header('Pragma:no-cache');   
header("Expires:0"); 
 
require_once dirname(__FILE__) . '/../init.php';
 
use App\Domain\Category as DomainCategory;//引入商品
use App\Domain\Goods as DomainGoods;//引入商品
use App\Domain\Suppliers\Xingyun as XingyunApi;//引入行云类
use App\Domain\Country as DomainCountry;//引入国家
$t1 = microtime(true);
// $list = $domain->getAllskuNo();
//  同步品牌名称
$Xingyun = new XingyunApi();  
$Category = new  DomainCategory();
$DomainCountry = new DomainCountry();
$DomainGoods = new DomainGoods();//实例化domain层的对象
$XyAllGoods = $DomainGoods->getAllXy();
$XyAllGoods = array_column($XyAllGoods, null, 'sku_no');
//国家名称
$Country =  $DomainCountry->getAllBrand();
$Country_list = array_column($Country, null, 'zh_name');
$category_list = json_decode($Xingyun->PublicRes('get_category_list',[]),true); //所有分类类别

var_export($Category->getGoodsAll(1));die;
//查找商品分类
if($category_list['code'] == '100002'){
    foreach ($category_list['data']['category_list'] as $k => $v) {
        //分类替换
        $cat_type = $Category->CategorySwitch($v['categoryId']);
        $cat_goods_all = array_reduce($Category->getGoodsAll($v['categoryId']), function ($result, $value) {
                            return array_merge($result, array_values($value));
                        }, array());
        foreach ($cat_goods_all as $h => $j) {
            if(empty($XyAllGoods[$j['sku_id']])){
                $order['sku_id'] = $j['sku_id'];
                $category_detail = json_decode($Xingyun->PublicRes('get_goods_detail',$order),true);// 查询详情
                // $category_price  =  $Xingyun->PublicRes('get_goods_price',$order);  // 查询价格
                $install_goods['goods_no'] = $j['sku_id'];
                $install_goods['category_id'] = $cat_type; //类别
                $install_goods['status'] = $j['sku_putaway'];   // 1 上架  
                $install_goods['title'] =str_replace("'", "",  $j['sku_name']);  //标题
                $install_goods['content'] = json_encode($category_detail['data']['sku_detail']);  // 详情1
                // $install_goods['product_price'] = $j['sku_id']; // 价格
                // $install_goods['retail_price'] = $j['sku_id']; // 建议零售价
                // $install_goods['settle_price'] = $j['sku_id']; //结算价格不含运费、税费
                // $install_goods['stock'] = $category_price['data'][0]['sku_stock_num_show'];  // 库存 2
                $install_goods['weight'] = $j['sku_weight']; // 重量单位g
                $install_goods['sku_no'] = $j['sku_id'];  // 商品编码
                $install_goods['tax_rate'] = $j['cross_border'];  // 税率
                $install_goods['supplier_id'] =3;  // 供应商ID
                $install_goods['delivery_type'] = $j['trade_id'];  // 发货方式   (贸易类型：1保税商品,2直邮商品,3完税商品,4售后商品)
                // $install_goods['brand_id'] = $j['sku_id'];  // 品牌ID
                $install_goods['sale_type'] = $category_detail['data']['sku_putaway_status']; //1 
                // $install_goods['sku_no'] = $j['sku_id'];  // 商品编码
                $install_goods['create_time'] = time();  
                $install_goods['delivery_city'] = $j['warehouse_name'];  // 发货地（仓库名称）
                $install_goods['country_id'] = empty($Country_list[$category_detail['data']['country_name']]['country_id']) ? $Country_list[$DomainCountry->Country_switch($category_detail['data']['country_name'])]['country_id']  :$Country_list[$category_detail['data']['country_name']]['country_id']  ;   // 国家id 1
                // $install_goods['xy_price'] =  json_encode( $category_price['data'][0]);  // 价格 2
                $install_goods_all[]  =$install_goods;
            }
        }
      
    }
    $t2 = microtime(true);
    echo '耗时'.round($t2-$t1,3).'秒<br>';
    print_r(1);die;
      // $sql = "INSERT INTO `ns_goods` ( `goods_no`, `category_id`, `status`, `title`, `content`, `stock`, `weight`, `sku_no`, `tax_rate`, `supplier_id`, `delivery_type`, `sale_type`, `create_time`, `delivery_city`, `country_id`, `xy_price`) VALUES";
        $sql = "INSERT INTO `ns_goods` ( `goods_no`, `category_id`, `status`, `title`, `content`, `weight`, `sku_no`, `tax_rate`, `supplier_id`, `delivery_type`, `country_id`, `sale_type`, `create_time`,`delivery_city`) VALUES";
        $count = count($install_goods_all);
        $tmp = "";
        for($i=0;$i<$count;$i++){
            if(empty($XyAllGoods[$install_goods_all[$i]['goods_no']])){
                // $tmp.= "(
                //     '".$install_goods_all[$i]['goods_no']."',                                    
                //     '".$install_goods_all[$i]['category_id']."',                                  
                //     '".$install_goods_all[$i]['status']."',
                //     '".$install_goods_all[$i]['title']."',                                                           
                //     '".$install_goods_all[$i]['content']."',                                                           
                //     '".$install_goods_all[$i]['stock']."',
                //     '".$install_goods_all[$i]['weight']."', 
                //     '".$install_goods_all[$i]['sku_no']."',
                //     '".$install_goods_all[$i]['tax_rate']."',                                                               
                //     '".$install_goods_all[$i]['supplier_id']."',                                                             
                //     '".$install_goods_all[$i]['delivery_type']."', 
                //     '".$install_goods_all[$i]['sale_type']."', 
                //     '".$install_goods_all[$i]['create_time']."', 
                //     '".$install_goods_all[$i]['delivery_city']."',                                                        
                //     '".$install_goods_all[$i]['country_id']."',  
                //     '".$install_goods_all[$i]['xy_price']."'),";
                $tmp.= "(
                    '".$install_goods_all[$i]['goods_no']."',                                    
                    '".$install_goods_all[$i]['category_id']."',                                  
                    '".$install_goods_all[$i]['status']."',
                    '".$install_goods_all[$i]['title']."',  
                    '".$install_goods_all[$i]['content']."',          
                    '".$install_goods_all[$i]['weight']."', 
                    '".$install_goods_all[$i]['sku_no']."',
                    '".$install_goods_all[$i]['tax_rate']."',                                                               
                    '".$install_goods_all[$i]['supplier_id']."',                                                             
                    '".$install_goods_all[$i]['delivery_type']."',                                                  
                    '".$install_goods_all[$i]['country_id']."',  
                    '".$install_goods_all[$i]['sale_type']."', 
                    '".$install_goods_all[$i]['create_time']."', 
                    '".$install_goods_all[$i]['delivery_city']."'),";
            }else{
                unset($install_goods_all[$i]);
            }
        }
        if(!empty($tmp)){
             $tmp = rtrim($tmp, ',');
             $tmp .= ";\n";
             $sql .= $tmp; 
             print_r($sql);
             $res=  $DomainGoods->insertData($sql);
             unset($install_goods_all);
        }

        if($res){
            $t2 = microtime(true);
            echo '耗时'.round($t2-$t1,3).'秒<br>';
        }else{
            echo '无数据更新！';
        }


    
}









