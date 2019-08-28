<?php
namespace App\Domain;//声明命名空间
use App\Model\Brand as ModelBrand;//引入model层



class Brand {

    public function getAllBrand(){
        $model = new ModelBrand();
        $list = $model->getAllBrand();
        $data['list'] = $list;
        return $data;
    }    

    public function getBrandId($brand_name){
        $model = new ModelBrand();
        $ret = $model->getBrandId($brand_name);    
        return $ret;  
    }

    public function getGlobalBrand($type){
        $model = new ModelBrand();
        $ret = $model->getGlobalBrand($type);    
        return $ret;  
    }


    function insertData($sql){
        $model = new ModelBrand();
       return $model->executeSql($sql);
    }


}
