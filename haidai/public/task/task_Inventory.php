<?php
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期 
ignore_user_abort(TRUE); //函数设置与客户机断开是否会终止脚本的执行。

ini_set('memory_limit','800M');    // 临时设置最大内存占用为3G
header('Cache-Control:no-cache,must-revalidate');   
header('Pragma:no-cache');   
header("Expires:0"); 

require_once dirname(__FILE__) . '/../init.php';

use App\Domain\Goods as DomainGoods;//引入商品

use App\Domain\Suppliers\Weini as WeiniApi;//引入唯妮类

$t1 = microtime(true);
$domain = new DomainGoods();//实例化domain层的对象
$list = $domain->getAllskuNo();

$data['list']=$list;
$old_ids = array_flip($list);

$weiNiSkuNoList = array_chunk($list, 2000); 
$WeiniApi = new WeiniApi();

if (is_array($weiNiSkuNoList) ) {
    foreach($weiNiSkuNoList as $lists){

        $goods_list = $WeiniApi->StockSynchro($lists);

        $goods_count = count($goods_list);
        $total_page = ceil($goods_count / 100);
        for ($i = 0; $i < $total_page; $i++) {

            $goods = array_slice($goods_list, $i * 100, 100);

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
                    echo $old_ids[$update['SkuNo']];
                    echo '成功 <br>';
                } else if ($rs === 0) {
                    // 相同数据，无更新
                    // echo $old_ids[$update['SkuNo']];
                    // echo '相同数据，无更新 <br>';
                } else if ($rs === false) {
                    // 更新失败
                    // echo $old_ids[$update['SkuNo']];
                    // echo '更新失败 <br>';                    
                }


            }
        }
    }

}

$t2 = microtime(true);
echo '耗时'.round($t2-$t1,3).'秒<br>';
echo 'Now memory_get_usage: ' . memory_get_usage() . '<br />';