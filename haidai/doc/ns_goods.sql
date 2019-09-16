/*
Navicat MySQL Data Transfer

Source Server         : 47.94.13.162
Source Server Version : 50562
Source Host           : 47.94.13.162:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50562
File Encoding         : 65001

Date: 2019-09-14 22:28:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ns_goods
-- ----------------------------
DROP TABLE IF EXISTS `ns_goods_haidai`;
CREATE TABLE `ns_goods_haidai` (
  `goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `pcate` int(11) DEFAULT '0' COMMENT '分类ID',
  `ccate` int(11) DEFAULT '0' COMMENT '上级分类ID',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `thumb` varchar(255) DEFAULT NULL COMMENT '首张主图',
  `content` text COMMENT '详情页内容',
  `product_price` decimal(10,2) DEFAULT '0.00' COMMENT '产品价格',
  `market_price` decimal(10,2) DEFAULT '0.00' COMMENT '市场价格',
  `cost_price` decimal(10,2) DEFAULT '0.00' COMMENT '进价（成本价）',
  `stock` int(10) DEFAULT '0' COMMENT '库存',
  `weight` decimal(10,2) DEFAULT '0.00' COMMENT '重量单位g',
  `thumb_url` text COMMENT '商品主图',
  `shop_id` int(11) DEFAULT '0' COMMENT '合作平台ID',
  `sku_no` varchar(128) DEFAULT NULL COMMENT '商品编码',
  `tax` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '税费',
  `tax_rate` decimal(5,4) NOT NULL DEFAULT '0.0000' COMMENT '税率',
  `supplier_id` int(255) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `delivery_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '发货方式',
  `purchase_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '进价',
  `sale_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1号仓库是否包邮  1包邮包税',
  `brand_id` int(11) DEFAULT NULL COMMENT '品牌ID',
  `is_hot` int(1) DEFAULT NULL COMMENT '是否热销商品 ',
  `is_recommend` int(1) DEFAULT NULL COMMENT '是否推荐 ',
  `is_new` int(1) DEFAULT NULL COMMENT '是否新品 ',
  `is_bill` int(1) DEFAULT NULL COMMENT '是否开具增值税发票 1是，0否 ',
  `sort` int(11) DEFAULT NULL COMMENT '排序 ',
  `real_sales` int(255) DEFAULT NULL COMMENT '实际销量 ',
  `create_time` int(11) DEFAULT NULL COMMENT '商品添加时间 ',
  `update_time` int(11) DEFAULT NULL COMMENT '商品编辑时间 ',
  `code` varchar(255) DEFAULT NULL COMMENT '商家编号',
  `bar_code` varchar(255) NOT NULL COMMENT '条形码',
  `promotion_type` int(11) DEFAULT NULL,
  `promote_id` int(11) NOT NULL,
  `delivery_city` varchar(255) DEFAULT NULL,
  `suppliers_id` int(255) DEFAULT '0' COMMENT '0:其他平台的分类id,1:是海带平台的分类id',
  `haidai_id` varchar(255) DEFAULT NULL,
  `haidai_cid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`goods_id`),
  KEY `idx_pcate` (`pcate`),
  KEY `idx_ccate` (`ccate`)
) ENGINE=MyISAM AUTO_INCREMENT=32332 DEFAULT CHARSET=utf8;
