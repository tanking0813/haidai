<?php
namespace App\Domain;//声明命名空间
use App\Model\Country as ModelCountry;//引入model层



class Country {

    public function getAllBrand(){
        $model = new ModelCountry();
        $list = $model->getAllCountry();
        // $data['list'] = $list;
        return $list;
    }    

    public function getCountryId($zh_name){
        $model = new ModelCountry();
        $ret = $model->getCountryId($zh_name);    
        return $ret;  
    }
    public function Country_switch($value='')
    {
        switch ($value) {
            case '中国广东':
                 $country_name = '中国';
                break;
            case '澳大利亚':
                 $country_name = '澳洲';
                break;
            case '印尼':
                 $country_name = '印度尼西亚';
                break;
            default:
                 $country_name = '中国';
                break;
        }

        return  $country_name;
    }
}
