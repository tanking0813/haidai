<?php
namespace App\Model;//声明命名空间
use PhalApi\Model\NotORMModel as NotORM;//引入框架封装的数据库操作类库notorm

class CategoryGlobal extends NotORM {

    protected function getTableName($id) {
        return 'goods_category_global';
    }

    public function getAllCategory() {
        return $this->getORM()
                    ->where( array('is_visible' =>1) )
                    ->select('category_id', 'category_name', 'pid', 'level', 'category_pic')
                    ->fetchAll();

    }    
    function getCategoryId($category_name){
	    return $this->getORM()
                    ->where(
                        "category_name",$category_name
                    )->select(
                        "category_id"
                    )->fetchOne();
    }  


    function executeSql($sql){
        return $this->getORM()->executeSql($sql);
    }    

}

