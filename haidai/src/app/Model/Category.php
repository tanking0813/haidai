<?php
namespace App\Model;//声明命名空间
use PhalApi\Model\NotORMModel as NotORM;//引入框架封装的数据库操作类库notorm

class Category extends NotORM {

    protected function getTableName($id) {
        return 'goods_category';
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


      /**
     * 行云 产品分类对应数据库
     */

    public function CategorySwitch($value='')
    {
       switch ($value) {
            //母婴用品 =>母婴儿童
            case '1':
                $cat = 5;
               break;
            //食品保健 =>营养保健
            case '4':
                $cat = 7;
               break;
            //生活用品 =>居家百货
            case '71':
                $cat = 3;
               break;
            //服饰鞋包 =>居家百货
            case '108':
                $cat = 9;
               break;
            //美妆个护 =>个人护理
            case '233':
                $cat = 1;
               break;
            //每周推荐 =>其他商品
            case '315':
                $cat = 10;
               break;
           default:
               $cat = 10;
               break;
       }
       return  $cat;
    }


}

