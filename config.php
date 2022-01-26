<?php
/*
 * Copyright (c) 2022. Kurenai Network
 * 项目名称:KurenaiSSManage
 * 文件名称:config.php
 * Date:2022/1/1 下午6:56
 * Author: Kurenai Network
 */

function get_config(){
    $subscribe_url = array(
        '',
        '',
        '',
        '',
        '',
        ''
    );
    return array(
        'api_key' => '',
        'mysql_host' => '127.0.0.1',
        'mysql_pass' => '',
        'mysql_user' => '',
        'redis_pass' => '',
        'redis_host' => '',
        'redis_port' => '',
        'admin_username' => '',
        'subscribe_url' => $subscribe_url[array_rand($subscribe_url,1)]
    );
}








