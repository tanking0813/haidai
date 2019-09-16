<?php
/**
 * 请在下面放置任何您需要的应用配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return array(

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        'sign' => array('name' => 'sign', 'require' => true, 'desc' => 'MD5签名'),
        'AppId' => array('name' => 'AppId', 'require' => true, 'desc' => 'AppId'),
        'AppSecret' => array('name' => 'AppSecret', 'require' => true, 'desc' => 'AppSecret'),
    ),

    /**
     * 接口服务白名单，格式：接口服务类名.接口服务方法名
     *
     * 示例：
     * - *.*         通配，全部接口服务，慎用！
     * - Site.*      Api_Default接口类的全部方法
     * - *.Index     全部接口类的Index方法
     * - Site.Index  指定某个接口服务，即Api_Default::Index()
     */
    'service_whitelist' => array(
        'Site.Index',
    ),

    /**
     * 计划任务配置
     */
    'Task' => array(
        //MQ队列设置，可根据使用需要配置
        'mq' => array(
            // 默认使用文件MQ
            'file' => array(
                'path' => API_ROOT . '/runtime',
                'prefix' => 'task',
            ),
        ),
    ),



);
