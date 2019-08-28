<?php
namespace App\Domain;//声明命名空间1
use App\Model\GoodsSync as ModelGoodsSync;//引入model层



class GoodsSync {

    public function insert($newData) {
        $model = new ModelGoodsSync();
        return $model->insert($newData);
    }
    
    public function insert_multi($newData) {
        // order_id
        // $newData['create_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $model = new ModelGoodsSync();
        return $model->insert_multi($newData);
    }
    function insertData($sql){
        $model = new ModelGoodsSync();
	return $model->executeSql($sql);
    }
    public function update($id, $newData) {
        $model = new ModelGoodsSync();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelGoodsSync();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelGoodsSync();
        return $model->delete($id);
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelGoodsSync();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);

        $rs['items'] = $items;
        $rs['total'] = $total;

        return $rs;
    }

public function getOneData($sku_no){
    $model = new ModelGoodsSync();
    $ret = $model->getOneData($sku_no);    
    return $ret;  
}



  public function updateDataBySku($data,$SkuNo){
    $model = new ModelGoodsSync();
    $ret = $model->updateDataBySku($data,$SkuNo);
    // return $this->getORM()->where("SkuNo",$SkuNo)->update($data);   
    return $ret;   
  }

    public function getAllskuNo(){
        $model = new ModelGoodsSync();
        $list = $model->getAllskuNo();
        foreach ($list as $k => $v) {
            $data[$v['goods_id']] = $v['sku_no'];
        }


        // return 120;
        return $data;
    }  

}
