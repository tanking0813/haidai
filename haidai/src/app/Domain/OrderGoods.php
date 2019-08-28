<?php
namespace App\Domain;//声明命名空间1
use App\Model\OrderGoods as ModelOrders;//引入model层



class OrderGoods {

    public function insert($newData) {
        // order_id
        // $newData['create_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $model = new ModelOrders();
        return $model->insert($newData);
    }

    public function update($id, $newData) {
        $model = new ModelOrders();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelOrders();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelOrders();
        return $model->delete($id);
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelOrders();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);

        $rs['items'] = $items;
        $rs['total'] = $total;

        return $rs;
    }
}
