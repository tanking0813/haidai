<?php
namespace App\Model;//声明命名空间
use PhalApi\Model\NotORMModel as NotORM;//引入框架封装的数据库操作类库notorm

class PaymentListNotify extends NotORM {

    protected function getTableName($id) {
        return 'payment_list_notify';
    }

    // public function insert($newData) {

    //     $user = $this->getORM();  // 在Model子类内，进行数据库操作前，先获取NotORM实例
    //     $user->insert($newData);//调用notrom中封装的insert方法
    //     return $user->insert_id();
    // }



    // public function getListItems($state, $page, $perpage) {
    //     return $this->getORM()
    //         ->select('*')
    //         ->where('state', $state)
    //         ->order('post_date DESC')
    //         ->limit(($page - 1) * $perpage, $perpage)
    //         ->fetchAll();
    // }

    // public function getListTotal($state) {
    //     $total = $this->getORM()
    //         ->where('order_id', $state)
    //         ->count('id');

    //     return intval($total);
    // }    

}







