/*
Navicat MySQL Data Transfer

Source Server         : 47.94.13.162
Source Server Version : 50562
Source Host           : 47.94.13.162:3306
Source Database       : new_runjia366_co

Target Server Type    : MYSQL
Target Server Version : 50562
File Encoding         : 65001

Date: 2019-09-15 20:13:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ns_goods_brand
-- ----------------------------
DROP TABLE IF EXISTS `ns_goods_brand`;
CREATE TABLE `ns_goods_brand` (
  `brand_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `brand_name` varchar(100) NOT NULL COMMENT '品牌名称',
  `brand_initial` varchar(1) NOT NULL COMMENT '品牌首字母',
  `brand_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
  `brand_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐，0为否，1为是，默认为0',
  `sort` int(11) DEFAULT NULL,
  `brand_category_name` varchar(50) NOT NULL DEFAULT '' COMMENT '类别名称',
  `category_id_array` varchar(1000) NOT NULL DEFAULT '' COMMENT '所属分类id组',
  `brand_ads` varchar(255) NOT NULL DEFAULT '' COMMENT '品牌推荐广告',
  `category_name` varchar(50) NOT NULL DEFAULT '' COMMENT '品牌所属分类名称',
  `category_id_1` int(11) NOT NULL DEFAULT '0' COMMENT '一级分类ID',
  `category_id_2` int(11) NOT NULL DEFAULT '0' COMMENT '二级分类ID',
  `category_id_3` int(11) NOT NULL DEFAULT '0' COMMENT '三级分类ID',
  `brand_story` mediumtext COMMENT '品牌故事',
  `avg_profit_precent` int(11) DEFAULT NULL COMMENT '平均利润率',
  `brand_logo` varchar(255) DEFAULT NULL COMMENT '品牌标识',
  `country_name` varchar(255) DEFAULT NULL COMMENT '国家名称',
  `country_code` int(255) DEFAULT NULL COMMENT '国家代码',
  `goods_child_cnt` int(255) DEFAULT NULL COMMENT '儿童用品',
  `goods_sku_cnt` mediumtext COMMENT '货物保险单',
  `goods_standard_cnt` varchar(255) NOT NULL COMMENT '商品标准',
  `if_authorised` int(11) DEFAULT NULL COMMENT '是否授权',
  `initial` varchar(255) DEFAULT NULL COMMENT '首写字母',
  `is_new` int(1) DEFAULT NULL COMMENT '是否新品',
  `is_show` int(1) DEFAULT NULL COMMENT '是否显示',
  `seq_num` int(255) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2322 DEFAULT CHARSET=utf8mb4 AVG_ROW_LENGTH=1024 COMMENT='品牌表';
