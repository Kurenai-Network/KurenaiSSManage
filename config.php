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
        'a8f3.571104823.xyz',
        '4a78.571104823.xyz',
        '4b63.571104823.xyz',
        'a1f3.571104823.xyz',
        '7c3d.571104823.xyz',
        'e971.571104823.xyz'
    );
    return array(
        'api_key' => 'e615c940-77e6-4a5b-bc99-2373f2ea0b91',
        'mysql_host' => '127.0.0.1',
        'mysql_pass' => 'DeHnDRNYpGet7hrH',
        'mysql_user' => 'ssmanage',
        'redis_pass' => '',
        'redis_host' => '127.0.0.1',
        'redis_port' => '6379',
        'admin_username' => 'kurenai',
        'subscribe_url' => $subscribe_url[array_rand($subscribe_url,1)]
    );
}








