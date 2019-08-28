<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\Goods as DomainGoods;//引入domain层
use App\Domain\GoodsSync as DomainGoodsSync;//引入domain层
use App\Domain\Brand as DomainBrand;//引入domain层
use App\Domain\Category as DomainCategory;//引入domain层
use App\Domain\Suppliers\Weini as WeiniApi;//引入domain层
use App\Domain\AppInfo as DomaiAppInfo;//引入domain层

/**
 * 临时接口
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Db extends Api {
    public function getRules() {
        return array(
            'StockSync' => array(
                
                'StockSync' => array('name' => 'SkuNo', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => '商品编码JSON格式的数组参数，例如：StockSync={"sku_no":"MDH0468,SWR2496"}'),
            ),

            'goodsSync' => array(
                
                'goodsSync' => array('name' => 'SkuNo', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => '商品编码JSON格式的数组参数，例如：StockSync={"sku_no":"MDH0468,SWR2496"}'),
            ),  
            'goodsSync' => array(
                
                'goodsSync' => array('name' => 'SkuNo', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => '商品编码JSON格式的数组参数，例如：StockSync={"sku_no":"MDH0468,SWR2496"}'),
            ),
            'getExpressInfo' => array(
                
                'getExpressInfo' => array('name' => 'order_nos', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => '商品编码JSON格式的数组参数，例如：StockSync={"order_nos":"SH20190302083750252289-19058,SH20190306154542894936-7761"}'),
            ),                                     
        );
    }

    protected function filterCheck()
    {
    }

    /**
     * 获取商品总数量
     * @desc 返回商品总数
     * @return int count 商品总数
     * @exception 400 非法请求，参数传递错误
     */
    public function GetGoodsCount() {
        $domain = new DomaiAppInfo();//实例化domain层的对象
        // $row = $domain->get(1);
        $row = $domain->getInfo('20190601');
    	return $row;
    }

    /**
     * 获取商品编码
     * @desc 返回全部商品编码
     * @return json list 全部商品编码
     * @exception 400 非法请求，参数传递错误
     */
    public function updateStock() {
        $domain = new DomainGoods();//实例化domain层的对象
        $list = $domain->getAllskuNo();
        $data['list']=$list;

        $old_ids = array_flip($list);

        $weiNiSkuNoList = array_chunk($list, 1000); 
        $WeiniApi = new WeiniApi();

        if (is_array($weiNiSkuNoList) ) {
            foreach($weiNiSkuNoList as $lists){

                $goods_list = $WeiniApi->StockSynchro($lists);

                $goods_count = count($goods_list);
                $total_page = ceil($goods_count / 50);
                for ($i = 0; $i < $total_page; $i++) {

                    $goods = array_slice($goods_list, $i * 50, 50);

                    foreach($goods as $update) {

                        $update_data['stock']=$update['Quantity'];// stock 、total

                        if($update['Quantity'] > 0){
                            $update_data['status']=1;
                        }else{
                            $update_data['status']=0;
                        }
                        
                        $domain = new DomainGoods();//实例化domain层的对象
                        $rs = $domain->update($old_ids[$update['SkuNo']], $update_data);

                        if ($rs >= 1) {
                            // 成功
                        } else if ($rs === 0) {
                            // 相同数据，无更新
                        } else if ($rs === false) {
                            // 更新失败
                        }


                    }
                }
            }

    	}

        return $as;
    }    


    public function download_goods()

    {


// $dd =array (
//   'SkuNo' => 'HPP5691*4',
//   'SkuName' => 'HIPP/荷兰喜宝  益生菌奶粉3段 900G[4件装]',
//   'BarCode' => '4062300265691',
//   'goodsNo' => 'HPP5691',
//   'SettlePrice' => 562.0,
//   'RetailPrice' => 647.0,
//   'Brand' => 'HIPP/荷兰喜宝',
//   'Country' => '荷兰',
//   'Category' => '母婴儿童',
//   'TwoCategory' => '奶粉',
//   'ThreeCategory' => '三段奶粉',
//   'Details' => '添加的益生元能帮助宝宝消化吸收，帮助肠道益生菌生长，调节免疫力。',
//   'Rate' => '0.0910',
//   'DeliveryCode' => 1,
//   'DeliveryCity' => '宁波,重庆',
//   'SaleType' => 1,
//   'Weight' => 4000.0,
//   'detailImgUrls' => 'http://image.mihui365.com/bbc/macImg/310153282358285.jpg;http://image.mihui365.com/bbc/macImg/310153345382906.jpg;http://image.mihui365.com/bbc/macImg/310153333331461.jpg;http://image.mihui365.com/bbc/macImg/310153401899542.jpg;http://image.mihui365.com/bbc/macImg/310153464353211.jpg;http://image.mihui365.com/bbc/macImg/310153877162504.jpg',
//   'displayImgUrls' => 'http://image.mihui365.com/bbc/middleImg/15271838258404494.jpg;http://image.mihui365.com/bbc/middleImg/15271838267566984.jpg;http://image.mihui365.com/bbc/middleImg/15271838301375836.jpg;http://image.mihui365.com/bbc/middleImg/15271838387602501.jpg',
// );


// $domain_goodssync = new DomainGoodsSync();//实例化domain层的对象
// $rs = $domain_goodssync->insert($dd);
// echo '<pre>';
// var_export($$rs);
// echo '</pre>';
// exit();









        $domain = new DomainGoods();//实例化商品domain层的对象
        $list = $domain->getAllskuNo();//获取全部商品编码
   
        $old_ids = array_flip($list);

        // echo '<pre>';
        // var_export($old_ids);
        // echo '</pre>';
        // exit();  
        $all_goods_sku_no = array_chunk($list, 1000); 
        $WeiniApi = new WeiniApi();
        $domain_goodssync = new DomainGoodsSync();//实例化domain层的对象

        if (is_array($all_goods_sku_no) ) {
            foreach($all_goods_sku_no as $lists){
                $goods_list = $WeiniApi->SkuSynchro($lists);

                // echo '第' . $k.'批';
                $goods_count = count($goods_list);
                $total_page = ceil($goods_count / 50);
                for ($i = 0; $i < $total_page; $i++) {

                    $goods = array_slice($goods_list, $i * 50, 50);

                    foreach($goods as $insert) {
        // echo '<pre>';
        // var_export($insert);
        // echo '</pre>';
        // exit();                      
                        // $insert["uniacid"] = $_W["uniacid"];
                        // $insert["stock_id"] = 1;
                        $insert["goods_id"] = $old_ids[$insert['SkuNo']];
                        // $a = pdo_insert("goods_sync", $insert);

                    
                    $rs[] = $domain_goodssync->insert($insert);


                    }
                    // echo '<hr>';              
                     
                }
                usleep(10);
            }
        }  

        return $rs;

    }






}




