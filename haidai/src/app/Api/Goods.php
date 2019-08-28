<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\Goods as DomainGoods;//引入domain层
use App\Domain\Brand as DomainBrand;//引入domain层
use App\Domain\Category as DomainCategory;//引入domain层
/**
 * 商品接口
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Goods extends Api {
    public function getRules() {
        return array(
            'StockSync' => array(
                
                'StockSync' => array('name' => 'SkuNo', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => '商品编码JSON格式的数组参数，例如：StockSync={"name":"MDH0468"}'),
            ),

            'goodsSync' => array(
                
                'goodsSync' => array('name' => 'SkuNo', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => '商品编码JSON格式的数组参数，例如：StockSync={"name":"MDH0468"}'),
            ),            
        );
    }

    protected function filterCheck()
    {
    }

    /**
     * 商品同步接口说明商品编码：  暂时提供两个测试用来测试 1、 HSB1473      2、 SWR2496
     * @desc 返回JSON格式的数组参数
     * @return      int         goods_id            商品ID
     * @return      string      goods_no            货号
     * @return      string      spec_option_name            商品属性选项名
     * @return      int      limit_num            限购数量
     * @return      float       settle_price        结算价格不含运费、税费(精度2位小数)如:0.00
     * @return      float       retail_price        建议零售价(精度2位小数)如:0.00
     * @return      float       tax_rate            税率(精度4位小数)如:0.0000
     * @return      int         delivery_type       发货方式  1-保税区发货  2-香港直邮 4-海外直邮 5-国内发货
     * @return      string      delivery_city       发货城市
     * @return      int         sale_type           是否包邮 1包邮包税
     * @return      float       weight              重量单位g(精度2位小数)如:0.00
     * @return      string      content             json数组 text文字介绍，pic图片数组
     * @return      json        main_picture        主图json数组
     * @return      int         supplier_id         供应商ID
     * @return      int         stock               库存如231
     * @return      string      sku_no              商品编码
     * @return      int         brand_id            品牌ID
     * @return      int         country_id            国家代码ID
     * @return      int         category_id            分类ID三级分类
     * @return      int         bar_code            条形码
     * @return      int         ccate               上级分类ID
     * @return      string      title               标题
     * @return      string      thumb               首张主图
     * @return      string      content             详情页内容
     * @return      int         is_hot              是否热销商品
     * @return      int         is_recommend        是否推荐 
     * @return      int         is_new              是否新品
     * @exception 400 非法请求，参数传递错误
     */
    public function goodsSync() {

        $sku_no_array = $this->goodsSync;

        // $sku_no = $sku_no_array['sku_no'];
        $sku_nos = $sku_no_array['sku_no'];
        // $sku_nos = explode(',',$sku_no);
        // echo '<pre>';
        // var_dump($sku_nos);
        // echo '<pre>';
        $domain = new DomainGoods();//实例化domain层的对象
        $list = $domain->goodsSync($sku_nos);
        return $list;
    }



    /**
     * 获取商品总数量
     * @desc 返回商品总数
     * @return int count 商品总数
     * @exception 400 非法请求，参数传递错误
     */
    public function GetGoodsCount() {

        $domain = new DomainGoods();//实例化domain层的对象
        $total = $domain->getListTotal();

        $data['count']=$total;
    	return $data;

    }

    /**
     * 库存同步接口说明商品编码：  暂时提供两个测试用来测试 1、 HSB1473      2、 SWR2496
     * @desc 返回JSON格式的数组参数
     * @return string sku_no 商品编码
     * @return number quantity 库存数量
     * @exception 400 非法请求，参数传递错误
     */
    public function stockSync() {
        $sku_no_array = $this->StockSync;

        $sku_nos = $sku_no_array['sku_no'];


        
        // $sku_nos = explode(',',$sku_no);
        // $sku_nos = implode(',',$sku_no);
// return $sku_nos;
        $domain = new DomainGoods();//实例化domain层的对象
        $list = $domain->getListstock($sku_nos);

        return $list;
    }


  // `brand_id` bigint(20) NOT NULL COMMENT '索引ID',
  // `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺ID',
  // `brand_name` varchar(100) NOT NULL COMMENT '品牌名称',

  // `brand_initial` varchar(1) NOT NULL COMMENT '品牌首字母',
  // `brand_pic` varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
  // `brand_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐，0为否，1为是，默认为0',
  // `sort` int(11) DEFAULT NULL,
  // `brand_category_name` varchar(50) NOT NULL DEFAULT '' COMMENT '类别名称',
  // `category_id_array` varchar(1000) NOT NULL DEFAULT '' COMMENT '所属分类id组',
  // `brand_ads` varchar(255) NOT NULL DEFAULT '' COMMENT '品牌推荐广告',
  // `category_name` varchar(50) NOT NULL DEFAULT '' COMMENT '品牌所属分类名称',
  // `category_id_1` int(11) NOT NULL DEFAULT '0' COMMENT '一级分类ID',
  // `category_id_2` int(11) NOT NULL DEFAULT '0' COMMENT '二级分类ID',
  // `category_id_3` int(11) NOT NULL DEFAULT '0' COMMENT '三级分类ID',
  // `brand_story` text COMMENT '品牌故事',
  // `avg_profit_precent` int(11) DEFAULT NULL COMMENT '平均利润率',
  // `brand_logo` varchar(255) DEFAULT NULL COMMENT '品牌标识',
  // `country_name` varchar(255) DEFAULT NULL COMMENT '国家名称',
  // `country_code` int(255) DEFAULT NULL COMMENT '国家代码',
  // `goods_child_cnt` int(255) DEFAULT NULL COMMENT '儿童用品',
  // `goods_sku_cnt` text COMMENT '货物保险单',
  // `goods_standard_cnt` varchar(255) NOT NULL COMMENT '商品标准',
  // `if_authorised` int(11) DEFAULT NULL COMMENT '是否授权',
  // `initial` varchar(255) DEFAULT NULL COMMENT '首写字母',
  // `is_new` int(1) DEFAULT NULL COMMENT '是否新品',
  // `is_show` int(1) DEFAULT NULL COMMENT '是否显示',
  // `seq_num` int(255) DEFAULT NULL COMMENT '排序'

    /**
     * 获取全部商品分类
     * @desc 返回JSON格式的数组参数
     * @return json list 全部分类列表列表，详细参数如下     * 
     * @return int category_id      分类ID
     * @return string category_name    分类名称
     * @return int pid    上级分类ID
     * @return int level    等级 采用三级分类1，2，3
     * @return string category_pic 分类图片
     * @exception 400 非法请求，参数传递错误
     */
    public function getAllCategory() {

        $domain = new DomainCategory();//实例化domain层的对象
        $list = $domain->getAllCategory();

        return $list;
    }


    /**
     * 获取全部品牌
     * @desc 返回JSON格式的数组参数
     * @return json list 品牌列表，详细参数如下
     * @return int brand_id 品牌ID
     * @return int country_code 国家代码
     * @return string brand_name 品牌名称
     * @return string brand_initial 品牌首字母
     * @return string brand_logo 品牌标识url
     * @exception 400 非法请求，参数传递错误
     */
    public function getAllBrand() {

        $domain = new DomainBrand();//实例化domain层的对象
        $list = $domain->getAllBrand($sku_nos);

        return $list;
    }
    /**
     * 获取商品编码
     * @desc 返回全部商品编码
     * @return json list 全部商品编码
     * @exception 400 非法请求，参数传递错误
     */
    public function getAllskuNo() {
        $domain = new DomainGoods();//实例化domain层的对象
        $list = $domain->getAllskuNo();
        $data['list']=$list;
        return $data;
    }

}