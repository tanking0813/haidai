<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\Suppliers\Haidai\Goods;
/**
 * 海带商品接口
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class HDGoods extends Api {
	public function getRules() {
        return array(
            // 'getCategory' => array(
            //     'getCategory' => array('name' => 'Categorys', 'type' => 'array', 'format' => 'json', 'default' => array(), 'require' => true, 'desc' => '{catId:0,listShow：1} catId 默认值 0,listShow默认值为 1'),
            // ),
        );
    }
    /**
     * 获取海带全部商品分类
     * @desc 返回JSON格式的数组参数
     * @return json list 全部分类列表列表，详细参数如下     * 
     * @return string image        类别图片
     * @return string name         类别名称
     * @return string parentId     父级类别
     * @return string catOrder     分类排序
     * @return int id              分类id
     * @return int totalCount      总条数
     * @exception 400 非法请求，参数传递错误
     */
    protected function filterCheck()
    {
    }
    /**
     * 获取海带全部商品分类
     * @desc 返回JSON格式的数组参数
     * @return json list 全部分类列表列表，详细参数如下     * 
     * @return string image        类别图片
     * @return string name         类别名称
     * @return string parentId     父级类别
     * @return string catOrder     分类排序
     * @return int id              分类id
     * @return int totalCount      总条数
     * @exception 400 非法请求，参数传递错误
     */
    public function GetAllCategory(){
    	//$getCategory = $this->getCategory;
    	$hdgoods = new Goods();
    	//$ret = $hdgoods->getCategory();
        $ret= $hdgoods->getCategory();  
        return $ret;
    }
     /**
     * 获取海带全部商品的GID
     * @desc 返回JSON格式的数组参数
     * @return json list 全部分类列表列表，详细参数如下     * 
     * @return string image        类别图片
     * @return string name         类别名称
     * @return string parentId     父级类别
     * @return string catOrder     分类排序
     * @return int id              分类id
     * @return int totalCount      总条数
     * @exception 400 非法请求，参数传递错误
     */
    public function GetAllskuNo(){
        $hdgoods = new Goods();
        //$ret = $hdgoods->getCategory();
        $ret= $hdgoods->getGoodsList();  
        return $ret;
    }
}