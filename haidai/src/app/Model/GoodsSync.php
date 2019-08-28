<?php
namespace App\Model;//声明命名空间
use PhalApi\Model\NotORMModel as NotORM;//引入框架封装的数据库操作类库notorm

class GoodsSync extends NotORM {

    protected function getTableName($id) {
        return 'goods_sync';
    }



// return $this->getORM()->insert_multi($rows, true);

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
    // public function insert_multi($newData) {

    //     $user = $this->getORM();  // 在Model子类内，进行数据库操作前，先获取NotORM实例
    //     return $user->insert_multi($newData,true);//调用notrom中封装的insert方法
    //     // return $user->insert_id();
    // }


function getcount(){
        return $this->getORM()->count();

}
    public function insert_multi($newData) {

            // $user = $this->getORM();
            //     $a=  $user->insert_multi($newData);
            //     return $a;

        // $rows = array(
        //     array('name' => 'A君', 'age' => 12, 'note' => 'AA'),
        //     array('name' => 'B君', 'age' => 14, 'note' => 'BB'),
        //     array('name' => 'C君', 'age' => 16, 'note' => 'CC'),
        // );

        // INSERT INTO tbl_user (name, age, note) VALUES ('A君', 12, 'AA'), ('B君', 14, 'BB'), ('C君', 16, 'CC')
        // 返回成功插入的条数
        // return $this->getORM()->insert_multi($newData);

        // PhalApi 2.2.0 及以上版本才支持
        // 如果希望使用 IGNORE ，可加传第二个参数
        // INSERT IGNORE INTO tbl_user (name, age, note) VALUES ('A君', 12, 'AA'), ('B君', 14, 'BB'), ('C君', 16, 'CC') 
        return $this->getORM()->insert_multi($newData);
    }

    function executeSql($sql){
        return $this->getORM()->executeSql($sql);
    
    }
  // `goods_id` int(11) NOT NULL AUTO_INCREMENT,
  // `pcate` int(11) DEFAULT '0' COMMENT '分类ID',
  // `ccate` int(11) DEFAULT '0' COMMENT '上级分类ID',
  // `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  // `title` varchar(255) DEFAULT NULL COMMENT '标题',
  // `thumb` varchar(255) DEFAULT NULL COMMENT '首张主图',
  // `content` text COMMENT '详情页内容',
  // `product_price` decimal(10,2) DEFAULT '0.00' COMMENT '产品价格',
  // `market_price` decimal(10,2) DEFAULT '0.00' COMMENT '市场价格',
  // `cost_price` decimal(10,2) DEFAULT '0.00' COMMENT '进价（成本价）',
  // `stock` int(10) DEFAULT '0' COMMENT '库存',
  // `weight` decimal(10,2) DEFAULT '0.00' COMMENT '重量单位g',
  // `thumb_url` text COMMENT '商品主图',
  // `shop_id` int(11) DEFAULT '0' COMMENT '合作平台ID',
  // `sku_no` varchar(128) DEFAULT NULL COMMENT '商品编码',
  // `tax` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '税费',
  // `tax_rate` decimal(5,4) NOT NULL DEFAULT '0.0000' COMMENT '税率',
  // `supplier_id` int(255) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  // `delivery_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '发货方式',
  // `purchase_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '进价',
  // `sale_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1号仓库是否包邮  1包邮包税',
  // `brand_id` int(11) DEFAULT NULL COMMENT '品牌ID',
  // `is_hot` int(1) DEFAULT NULL COMMENT '是否热销商品 ',
  // `is_recommend` int(1) DEFAULT NULL COMMENT '是否推荐 ',
  // `is_new` int(1) DEFAULT NULL COMMENT '是否新品 ',
  // `is_bill` int(1) DEFAULT NULL COMMENT '是否开具增值税发票 1是，0否 ',
  // `sort` int(11) DEFAULT NULL COMMENT '排序 ',
  // `real_sales` int(255) DEFAULT NULL COMMENT '实际销量 ',
  // `create_time` int(11) DEFAULT NULL COMMENT '商品添加时间 ',
  // `update_time` int(11) DEFAULT NULL COMMENT '商品编辑时间 ',
  // `code` varchar(255) DEFAULT NULL COMMENT '商家编号',
  // `bar_code` varchar(255) NOT NULL COMMENT '条形码',
  // `promotion_type` int(11) DEFAULT NULL,
  // `promote_id` int(11) NOT NULL,
  // `delivery_city` varchar(255) DEFAULT NULL,
    function getOneData($sku_no){
	    return $this->getORM()
                    ->where(
                        "SkuNo",$sku_no
                    )->select(
                        "SkuNo as sku_no",
                        "goodsNo as goods_no",
                        "SkuName as title",
                        "BarCode as bar_code",
                        "SettlePrice as cost_price",
                        "RetailPrice as market_price",
                        "Rate as tax_rate",
                        "DeliveryCode as promotion_type",
                        "DeliveryCity as delivery_city",
                        "SaleType as sale_type",
                        "Weight as weight",
                        "Details as content",
                        "detailImgUrls as img",
                        "displayImgUrls"
                    )->fetchOne();
    }
    function getMultiData($num=1000,$offet=0){
	    return $this->getORM()
                    ->select(
                        "SkuNo as sku_no",
                        "goodsNo as goods_no",
                        "SkuName as title",
                        "BarCode as bar_code",
                        "SettlePrice as cost_price",
                        "RetailPrice as market_price",
                        "Rate as tax_rate",
                        "DeliveryCode as promotion_type",
                        "DeliveryCity as delivery_city",
                        "SaleType as sale_type",
                        "Weight as weight",
                        "Details as content",
                        "detailImgUrls as img",
                        "goods_id")
                    ->limit($num,$offet)
                    ->fetchAll();
   	 
    }

  public function updateDataBySku($data,$SkuNo){
    return $this->getORM()->where("SkuNo",$SkuNo)->update($data);   
  }
  
  public function update($data,$goods_id){
    return $this->getORM()->where("id",$goods_id)->update($data);
  }

    public function getAllskuNo($status=1) {
        return $this->getORM()
                    // ->where( 'status', $status )
                    ->select('id as goods_id', 'SkuNo as sku_no')
                    ->fetchAll();

    }    

}







