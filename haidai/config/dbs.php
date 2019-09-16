<?php
/**
 * 分库分表的自定义数据库路由配置
 * 
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author: dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

return array(
    /**
     * DB数据库服务器集群
     */
    'servers' => array(
        'db_master' => array(                       //服务器标记
            'type'      => 'mysql',                 //数据库类型，暂时只支持：mysql, sqlserver
            'host'      => '47.94.13.162',             //数据库域名
            // 'host'      => 'localhost',             //数据库域名
            'name'      => 'new_runjia366_co',               //数据库名字
            'user'      => 'new_runjia366_co',                  //数据库用户名
            'password'  => '7aTRm6icRcZSYnZH',                      //数据库密码
            'port'      => 3306,                    //数据库端口
            'charset'   => 'UTF8',                  //数据库字符集
        ),
    ),

    /**
     * 自定义路由表
     */
    'tables' => array(
        //通用路由
        '__default__' => array(
            'prefix' => 'ns_',
            'key' => 'id',
            'map' => array(
                array('db' => 'db_master'),
            ),
        ),

        
        'goods' => array(                                                //表名
            'prefix' => 'ns_',                                         //表名前缀
            'key' => 'goods_id',                                              //表主键名
            'map' => array(                                             //表路由配置
                array('db' => 'db_master'),                               //单表配置：array('db' => 服务器标记)
                array('start' => 0, 'end' => 2, 'db' => 'db_master'),     //分表配置：array('start' => 开始下标, 'end' => 结束下标, 'db' => 服务器标记)
            ),
        ),
        'goods_sync' => array(                                                //表名
            'prefix' => 'ns_',                                         //表名前缀
            'key' => 'id',                                              //表主键名
            'map' => array(                                             //表路由配置
                array('db' => 'db_master'),                               //单表配置：array('db' => 服务器标记)
                array('start' => 0, 'end' => 2, 'db' => 'db_master'),     //分表配置：array('start' => 开始下标, 'end' => 结束下标, 'db' => 服务器标记)
            ),
        ),
        'country' => array(                                                //表名
            'prefix' => 'ns_',                                         //表名前缀
            'key' => 'country_id',                                              //表主键名
            'map' => array(                                             //表路由配置
                array('db' => 'db_master'),                               //单表配置：array('db' => 服务器标记)
                array('start' => 0, 'end' => 2, 'db' => 'db_master'),     //分表配置：array('start' => 开始下标, 'end' => 结束下标, 'db' => 服务器标记)
            ),
        ),                  
         
    ),
);
