<?php
namespace App\Domain;//声明命名空间
use App\Model\CategoryGlobal as ModelCategoryGlobal;//引入model层


class CategoryGlobal {



    public function update($id, $newData) {
        $model = new ModelCategoryGlobal();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelCategoryGlobal();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelCategoryGlobal();
        return $model->delete($id);
    }

    public function getListTotal() {
        $model = new ModelCategoryGlobal();
        return $model->getListTotal();
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelCategoryGlobal();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);

        $rs['items'] = $items;
        $rs['total'] = $total;

        return $rs;
    }
    public function getListstock($sku_nos){
        $model = new ModelCategoryGlobal();
        $list = $model->getListstock($sku_nos);

        foreach ($list as $k => $v) {
            $data[$v['sku_no']] = $v['stock'];
        }


        return $data;
    }



    public function getAllCategory(){
        $model = new ModelCategoryGlobal();
        $list = $model->getAllCategory();
        $data['list'] = $list;
        return $data;
    }    
    public function getCategoryId($category_name){
        $model = new ModelCategoryGlobal();
        $ret = $model->getCategoryId($category_name);
        return $ret;  
    }
   
   function insertData($sql){
        $model = new ModelCategoryGlobal();
       return $model->executeSql($sql);
    }


}
