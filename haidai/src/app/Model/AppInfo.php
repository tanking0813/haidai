<?php
namespace App\Model;//声明命名空间
use PhalApi\Model\NotORMModel as NotORM;//引入框架封装的数据库操作类库notorm

class AppInfo extends NotORM {

    protected function getTableName($id) {
        return 'app_info';
    }

    public function getInfo($app_id){
        return $this->getORM()->where( 'app_id', $app_id )->select( 'id', 'app_id', 'app_secret', 'app_status')->fetchOne();;
    }
    public function getListTotal($state =1) {
        $total = $this->getORM()
            ->where('status', $state)
            ->count('goods_id');
        return intval($total);
    }

    public function getAppNotice($shop_id){
        return $this->getORM()->where( 'shop_id', $shop_id )->select( 'notice_url')->fetchOne();
    }
    public function goodsSync($sku_nos) {
        return $this->getORM()
                    ->where( 'sku_no', $sku_nos )
                    ->select('goods_id', 'pcate', 'ccate', 'title', 'thumb', 'content', 'market_price', 'cost_price', 'stock', 'weight', 'thumb_url', 'sku_no', 
                             'tax', 'tax_rate', 'supplier_id', 'delivery_type', 'sale_type', 'brand_id','is_hot', 'is_recommend', 'is_new', 'code', 'bar_code','delivery_city')
                    ->fetchAll();

    } 

    public function getAllskuNo() {
        return $this->getORM()
                    ->where( 'status', 1 )
                    ->select('goods_id', 'sku_no')
                    ->fetchAll();

    }           

}