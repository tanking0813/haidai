<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\Suppliers\Haidai\Common;
use App\Domain\Suppliers\Haidai\Goods;
/**
 * 验签接口
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Test extends Api {
    public function getRules() {
        return array(
            'index' => array(
                'username'  => array('name' => 'username', 'default' => 'zero', 'desc' => '用户名'),
            ),
        );
    }
    protected function filterCheck()
    {
    }    
    /**
     * 验签接口
     * @desc 测试签名专用服务
     * @return string title 标题
     * @return string content 内容
     * @return string version 版本，格式：X.X.X
     * @return int time 当前时间戳
     * @exception 400 非法请求，参数传递错误
     */
    public function index() {
        return array(
            'title' => 'Hello ' . $this->username,
            'version' => PHALAPI_VERSION,
            'time' => $_SERVER['REQUEST_TIME'],
        );
    }
	public function world() {
		/*$hdcomm = new Common();
		return $hdcomm->Login();
		return array('title' => 'Hello World! php');*/
        $hdgoods = new Goods();
        $ret = $hdgoods->getCategory();
        return $ret;
	}
}
