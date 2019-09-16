<?php
namespace App\Domain;//声明命名空间
use App\Model\Goods as ModelGoods;//引入model层
use App\Domain\Suppliers\GlobalShop as GlobalShopApi;//引入全球购类


class Goods {

    public function insert($newData) {
        // order_id
        // $newData['create_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $model = new ModelGoods();
        return $model->insert($newData);
    }

    public function update($id, $newData) {
        $model = new ModelGoods();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelGoods();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelGoods();
        return $model->delete($id);
    }

    public function getListTotal() {
        $model = new ModelGoods();
        return $model->getListTotal();
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelGoods();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);
        $rs['items'] = $items;
        $rs['total'] = $total;
        return $rs;
    }
    public function getListstock($sku_nos){
        $model = new ModelGoods();
        $list = $model->getListstock($sku_nos);

        foreach ($list as $k => $v) {
            $data[$v['sku_no']] = $v['stock'];
        }
        return $data;
    }

    public function getAllskuNo(){
        $model = new ModelGoods();
        $list = $model->getAllskuNo();
        foreach ($list as $k => $v) {
            $data[$v['goods_id']] = $v['sku_no'];
        }
        // return 120;
        return $data;
    }    
    public function updateData($data,$goods_id){
        $model = new ModelGoods();
        $ret = $model->updateData($data,$goods_id);
        return $ret;     
    }
    public function goodsSync($sku_nos){
        $model = new ModelGoods();
        $list = $model->goodsSync($sku_nos);

        foreach ($list as $k => $v) {
            $data[$v['goods_id']] = $v;
        }


        // return 120;
        return $list;
    }    
    //行云数据
    public function getAllXy($supplier_id = 3,$status=1){
        $model = new ModelGoods();
        $list = $model->getAllXy($supplier_id,$status);
        return $list;
    }  

    function insertData($sql){
        $model = new ModelGoods();
       return $model->executeSql($sql);
    }

    //计算总共多少商品
    public function  getAllCount(){
        $model = new ModelGoods();
        $count = $model->getAllCount();
        return $count;
    }
    //计算总共多少商品
    public function  getAllGoods($page,$perpage,$state=1){
        $model = new ModelGoods();
        $list = $model->getAllGoods($page,$perpage,$state);
        return $list;
    }

    //全球购回调商品信息修改
    public function request_update($goods_no,$data)
    {
        $model = new ModelGoods();
        return $model->request_update($goods_no, $data);
    }

}
