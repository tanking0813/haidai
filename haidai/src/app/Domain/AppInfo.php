<?php
namespace App\Domain;//声明命名空间
use App\Model\AppInfo as ModelAppInfo;//引入model层



class AppInfo {

    public function insert($newData) {
        // order_id
        // $newData['create_time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        // $model = new ModelAppInfo();
        // return $model->insert($newData);
    }

    public function update($id, $newData) {
        $model = new ModelAppInfo();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelAppInfo();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelAppInfo();
        return $model->delete($id);
    }

    public function getListTotal() {
        $model = new ModelAppInfo();
        return $model->getListTotal();
    }

    public function getInfo($app_id){
        $model = new ModelAppInfo();
        return $model->getInfo($app_id);
    }

    public function getAppNotice($shop_id){
        // return $this->getORM()->where( 'shop_id', $shop_id )->select( 'notice_url')->fetchOne();;
        $model = new ModelAppInfo();
        return $model->getAppNotice($shop_id);        
    }
   


}
