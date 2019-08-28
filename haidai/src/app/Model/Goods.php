<?php
namespace App\Model;//声明命名空间
use PhalApi\Model\NotORMModel as NotORM;//引入框架封装的数据库操作类库notorm

class Goods extends NotORM {

    protected function getTableName($id) {
        return 'goods';
    }

    public function  getOneData($skuno){
    	return $this->getORM()->where("sku_no",$skuno)->select("good_id","status","sku_no")->fetchOne();
    }

    public function getListstock($sku_nos){
        return $this->getORM()->where( 'sku_no', $sku_nos )->select('stock','sku_no');
    }

    public function getListTotal($state =1) {
        $total = $this->getORM()
            ->where('status', $state)
            ->count('goods_id');
        return intval($total);
    }


    public function goodsSync($sku_nos) {
        return $this->getORM()
                    ->where( 'sku_no', $sku_nos )
                    ->where( 'status', 1 )
                    ->select(
                        'goods_id',
                        'spec_option_name',
                        'bar_code',
                        'title',
                        'goods_no',
                        'limit_num',
                        'settle_price',
                        'retail_price',
                        'tax_rate',
                        'delivery_type',
                        'delivery_city',
                        'sale_type',
                        'weight',                        
                        'content',
                        'main_picture',
                        'supplier_id',
                        'stock',                                                
                        'sku_no',                      
                        'brand_id',
                        'country_id',
                        'category_id',
                        'category_id',
                        'is_hot',
                        'is_recommend',
                        'is_new'
                    )
                    ->fetchAll();

    } 

    public function getAllskuNo($status=1) {
        return $this->getORM()
                    ->where( 'status', $status )
                    ->select('goods_id', 'sku_no')
                    ->fetchAll();

    }

	public function updateData($data,$goods_id){
		return $this->getORM()->where("goods_id",$goods_id)->update($data);		
	}

    function executeSql($sql){
        return $this->getORM()->executeSql($sql);
    
    }
    //查看行云商品
    public function getAllXy($supplier_id = 3,$status=1) {
        return $this->getORM()
                    ->where( 'status', $status )
                    ->where( 'supplier_id', $supplier_id )
                    ->select('goods_id', 'sku_no')
                    ->fetchAll();

    }
     public function getAllGoods($page,$perpage,$state=1) {
        return $this->getORM()
            ->select('goods_id', 'sku_no','content','main_picture')
            ->where('status', $state)
            ->limit(($page - 1) * $perpage, $perpage)
            ->fetchAll();
    }

    public function getAllCount($state =1) {
        $total = $this->getORM()
            ->where('status', $state)
            ->count('goods_id');
        return intval($total);
    }
}
