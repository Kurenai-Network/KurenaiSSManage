<?php
require_once '../../../../../config.php';
require_once '../../../../database.php';
$token = $_GET['token'];
$node_id = $_GET['node_id'];
$input = file_get_contents('php://input');
if (!empty($token)){
    if ($token == get_config()['api_key']){
        if (!empty($node_id)){
            header('Content-Type:text/plain; charset=utf-8');
            echo submit($input,$node_id);
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

function submit($data, $node_id){
    $data = json_decode($data, true);
    $node = get_node($node_id);
    file_put_contents('log.txt',json_encode($data),FILE_APPEND);
    foreach ($data as $item) {
        $u = $item['u'] * $node['rate'];
        $d = $item['d'] * $node['rate'];
        $report = array(
            'user_id' => $item['user_id'],
            'u' => $u,
            'd' => $d,
            'node_id' => $node_id
        );
        $result = report_traffic($report);
    }
    return json_encode([
        'ret' => 1,
        'msg' => 'ok'
    ]);
}
