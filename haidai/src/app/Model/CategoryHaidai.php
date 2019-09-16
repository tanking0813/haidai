<?php
namespace App\Model;//声明命名空间
use PhalApi\Model\NotORMModel as NotORM;//引入框架封装的数据库操作类库notorm

class CategoryHaidai extends NotORM {

    protected function getTableName($id) {
        return 'goods_category_haidai';
    }

    public function getAllCategory() {
        return $this->getORM()
                    ->where( array('is_visible' =>1) )
                    ->select('category_id', 'category_name', 'pid', 'level', 'category_pic')
                    ->fetchAll();

    }    
    function executeSql($sql){
        return $this->getORM()->executeSql($sql);
    }
    function getFieldFormField($fieldname,$fieldval,$getonefield){
      return $this->getORM()->where($fieldname,$fieldval)->select($getonefield)->fetchOne();
    }
    function getCategoryIdByHaidaiId($hid){
      return $this->getORM()
                    ->where(
                        "haidai_id",$hid
                    )->select(
                        "category_id"
                    )->fetchOne();
    }  
}
