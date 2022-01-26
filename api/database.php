<?php
/*
 * Copyright (c) 2022. Kurenai Network
 * 项目名称:KurenaiSSManage
 * 文件名称:database.php
 * Date:2022/1/1 下午6:54
 * Author: Kurenai Network
 */

require_once substr(__DIR__, 0,-4).'/config.php';
require_once substr(__DIR__, 0,27).'/init.php';
//CREATE TABLE `user` (
//`id` int(11) NOT NULL AUTO_INCREMENT,
//  `email` text NOT NULL,
//  `uuid` varchar(36) NOT NULL,
//  `u` bigint(30) NOT NULL,
//  `d` bigint(30) NOT NULL,
//  `bandwidth` bigint(30) NOT NULL,
//  `enable` tinyint(4) NOT NULL DEFAULT '1',
//  `created_at` int(10) NOT NULL,
//  `updated_at` int(10) NOT NULL,
//  `need_reset` tinyint(1) NOT NULL DEFAULT '1',
//  `sid` int(15) NOT NULL,
//  `package_id` int(10) DEFAULT NULL,
//  `telegram_id` int(20) DEFAULT NULL,
//  `token` VARCHAR(20) DEFAULT NULL,
//  `node_group_id` text NOT NULL,
//  PRIMARY KEY (`id`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8;
//
//CREATE TABLE `nodes` (
//`id` int(10) NOT NULL AUTO_INCREMENT,
//    `node_type` varchar(10) NOT NULL,
//    `group_id` int(10) NOT NULL,
//    `node_name` text NOT NULL,
//    `address` varchar(15) NOT NULL,
//    `port` int(5) NOT NULL,
//    `node_method` text NOT NULL,
//    `rate` int(10) NOT NULL DEFAULT '1',
//    `network_type` varchar(6) NOT NULL,
//    `tag` text NOT NULL,
//    PRIMARY KEY (`id`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
//
//CREATE TABLE `user_usage` (
//`id` int(11) NOT NULL AUTO_INCREMENT,
//    `sid` int(11) NOT NULL,
//    `log_at` int(11) NOT NULL,
//    `upload` text NOT NULL,
//    `download` text NOT NULL,
//    `node_id` text NOT NULL,
//    PRIMARY KEY (`id`)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
function new_account($data){
    try {
        $mysql_host = get_config()['mysql_host'];
        $mysql_user = get_config()['mysql_user'];
        $mysql_pass = get_config()['mysql_pass'];
        if (count(get_user($data['sid'])) == 0){
            $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
            $insert = 'INSERT INTO `user`(`email`,`uuid`,`u`,`d`,`bandwidth`,`created_at`,`updated_at`,`need_reset`,`sid`,`package_id`,`enable`,`telegram_id`,`token`,`node_group_id`) VALUES (:email,:uuid,0,0,:bandwidth,UNIX_TIMESTAMP(),0,:need_reset,:sid,:package_id,:enable,:telegram_id,:token,:node_group_id)';
            $action = $conn->prepare($insert);
            $action->bindValue(':email',$data['email']);
            $action->bindValue(':uuid',$data['uuid']);
            $action->bindValue(':need_reset',$data['need_reset']);
            $action->bindValue(':sid',$data['sid']);
            $action->bindValue(':package_id',$data['package_id']);
            $action->bindValue(':enable',$data['enable']);
            $action->bindValue(':telegram_id',$data['telegram_id']);
            $action->bindValue(':token',$data['token']);
            $action->bindValue(':bandwidth', $data['bandwidth']);
            $action->bindValue(':node_group_id', $data['node_group_id']);
            set_redis($data['sid'], $data['token'], 'set', 0);
            set_redis("node_group_id_sid_".$data['sid'], $data['node_group_id'], 'set', 0);
            set_redis('uuid'.$data['sid'],$data['uuid'],'set',0);
            return $action->execute();
        } else{
            return L::error_account_already_exists;
        }
    } catch (Exception $e){
        return $e;
    }

}

function get_user($sid){
    $mysql_host = get_config()['mysql_host'];
    $mysql_user = get_config()['mysql_user'];
    $mysql_pass = get_config()['mysql_pass'];
    $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
    $insert = 'SELECT * FROM user WHERE `sid` = :sid';
    $db = $conn->prepare($insert);
    $db->bindValue(':sid',$sid);
    $db->execute();
    return $db->fetchAll(PDO::FETCH_ASSOC);
}
function set_redis($key, $value, $action, $index){

    try {
        $redis_port = get_config()['redis_port'];
        $redis_host = get_config()['redis_host'];
        $redis = new Redis();
        $redis->connect($redis_host, $redis_port);
        if (isset($redis_pass)){
            $redis->auth($redis_pass);
        }
        $redis->select($index);
        switch ($action){
            case 'set':
                $result = $redis->set($key, $value);
                break;
            case 'del':
                $result = $redis->del($key);
                break;
            case 'get':
                $result = $redis->get($key);
                break;
            case 'incrBy':
                $result = $redis->incrBy($key, $value);
                break;
        }
        $redis->close();
        return $result;
    } catch (Exception $e){
        return $e;
    }
}

function set_status($data){
    try {
        $mysql_host = get_config()['mysql_host'];
        $mysql_user = get_config()['mysql_user'];
        $mysql_pass = get_config()['mysql_pass'];
        if (count(get_user($data['sid'])) > 0){
            $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
            $db = $conn->prepare('UPDATE `user` SET `enable` = :enable WHERE `sid` = :sid');
            $db->bindValue(':enable',$data['action']);
            $db->bindValue(':sid',$data['sid']);
            return $db->execute();
        } else{
            return L::error_account_not_found;
        }
    } catch (Exception $e){
        return $e;
    }
}

function delete_account($data){
    try {
        $mysql_host = get_config()['mysql_host'];
        $mysql_user = get_config()['mysql_user'];
        $mysql_pass = get_config()['mysql_pass'];
        if (count(get_user($data['sid'])) > 0){
            $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
            $db = $conn->prepare('DELETE FROM `user` WHERE `sid` = :sid');
            $db->bindValue(':sid',$data['sid']);
            //set_redis($data['sid'],null,'del');
            return $db->execute();
        } else{
            return L::error_account_not_found;
        }

    } catch (Exception $e){
        return $e;
    }
}

function reset_uuid($data){
    try {
        $mysql_host = get_config()['mysql_host'];
        $mysql_user = get_config()['mysql_user'];
        $mysql_pass = get_config()['mysql_pass'];
        if (count(get_user($data['sid'])) > 0) {
            $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
            $db = $conn->prepare('UPDATE `user` SET `uuid` = :uuid WHERE `sid` = :sid');
            $db->bindValue(':sid', $data['sid']);
            $db->bindValue(':uuid', $data['uuid']);
            set_redis('uuid'.$data['sid'],null,'del',0);
            set_redis('uuid'.$data['sid'],$data['uuid'],'set',0);
            return $db->execute();
        }else{
            return L::error_account_not_found;
        }
    } catch (Exception $e){
        return $e;
    }
}
function set_bandwidth($data){
    try {
        $mysql_host = get_config()['mysql_host'];
        $mysql_user = get_config()['mysql_user'];
        $mysql_pass = get_config()['mysql_pass'];
        if (count(get_user($data['sid'])) > 0) {
            $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
            $db = $conn->prepare('UPDATE `user` SET `u` = :u , `d` = :d  WHERE `sid` = :sid');
            $db->bindValue(':u', $data['u']);
            $db->bindValue(':d',$data['d']);
            $db->bindValue(':sid', $data['sid']);
            return $db->execute();
        }else{
            return L::error_account_not_found;
        }
    } catch (Exception $e){
        return $e;
    }
}
function verify_token($key, $value){
    $data = set_redis($key, null, 'get',0);
    if ($data != null){
        if ($data == $value){
            return true;
        }else{
            return false;
        }
    }else{
        $mysql_host = get_config()['mysql_host'];
        $mysql_user = get_config()['mysql_user'];
        $mysql_pass = get_config()['mysql_pass'];
        if (count(get_user($key)) > 0) {
            $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
            $db = $conn->prepare('SELECT uuid,node_group_id FROM user WHERE `sid` = :sid');
            $db->bindValue(':sid', $key);
            $result = $db->fetch(PDO::FETCH_ASSOC);
            if ($result['uuid'] == $value) {
                set_redis($key, $value, 'set', 0);
                set_redis("node_group_id_sid_" . $key, $result['node_group_id'], 'set', 0);
                return true;
            } else {
                return false;
            }
        }else{
            return L::error_account_not_found;
        }

    }

}
function change_package($data){
    try {
        $mysql_host = get_config()['mysql_host'];
        $mysql_user = get_config()['mysql_user'];
        $mysql_pass = get_config()['mysql_pass'];
        if (count(get_user($data['sid'])) > 0) {
            $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
            $db = $conn->prepare('UPDATE `user` SET `bandwidth` = :bandwidth,`package_id` = :package_id, `u` = 0, `d` = 0, `node_group_id` = :node_group_id WHERE `sid` = :sid');
            $db->bindValue(':bandwidth', $data['bandwidth']);
            $db->bindValue(':sid', $data['sid']);
            $db->bindValue(':package_id', $data['pid']);
            $db->bindValue(':node_group_id', $data['node_group_id']);
            return $db->execute();
        }else{
            return L::error_account_not_found;
        }
    } catch (Exception $e){
        return $e;
    }
}

//function set_node($data, $action){
//    $mysql_host = get_config()['mysql_host'];
//    $mysql_user = get_config()['mysql_user'];
//    $mysql_pass = get_config()['mysql_pass'];
//    $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
//    $node_group = array();
//    switch ($action){
//        case 'add':
//            $add = $conn->prepare('INSERT INTO `nodes`(`node_type`,`group_id`,`node_name`,`address`,`port`,`node_method`,`rate`,`network_type`,`tag`) VALUES (:node_type,:group_id,:node_name,:address,:port,:node_method,:rate,:network_type,:tag)');
//            foreach ($data as $id => $nodes){
//                $node = explode("|",$nodes);
//                $node_type = $node[0];
//                $group_id = $node[1];
//                $node_name = $node[2];
//                $address = $node[3];
//                $port = $node[4];
//                $node_method = $node[5];
//                $rate = $node[6];
//                $network_type = $node[7];
//                $tag = $node[8];
//                $node_data_db = array(
//                    ':node_type' => $node_type,
//                    ':address' => $address,
//                    ':port' => $port,
//                    ':node_name' => $node_name,
//                    ':group_id' => $group_id,
//                    ':node_method' => $node_method,
//                    ':rate' => $rate,
//                    ':network_type' => $network_type,
//                    ':tag' => $tag
//                );
//                $add->execute($node_data_db);
//                $node_data = array(
//                    'node_type' => $node_type,
//                    'address' => $address,
//                    'port' => $port,
//                    'node_name' => $node_name,
//                    'group_id' => $group_id,
//                    'node_method' => $node_method,
//                    'rate' => $rate,
//                    'network_type' => $network_type,
//                    'tag' => $tag
//                );
//                $node_group[$group_id][] = $node_data;
//            }
//            for ($i = 1; $i <= count($node_group); $i++){
//                set_redis("node_group_id_".$i, $node_group[$i],'set',1);
//            }
//        case 'del':
//            $del = $conn->prepare('DELETE FROM `nodes`');
//            $del->execute();
//    }
//}

function get_nodes($sid){
//    $node_group_id = set_redis('node_group_id_sid_'.$sid,null,'get',0);
//    if ($node_group_id != null){
//        for ($i = 1; $i <= count(explode(",", $node_group_id)); $i++){
//            $node_data[$i] = set_redis("node_group_id_".$i,null,'get',1);
//        }
//    }else{
    $mysql_host = get_config()['mysql_host'];
    $mysql_user = get_config()['mysql_user'];
    $mysql_pass = get_config()['mysql_pass'];
    if (count(get_user($sid)) > 0) {
        $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user;charset=utf8mb4", $mysql_user, $mysql_pass);
        $action = $conn->prepare('SELECT node_group_id FROM user WHERE `sid` = :sid');
        $action->bindValue(':sid', $sid);
        $action->execute();
        $id = $action->fetch(PDO::FETCH_ASSOC)['node_group_id'];
        //set_redis('node_group_id_sid'.$sid, $id,'set','0');
//            $node_group_id = explode(",", $id);
//            $sql = 'SELECT * FROM nodes WHERE ';
//            for ($i = 0; $i <= count($node_group_id); $i++) {
//                if ($i = 0){
//                    $sql .= '`group_id` = ' . $node_group_id[$i];
//                }
//                $sql .= 'AND `group_id` = ' . $node_group_id[$i];
//            }
        $sql = 'SELECT * FROM nodes WHERE `group_id` = :group_id';
        $action = $conn->prepare($sql);
        $action->bindValue(':group_id',$id);
        $action->execute();
        $node_data = $action->fetchAll(PDO::FETCH_ASSOC);
//            foreach ($node_data as $nodes){
//                if (in_array($nodes['group_id'], $node_group_id)){
//                    set_redis("node_group_id_".$nodes['group_id'], $nodes,'set',1);
//                }
//            }
    }else{
        return L::error_account_not_found;
    }
    return $node_data;

}
function get_uuid($sid){
//    $redis = set_redis('uuid'.$sid,null,'get',0);
//    if ($redis != null){
//        return $redis;
//    }else{
        $mysql_host = get_config()['mysql_host'];
        $mysql_user = get_config()['mysql_user'];
        $mysql_pass = get_config()['mysql_pass'];
        $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
        $action = $conn->prepare('SELECT uuid FROM user WHERE `sid` = :sid');
        $action->bindValue(':sid', $sid);
        $action->execute();
        $result = $action->fetch(PDO::FETCH_ASSOC)['uuid'];
        set_redis('uuid'.$sid,null,'del',0);
        set_redis('uuid'.$sid,$result,'set',0);
        return $result;

//    }
}

function get_invoice($sid){
    $command = 'GetInvoice';
    $postData = array(
        'invoiceid' => $sid,
    );
    $adminUsername = get_config()['admin_username'];
    $results = localAPI($command, $postData, $adminUsername);
    return $results;
}

function get_node($node_id){
    $mysql_host = get_config()['mysql_host'];
    $mysql_user = get_config()['mysql_user'];
    $mysql_pass = get_config()['mysql_pass'];
    $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
    $action = $conn->prepare('SELECT * FROM nodes WHERE `id` = :id');
    $action->bindValue(':id', $node_id);
    $action->execute();
    return $action->fetch(PDO::FETCH_ASSOC);
}

function get_group_id_user($group_id){
    $mysql_host = get_config()['mysql_host'];
    $mysql_user = get_config()['mysql_user'];
    $mysql_pass = get_config()['mysql_pass'];
    $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
    $action = $conn->prepare('SELECT * FROM user WHERE `node_group_id` = :node_group_id AND `enable` = 1 ');
    $action->bindValue(':node_group_id', $group_id);
    $action->execute();
    return $action->fetchAll(PDO::FETCH_ASSOC);
}

function report_traffic($data){
    $u = $data['u'];
    $d = $data['d'];
    $sid = $data['user_id'];
    $node_id = $data['node_id'];
    $mysql_host = get_config()['mysql_host'];
    $mysql_user = get_config()['mysql_user'];
    $mysql_pass = get_config()['mysql_pass'];
    $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
    $action = $conn->prepare('UPDATE `user` SET `u` = u+:u,`d` = d+:d, `updated_at` = UNIX_TIMESTAMP() WHERE `sid` = :sid;INSERT INTO `user_usage`(sid,log_at,upload,download,node_id) VALUES (:sid,UNIX_TIMESTAMP(),:u,:d,:node_id)');
    $action->bindValue(':u',$u);
    $action->bindValue(':d',$d);
    $action->bindValue(':sid',$sid);
    $action->bindValue(':node_id',$node_id);
    return $action->execute();
}
function get_database_count($name){
    $mysql_host = get_config()['mysql_host'];
    $mysql_user = get_config()['mysql_user'];
    $mysql_pass = get_config()['mysql_pass'];
    $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
    $sql = "SELECT COUNT(*) AS count FROM {$name}";
    $action = $conn->prepare($sql);
    $action->execute();
    return $action->fetch(PDO::FETCH_ASSOC)['count'];
}
function reset_traffic_month(){
    $adminUsername = get_config()['admin_username'];
    $get_orders_data = array(
        'status' => 'Active',
        'limitstart' => 0,
        'limitnum' => get_database_count('user')
    );
    $result_orders = localAPI('GetOrders', $get_orders_data, $adminUsername);
    $get_products_data = array(
        'module' => 'KurenaiSSManage'
    );
    $result_products = localAPI('GetProducts', $get_products_data, $adminUsername);
    if ($result_products['result'] == 'success'){
        $pid_array = array();
        foreach ($result_products['products']['product'] as $data){
            $pid_array[] = $data['pid'];
        }
    }
    $today = date("d");
    $month = date("m");
    $total_order = $result_orders['totalresults'];
    $return_start_number = $result_orders['startnumber'];
    $num_returned = $result_orders['numreturned'];
    if ($result_orders['result'] == 'success'){
        foreach ($result_orders['orders']['order'] as $data){
            $sid = $data['lineitems']['lineitem'][0]['relid'];
            $product = get_client_products($sid)['products']['product'][0];
            if ($product['status'] == 'Active' && in_array($product['pid'], $pid_array)){
                $due_date_origin = $product['nextduedate'];
                $due_date = date("d", strtotime($due_date_origin));
                //检测是否为免费账户
                if ($product['billingcycle'] == 'Free Account'){
                    $buy_date = date("d", strtotime($product['regdate']));
                    if ($buy_date != '31'){
                        if ($buy_date == $today){
                            if (get_user($sid) != null){
                                $reset_traffic = array(
                                    'u' => 0,
                                    'd' => 0,
                                    'sid' => $sid
                                );
                                set_bandwidth($reset_traffic);
                            }
                        }
                    }else{
                        if (get_user($sid) != null){
                            $reset_traffic = array(
                                'u' => 0,
                                'd' => 0,
                                'sid' => $sid
                            );
                            set_bandwidth($reset_traffic);
                        }
                    }
                }else{
                    //如果不是则按照duedate
                    if ($due_date != '31'){
                        if ($due_date == $today){
                            if (get_user($sid) != null){
                                $reset_traffic = array(
                                    'u' => 0,
                                    'd' => 0,
                                    'sid' => $sid
                                );
                                set_bandwidth($reset_traffic);
                            }
                        }
                    }else{
                        if ($today = '30'){
                            if (get_user($sid) != null){
                                $reset_traffic = array(
                                    'u' => 0,
                                    'd' => 0,
                                    'sid' => $sid
                                );
                                set_bandwidth($reset_traffic);
                            }
                        }
                    }
                }
            }
        }
    }
}

function get_client_products($sid){
    $adminUsername = get_config()['admin_username'];
    $data = array(
        'serviceid' => $sid
    );
    $result = localAPI('GetClientsProducts', $data,$adminUsername);
    return $result;
}

function set_credit($data){
    $adminUsername = get_config()['admin_username'];
    $client_id = $data['clientid'];
    $description = $data['description'];
    $amount = $data['amount'];
    $type = $data['type'];
    $insert = array(
        'clientid' => $client_id,
        'description' => $description,
        'amount' => $amount,
        'type' => $type
    );
    $result = localAPI('AddCredit',$insert, $adminUsername);
    return $result;
}
function check_traffic(){
    $mysql_host = get_config()['mysql_host'];
    $mysql_user = get_config()['mysql_user'];
    $mysql_pass = get_config()['mysql_pass'];
    $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_user", $mysql_user, $mysql_pass);
    $action = $conn->prepare('SELECT * FROM user');
    $action->execute();
    $result = $action->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $user){
        $total_used = $user['u'] + $user['d'];
        if ($total_used > $user['bandwidth'] && $user['enable'] == 1){
            $data = array(
                'sid' => $user['sid'],
                'action' => 0,
            );
            set_status($data);
        }elseif ($user['enable'] == 0 && $total_used < $user['bandwidth']){
            $data = array(
                'sid' => $user['sid'],
                'action' => 1,
            );
            set_status($data);
        }
    }
}