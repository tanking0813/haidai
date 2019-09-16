/*
Navicat MySQL Data Transfer

Source Server         : 47.94.13.162
Source Server Version : 50562
Source Host           : 47.94.13.162:3306
Source Database       : new_runjia366_co

Target Server Type    : MYSQL
Target Server Version : 50562
File Encoding         : 65001

Date: 2019-09-15 19:41:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ns_goods
-- ----------------------------
DROP TABLE IF EXISTS `ns_goods_haidai`;
CREATE TABLE `ns_goods_haidai` (
  `goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_no` varchar(255) DEFAULT NULL COMMENT '货号',
  `category_id` int(11) DEFAULT '0' COMMENT '分类ID',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 ',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `content` mediumtext COMMENT '详情页内容',
  `product_price` decimal(10,2) DEFAULT '0.00' COMMENT '产品价格',
  `retail_price` decimal(10,2) DEFAULT '0.00' COMMENT '建议零售价',
  `settle_price` decimal(10,2) DEFAULT '0.00' COMMENT '结算价格不含运费、税费',
  `stock` int(10) DEFAULT '0' COMMENT '库存',
  `weight` decimal(10,2) DEFAULT '0.00' COMMENT '重量单位g',
  `sku_no` varchar(128) DEFAULT NULL COMMENT '商品编码',
  `tax_rate` decimal(5,4) NOT NULL DEFAULT '0.0000' COMMENT '税率',
  `supplier_id` int(255) NOT NULL DEFAULT '0' COMMENT '供应商ID   3行云 4全球购',
  `delivery_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '发货方式',
  `sale_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1号仓库是否包邮  1包邮包税  2包邮不包税 3保税 4一般贸易，5直邮 6海外自提 ',
  `brand_id` int(11) DEFAULT NULL COMMENT '品牌ID',
  `is_hot` int(1) DEFAULT NULL COMMENT '是否热销商品 ',
  `is_recommend` int(1) DEFAULT NULL COMMENT '是否推荐 ',
  `is_new` int(1) DEFAULT NULL COMMENT '是否新品 ',
  `is_bill` int(1) DEFAULT NULL COMMENT '是否开具增值税发票 1是，0否 ',
  `sort` int(11) DEFAULT NULL COMMENT '排序 ',
  `real_sales` int(255) DEFAULT NULL COMMENT '实际销量 ',
  `create_time` int(11) DEFAULT NULL COMMENT '商品添加时间 ',
  `update_time` int(11) DEFAULT NULL COMMENT '商品编辑时间 ',
  `bar_code` varchar(255) DEFAULT NULL COMMENT '条形码',
  `delivery_city` varchar(255) DEFAULT NULL,
  `main_picture` text COMMENT '主图',
  `spec_option_name` varchar(255) DEFAULT NULL,
  `limit_num` int(11) NOT NULL DEFAULT '0' COMMENT '限购',
  `country_id` tinyint(255) NOT NULL DEFAULT '0' COMMENT '国家id',
  `xy_price` text COMMENT '行云价格',
  `params` varchar(255) DEFAULT NULL COMMENT '商品规格属性(参数，属性)',
  `haidai_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`goods_id`),
  KEY `idx_pcate` (`category_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2445 DEFAULT CHARSET=utf8mb4 COMMENT='商品表';
