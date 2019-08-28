<?php
namespace App\Domain;//声明命名空间1
use App\Model\Orders as ModelOrders;//引入model层



class Orders {

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
    public function getSubOrders($order_sn) {
        // SELECT id, name FROM tbl_user WHERE (age > 18) LIMIT 1;
        
        $model = new ModelOrders();
        // return $this->getORM()->select('id, name')->where("order_sn =  18")->fetchOne();
        return $model->getSubOrders($order_sn);
    }    
}
