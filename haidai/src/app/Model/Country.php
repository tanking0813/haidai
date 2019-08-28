<?php
namespace App\Model;//声明命名空间
use PhalApi\Model\NotORMModel as NotORM;//引入框架封装的数据库操作类库notorm

class Country extends NotORM {

    protected function getTableName($id) {
        return 'country';
    }

    public function getAllCountry() {
        return $this->getORM()
                    ->select('country_id', 'name', 'zh_name', 'short_name')
                    ->fetchAll();

    }

    function getCountryId($zh_name){
	    return $this->getORM()
                    ->where(
                        "zh_name",$zh_name
                    )->select(
                        "country_id"

                    )->fetchOne();
    }        

}