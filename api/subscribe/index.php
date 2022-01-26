<?php
/*
 * Copyright (c) 2022. Kurenai Network
 * 项目名称:KurenaiSSManage
 * 文件名称:index.php
 * Date:2022/1/1 下午6:56
 * Author: Kurenai Network
 * Thanks V2board
 */

use Symfony\Component\Yaml\Yaml;

require_once '../database.php';
$flag = array("ss", "nodelist", "clash", "surge", "qx","shadowrocket","stash","sip008");
$token = $_GET['token'];
$sid = $_GET['sid'];
$type = $_GET['type'];
$client_product = get_client_products($sid)['products']['product'][0];
if ($client_product['nextduedate'] != '0000-00-00'){
    $timestamp_due_date = strtotime($client_product['nextduedate']);
}else{
    $timestamp_due_date = '';
}
if (in_array($type, $flag) && isset($token) && preg_match("/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i", $token) && preg_match("/^\d*$/", $sid)){
    if (get_uuid($sid) == $token){
        $data = get_nodes($sid);
        $user = get_user($sid)[0];
        header('Content-Type:text/plain; charset=utf-8');
        //echo json_encode($user);
        switch ($type) {
            case 'ss':
                $result = ss_generate($data,$user);
                echo $result;
                break;
            case 'shadowrocket':
                $result = shadowrocket_generate($data,$user,$timestamp_due_date);
                echo $result;
                break;
            case 'nodelist':
                $result = surge_node_ist_generate($data,$user);
                echo $result;
                break;
            case 'clash':
            case 'stash':
                $result = clash_generate($data,$user,$timestamp_due_date);
                echo $result;
                break;
            case 'qx':
                $result = quantumultx_generate($data,$user,$timestamp_due_date);
                echo $result;
                break;
            case 'surge':
                $result = surge_generate($data,$user);
                echo $result;
                break;
            case 'sip008':
                $result = sip008_generate($data,$user);
                echo json_encode($result);
                break;
        }
        return $result;
    }else{
        header('HTTP/1.1 500 Internal Server Error');
        exit('token error');
    }
}

/**
 * @param $data
 * @param $user
 * @return array|false|string|string[]
 */
function surge_generate($data, $user){
    $proxies = '';
    $proxyGroup = '';
    $defaultConfig = __DIR__ . '/rules/default.surge.conf';
    $subsDomain = get_config()['subscribe_url'];
    $subsURL = 'https://' . $subsDomain . '/sub?token=' . $user['uuid'] . '&type=surge&sid='.$user['sid'];
    header("content-disposition:attachment; filename=Kurenai_Network_Surge.conf; filename*=UTF-8''Kurenai_Network_Surge.conf");
    foreach ($data as $node){
        // [Proxy]
        $proxies .= buildShadowsocks($user['uuid'], $node);
        // [Proxy Group]
        $proxyGroup .= $node['node_name'] . ', ';
    }
    $config = file_get_contents("$defaultConfig");
    $config = str_replace('$subs_link', $subsURL, $config);
    $config = str_replace('$subs_domain', $subsDomain, $config);
    $config = str_replace('$proxies', $proxies, $config);
    $config = str_replace('$proxy_group', rtrim($proxyGroup, ', '), $config);
    return $config;

}

function buildShadowsocks($uuid, $data)
{
    $config = [
        "{$data['node_name']}=ss",
        "{$data['address']}",
        "{$data['port']}",
        "encrypt-method={$data['node_method']}",
        "password={$uuid}",
        'tfo=true',
        'udp-relay=true'
    ];
    $config = array_filter($config);
    $uri = implode(',', $config);
    $uri .= "\r\n";
    return $uri;
}

/**
 * @param $data
 * @param $user
 * @param $timestamp_due_date
 * @return array|string|string[]
 */
function clash_generate($data, $user, $timestamp_due_date){
    header("subscription-userinfo: upload={$user['u']}; download={$user['d']}; total={$user['bandwidth']}; expire={$timestamp_due_date}");
    header('profile-update-interval: 24');
    header("content-disposition: filename=Kurenai Network");
    $defaultConfig = __DIR__ . '/rules/default.clash.yaml';
    $config = Yaml::parseFile($defaultConfig);
    $proxy = [];
    $proxies = [];
    foreach ($data as $node){
        $proxy[] = generate_ss_clash($user['uuid'], $node);
        $proxies[] = $node['node_name'];
    }
    $config['proxies'] = array_merge($config['proxies'] ? $config['proxies'] : [], $proxy);
    foreach ($config['proxy-groups'] as $k => $v) {
        if (!is_array($config['proxy-groups'][$k]['proxies'])) continue;
        $isFilter = false;
        foreach ($config['proxy-groups'][$k]['proxies'] as $src) {
            foreach ($proxies as $dst) {
                if (isMatch($src, $dst)) {
                    $isFilter = true;
                    $config['proxy-groups'][$k]['proxies'] = array_diff($config['proxy-groups'][$k]['proxies'], [$src]);
                    $config['proxy-groups'][$k]['proxies'][] = $dst;
                }
            }
        }
        if (!$isFilter) {
            $config['proxy-groups'][$k]['proxies'] = array_merge($config['proxy-groups'][$k]['proxies'], $proxies);
        }
    }
    // Force the current subscription domain to be a direct rule
    $subsDomain = $_SERVER['SERVER_NAME'];
    $subsDomainRule = "DOMAIN,{$subsDomain},Proxies";
    array_unshift($config['rules'], $subsDomainRule);

    $yaml = Yaml::dump($config);
    $yaml = str_replace('$app_name', 'Kurenai Network', $yaml);
    return $yaml;

}

function generate_ss_clash($uuid,$data){
    $array = [];
    $array['name'] = $data['node_name'];
    $array['type'] = 'ss';
    $array['server'] = $data['address'];
    $array['port'] = $data['port'];
    $array['cipher'] = $data['node_method'];
    $array['password'] = $uuid;
    $array['udp'] = true;
    return $array;
}
function isMatch($exp, $str)
{
    try {
        return preg_match($exp, $str);
    } catch (\Exception $e) {
        return false;
    }
}

/**
 * @param $data
 * @param $user
 * @param $timestamp_due_date
 * @return string
 */
function quantumultx_generate($data, $user, $timestamp_due_date){
    header("subscription-userinfo: upload={$user['u']}; download={$user['d']}; total={$user['bandwidth']}; expire={$timestamp_due_date}");
    $url = '';
    foreach ($data as $node){
        $config = [
            "shadowsocks={$node['address']}:{$node['port']}",
            "method={$node['node_method']}",
            "password={$user['uuid']}",
            'fast-open=true',
            'udp-relay=true',
            "tag={$node['node_name']}"
        ];
        $config = array_filter($config);
        $uri .= implode(',', $config);
        $uri .= "\r\n";
    }
    return $uri;
}

/**
 * @param $data
 * @param $user
 * @return string
 */
function surge_node_ist_generate($data, $user){
    header('Content-Type:text/plain; charset=utf-8');
    $url = '';
    foreach ($data as $node){
        $url .= "{$node['node_name']} = ss, {$node['address']}, {$node['port']}, encrypt-method={$node['node_method']}, password={$user['uuid']}, tfo=true, udp-relay=true\r\n";
    }
    return $url;


}

/**
 * @param $data
 * @param $user
 * @param $timestamp_due_date
 * @return string
 */
function shadowrocket_generate($data, $user, $timestamp_due_date){
    $url = '';
    $tot = convert_byte($user['bandwidth']);
    $upload = convert_byte($user['u']);
    $download = convert_byte($user['d']);
    $time = date('Y-m-d', $timestamp_due_date);
    $url .= "STATUS=🚀↑:{$upload},↓:{$download},TOT:{$tot}💡Expires:{$time}\r\n";

    foreach ($data as $node){
        $name = rawurlencode($node['node_name']);
        $str = str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode("{$node['node_method']}:{$user['uuid']}")
        );
        $url .= "ss://{$str}@{$node['address']}:{$node['port']}#{$name}\r\n";
    }
    return base64_encode($url);
}

/**
 * @param $data
 * @param $user
 * @return array
 */
function sip008_generate($data, $user){
    foreach ($data as $nodes){
        $node_info[] = array(
            'id' => $nodes['id'],
            "remarks" => $nodes['node_name'],
            "server" => $nodes['address'],
            "server_port" => $nodes['port'],
            "password" => $user['uuid'],
            "method" => $nodes['node_method']
        );
    }
    $result = array(
        'version' => 1,
        'servers' => $node_info,
        'bytes_used' => $user['u'] + $user['d'],
        'bytes_remaining' => $user['bandwidth'] - ($user['u'] + $user['d'])
    );
    return $result;
}
function ss_generate($data, $user){

    foreach ($data as $nodes){
        $user_info = base64_encode($nodes['node_method'].":".$user['uuid']);
        $node_url .= "ss://".$user_info."@".$nodes['address'].":".$nodes['port']."#".rawurlencode($nodes['node_name'])."\n";
    }
    return base64_encode($node_url);

}

function convert_byte($size, $digits=2){
    $unit= array('','K','M','G','T','P');
    $base= 1024;
    $i = floor(log($size,$base));
    $n = count($unit);
    if($i >= $n){
        $i=$n-1;
    }
    return round($size/pow($base,$i),$digits).' '.$unit[$i] . 'B';
}