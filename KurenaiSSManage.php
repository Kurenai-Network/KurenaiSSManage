<?php
/*
 * Copyright (c) 2022. Kurenai Network
 * 项目名称:KurenaiSSManage
 * 文件名称:KurenaiSSManage.php
 * Date:2022/1/1 下午6:55
 * Author: Kurenai Network
 */

require_once 'config.php';
require 'api/database.php';
require 'vendor/autoload.php';
require_once 'vendor/philipp15b/php-i18n/i18n.class.php';
use Ramsey\Uuid\Uuid;
use WHMCS\Database\Capsule;

//初始化i18n
class whmcs_i18n extends i18n{
    public function getUserLangs() {
        $userLangs = array();
        $userLangs[] = $GLOBALS[_LANG]['locale'];
        return $userLangs;

    }
}
$i18n = new whmcs_i18n(__DIR__.'/lang/lang_{LANGUAGE}.ini', __DIR__.'/vendor/philipp15b/php-i18n/langcache/');
try {
    $i18n->init();
} catch (Exception $e) {
    echo "Uninitialized i18n: ".$e;
}
//whmcs access
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function KurenaiSSManage_MetaData(){
    return array(
        'DisplayName' => 'KurenaiSSManage',
        'APIVersion' => '1.0',
        'RequiresServer' => true
    );
}

function KurenaiSSManage_ConfigOptions(){
    return array(
        L::admin_database => array('Type' => 'text', 'Size' => '25'),
        L::admin_reset_strategy => array(
            'Type'        => 'dropdown',
            'Options'     => array('3' => L::admin_end_of_month, '2' => L::admin_start_of_month, '1' => L::admin_order_date, '0' => L::admin_no_reset)
        ),
        L::admin_node_list => array(
            'Type' => 'textarea',
            'Rows' => '3',
            'Cols' => '50',
            'Description' => L::admin_node_list
        ),
        L::admin_bandwidth => array(
            'Type' => 'text',
            'Size' => '25',
            'Description' => 'Byte'
        ),
        L::admin_manual_reset_bandwidth_option => array(
            'Type' => 'dropdown',
            'Options' => array('1' => L::common_allow, '0' => L::common_prohibit)
        ),
        L::admin_reset_bandwidth_cost_percentage => array(
            'Type' => 'text',
            'Size' => '25'
        ),
        L::admin_node_group_id => array(
            'Type' =>'text',
            'Size' => '25'
        )
    );
}

function KurenaiSSManage_TestConnection(array $params)
{

    try {
        $mysql_host = get_config()['mysql_host'];
        $mysql_user = get_config()['mysql_user'];
        $redis_pass = get_config()['redis_pass'];
        $redis_port = get_config()['redis_port'];
        $redis_host = get_config()['redis_host'];
        if (!empty($params['serverhostname'])) {
            $mysql_host = $params['serverhostname'];
        } else {
            if (!empty($params['serverip'])) {
                $mysql_host = $params['serverip'];
            } else {
                throw new Exception('Unable to get the database server address.');
            }
        }
        if (!empty($redis_host) && !empty($redis_port)){
            $redis = new Redis();
            $redis->connect($redis_host, $redis_port);
            if (!empty($redis_pass)){
                $redis->auth($redis_pass);
            }
        }else{
            throw new Exception('Unable to connect redis');
        }
        $mysql_user = $params['serverusername'];
        $mysql_pass = $params['serverpassword'];
        $mysql = new PDO('mysql:host=' . $mysql_host, $mysql_user, $mysql_pass);
        $success = true;
        $errorMsg = '';
    } catch (Exception $e) {
        logModuleCall('KurenaiSSManage', 'KurenaiSSManage_TestConnection', $params, $e->getMessage(), $e->getTraceAsString());
        $success = false;
        $errorMsg = $e->getMessage();
    }
    return array('success' => $success, 'error' => $errorMsg);
}

function KurenaiSSManage_CreateAccount($params){
    $pid = $params['serviceid'];
    $data = array(
        'email' => $params['clientsdetails']['email'],
        'uuid' => Uuid::uuid4(),
        'token' => $params['password'],
        'sid' => $params['serviceid'],
        'package_id' => $params['pid'],
        'telegram_id' => 0,
        'enable' => 1,
        'need_reset' => $params['configoption5'],
        'node_group_id' => $params['configoption7'],
        'bandwidth' => $params['configoption4']
    );

    $push = new_account($data);
    return $push;
}

function KurenaiSSManage_SuspendAccount($params){
    $sid = $params['serviceid'];
    $data = array(
        'sid' => $sid,
        'action' => 0,
    );
    $action = set_status($data);
    return $action;
}

function KurenaiSSManage_UnsuspendAccount($params){
    $sid = $params['serviceid'];
    $data = array(
        'sid' => $sid,
        'action' => 1,
    );
    $action = set_status($data);
    return $action;
}

function KurenaiSSManage_TerminateAccount(array $params){
    $sid = $params['serviceid'];
    $data = array(
        'sid' => $sid
    );
    $action = delete_account($data);
    return $action;
}

function KurenaiSSManage_ChangePackage(array $params){
    $sid = $params['serviceid'];
    $data = array(
        'sid' => $sid,
        'pid' => $params['pid'],
        'bandwidth' =>  $params['configoption4'],
        'node_group_id' => $params['configoption7']
    );
    $action = change_package($data);
    return $action;
}
function KurenaiSSManage_AdminCustomButtonArray(){
    return array(
        L::product_reset_bandwidth => 'reset_bandwidth_admin',
        L::product_reset_uuid => 'reset_uuid',
    );
}
function KurenaiSSManage_ClientArea($params){
    if (isset($_GET['KurenaiSSManageAction'])){
        switch ($_GET['KurenaiSSManageAction']){
            case 'ResetUUID':
                if (isset($_GET['sid']) && $_GET['sid'] == $params['serviceid']){
                    KurenaiSSManage_reset_uuid($params);
                }else{
                    $result = array(
                        'status' => 'fail',
                        'msg' => L::common_prohibit
                    );
                    echo json_encode($result);
                    die();
                }
                break;
            case 'ResetBandwidth':
                if (isset($_GET['sid']) && $_GET['sid'] == $params['serviceid']){
                    KurenaiSSManage_reset_bandwidth_user($params);
                }else{
                    $result = array(
                        'status' => 'fail',
                        'msg' => L::common_prohibit
                    );
                    echo json_encode($result);
                    die();
                }
                break;
        }
    }
    $user = get_user($params['serviceid']);
    if ($params['status'] == 'Active') {
        $user_traffic_total = $user[0]['u'] + $user[0]['d'];
        $user_traffic_upload = $user[0]['u'];
        $user_traffic_download = $user[0]['d'];
        $bandwidth = $user[0]['bandwidth'];
        $left = $user[0]['bandwidth'] - $user_traffic_total;
        $uuid = $user[0]['uuid'];
        $telegram_id = $user[0]['telegram_id'];
        $sid = $user[0]['sid'];
        $created_at = $user[0]['created_at'];
        $token = $user[0]['token'];
        $info = array(
            'uuid' => $uuid,
            'upload' => convert_byte($user_traffic_upload),
            'download' => convert_byte($user_traffic_download),
            'total_used' => convert_byte($user_traffic_total),
            'left' => convert_byte($left),
            'created_at' => $created_at,
            'telegram_id' => $telegram_id,
            'bandwidth' => convert_byte($bandwidth),
            'sid' => $sid,
            'token' => $token
        );
        return array(
            'tabOverviewReplacementTemplate' => 'details.tpl',
            'templateVariables' => array(
                'user' => $info,
                'node' => get_nodes($params['serviceid']),
                'subscribe_url' => get_config()['subscribe_url'],
            ),
        );
    }else{
        return array(
            'tabOverviewReplacementTemplate' => 'error.tpl',
            'templateVariables' => array(
                'error' => L::error_account_not_found,
            ),
        );
    }


}
function KurenaiSSManage_reset_bandwidth_user(array $params){
    if ($params['configoption5'] != 0){
        $client_product = get_client_products($params['serviceid']);
        if ($client_product['result'] = 'success'){
            $product = $client_product['products']['product'][0];
            if ($product['status'] = 'Active'){
                $amount = $product['recurringamount'];
                $cost = $params['configoption6'] * $amount;
                if ($cost > 0){
                    $data = array(
                        'clientid' => $params['userid'],
                        'description' => 'reset traffic fee by'.$params['userid'],
                        'amount' => (float)($cost),
                        'type' => 'remove',
                    );
                    $result = set_credit($data);
                }else{
                    $reset = array(
                        'sid' => $params['serviceid'],
                        'u' => 0,
                        'd' => 0,
                    );
                    set_bandwidth($reset);
                    $echo = array(
                        'status' => 'success',
                        'msg' => L::product_reset_bandwidth_success
                    );
                    echo json_encode($echo);
                    die();
                }

                if ($result['result'] == 'success'){
                    $reset = array(
                        'sid' => $params['serviceid'],
                        'u' => 0,
                        'd' => 0,
                    );
                    set_bandwidth($reset);
                    $echo = array(
                        'status' => 'success',
                        'msg' => L::product_reset_bandwidth_success
                    );
                    echo json_encode($echo);
                    die();
                }else{
                    $echo = array(
                        'status' => 'fail',
                        'msg' => L::product_reset_bandwidth_error
                    );
                    echo json_encode($echo);
                    die();
                }
            }
        }
    }
}

function KurenaiSSManage_reset_bandwidth_admin(array $params){
    try {
        $data = array(
            'sid' => $params['serviceid'],
            'u' => 0,
            'd' => 0,
        );
        $action = set_bandwidth($data);
        if ($action){
            return L::product_reset_bandwidth_success;
        }else{
            return L::product_reset_bandwidth_error;
        }
    } catch (Exception $e){
        return $e;
    }
}

function KurenaiSSManage_reset_uuid(array $params){
    $sid = $params['serviceid'];
    $data = array(
        'sid' => $sid,
        'uuid' => Uuid::uuid4()
    );
    $action = reset_uuid($data);

    if (!$action){
        echo $action->errorInfo();
    }
    $result = array(
        'status' => 'success',
        'msg' => L::product_reset_uuid_success
    );
    echo json_encode($result);
    die();
}
function KurenaiSSManage_AdminServicesTabFields(array $params){
    try {
        $user = get_user($params['serviceid'])[0];
        $result = array(
            'uuid' => $user['uuid'],
            L::admin_bandwidth => convert_byte($user['bandwidth']),
            L::common_upload => convert_byte($user['u']),
            L::common_download => convert_byte($user['d']),
            L::common_left => convert_byte($user['bandwidth'] - ($user['u'] + $user['d'])),
            L::common_used => convert_byte($user['u'] + $user['d']),
            L::common_created_at => $user['created_at'],

        );
        return $result;
    } catch (Exception $e){
        return $e;
    }
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
