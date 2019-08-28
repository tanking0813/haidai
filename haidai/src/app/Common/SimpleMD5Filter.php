<?php
namespace App\Common;

use PhalApi\Filter;
use PhalApi\Exception\BadRequestException;




// use PhalApi\Filter;
// use PhalApi\Exception\BadRequestException;
use App\Domain\AppInfo as DomaiAppInfo;//引入domain层
/**
 * SimpleMD5Filter 简单的MD5拦截器
 *
 * - 签名的方案如下：
 *
 * + 1、排除签名参数（默认是sign）
 * + 2、将剩下的全部参数，按参数名字进行字典排序
 * + 3、将排序好的参数，全部用字符串拼接起来
 * + 4、进行md5运算
 *
 * 注意：无任何参数时，不作验签
 *
 * @package     PhalApi\Filter
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-10-23
 */

class SimpleMD5Filter implements Filter {

    protected $signName;
    protected $separator;

    public function __construct($signName = 'sign', $separator = false) {
        $this->signName = $signName;
        $this->separator = $separator;
    }

// p.Test.Index',\n  'sign' => 'w',\n  'AppId' => '20190601',\n  'AppSecret' => 'w',\n  'username' => 'zero',\n)
    public function check() {
        $allParams = \PhalApi\DI()->request->getAll();

        if (empty($allParams)) {
            return;
        }

        $app_id = $allParams['AppId'];
        $domain = new DomaiAppInfo();//实例化domain层的对象
        $row = $domain->getInfo($app_id);

        if( $app_id != $row['app_id'] || empty($app_id) ){
         
             throw new BadRequestException(\PhalApi\T('wrong AppId'), 40013);
        }
        if($allParams['AppSecret'] !=$row['app_secret']){
            throw new BadRequestException(\PhalApi\T('wrong AppSecret'), 40001);
        }

        if(0 ==$row['app_status']){
            throw new BadRequestException(\PhalApi\T('接口已禁用'), 40001);
        }
        // echo '<pre>';
        // var_export($allParams['AppId']);
        // echo '<pre>';
        // exit();




        $sign = isset($allParams[$this->signName]) ? $allParams[$this->signName] : '';
        unset($allParams[$this->signName]);

        $expectSign = $this->encryptAppKey($allParams);

        if ($expectSign != $sign) {
            \PhalApi\DI()->logger->debug('Wrong Sign', array('needSign' => $expectSign));
            throw new BadRequestException(\PhalApi\T('wrong sign'), 6);
        }
    }

    protected function encryptAppKey($params) {


        ksort($params);

        $paramsStrExceptSign = '';
        foreach ($params as $index => $val) {
            if($this->separator){
                if(isset($val) && $val){
                    $paramsStrExceptSign .= ($index . $this->separator . $val);
                }
            }else{
                $paramsStrExceptSign .= $val;
            }
        }
        return md5($paramsStrExceptSign);
    }
}
