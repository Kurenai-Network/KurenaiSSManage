<?php
require_once '../../../../../config.php';
require_once '../../../../database.php';
if (!empty($_GET) && $_SERVER["REQUEST_METHOD"] == "GET"){
    $token = $_GET['token'];
    $node_id = $_GET['node_id'];
    if (!empty($token)){
        if ($token == get_config()['api_key']){
            if (!empty($node_id)){
                header('Content-Type:text/plain; charset=utf-8');
                echo user($node_id);
            }else{
                header('HTTP/1.1 500 Internal Server Error');
                exit('node_id is null');
            }

        }else{
            header('HTTP/1.1 500 Internal Server Error');
            exit('token is error');
        }
    }else{
        header('HTTP/1.1 500 Internal Server Error');
        exit('token is null');
    }
}else{
    header('HTTP/1.1 404 Not Found');
    exit('method is error');
}

function user($node_id){
$node = get_node($node_id);
$users = get_group_id_user($node['group_id']);
$result = [];
foreach ($users as $user){
    $result[] = [
        'id' => intval($user['sid']),
        'port' => intval($node['port']),
        'cipher' => $node['node_method'],
        'secret' => $user['uuid']
    ];
}
return json_encode(
    array(
        'data' => $result
    )
);
}