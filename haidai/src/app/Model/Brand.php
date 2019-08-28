<?php
namespace App\Model;//声明命名空间
use PhalApi\Model\NotORMModel as NotORM;//引入框架封装的数据库操作类库notorm

class Brand extends NotORM {

    protected function getTableName($id) {
        return 'goods_brand';
    }

    public function getAllBrand() {
        return $this->getORM()
                    ->where(  array('shop_id'=>0 )    )
                    ->select('brand_id', 'country_code', 'brand_name', 'brand_logo')
                    ->fetchAll();

    }


    public function getGlobalBrand($type) {
        return $this->getORM()
                    ->where(  array('supplier_id'=>$type ))
                    ->select('brand_id', 'brand_name')
                    ->fetchAll();

    }

    function getBrandId($brand_name){
	    return $this->getORM()
                    ->where(
                        "brand_name",$brand_name
                    )->select(
                        "brand_id"

                    )->fetchOne();
    }

    function executeSql($sql){
        return $this->getORM()->executeSql($sql);
    }        



}