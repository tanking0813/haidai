<?php
namespace App\Domain;//声明命名空间
use App\Model\Category as ModelCategory;//引入model层
use App\Domain\Suppliers\Xingyun as XingyunApi;//引入行云类


class Category {



    public function update($id, $newData) {
        $model = new ModelCategory();
        return $model->update($id, $newData);
    }

    public function get($id) {
        $model = new ModelCategory();
        return $model->get($id);
    }

    public function delete($id) {
        $model = new ModelCategory();
        return $model->delete($id);
    }

    public function getListTotal() {
        $model = new ModelCategory();
        return $model->getListTotal();
    }

    public function getList($state, $page, $perpage) {
        $rs = array('items' => array(), 'total' => 0);

        $model = new ModelCategory();
        $items = $model->getListItems($state, $page, $perpage);
        $total = $model->getListTotal($state);

        $rs['items'] = $items;
        $rs['total'] = $total;

        return $rs;
    }
    public function getListstock($sku_nos){
        $model = new ModelCategory();
        $list = $model->getListstock($sku_nos);

        foreach ($list as $k => $v) {
            $data[$v['sku_no']] = $v['stock'];
        }


        return $data;
    }



    public function getAllCategory(){
        $model = new ModelCategory();
        $list = $model->getAllCategory();
        $data['list'] = $list;
        return $data;
    }    
    public function getCategoryId($category_name){
        $model = new ModelCategory();
        $ret = $model->getCategoryId($category_name);
        return $ret;  
    }
     /**
     * 行云 产品分类对应数据库
     */
    public function CategorySwitch($value=''){
        $model = new ModelCategory();
        $id = $model->CategorySwitch($value);
        
        return $id;
    }

    // 获取分类下的所有商品
    public function getGoodsAll($category_id=1,$page=1,$return_list=[])
    {
        $Xingyun = new XingyunApi();
        $order['category_id'] = $category_id;
        $order['page_size'] = 50;
        $order['page_index'] = $page;
        $field = 'get_goods_list';
        $order_list =  json_decode( $Xingyun->PublicRes($field,$order),true);
        $total = $order_list['data']['total'];
        $sum = round($total/50)+5;
        $return_list[] = $order_list['data']['goods_list'];
        $page ++ ;
        // $page < $sum
        if(!empty($order_list['data']['goods_list'])){
           return  $this->getGoodsAll($category_id,$page,$return_list);
        }
        return  $return_list;
    }

}
