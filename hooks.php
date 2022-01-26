<?php
/*
 * Copyright (c) 2022. Kurenai Network
 * 项目名称:KurenaiSSManage
 * 文件名称:hook.php
 * Date:2022/1/1 下午6:55
 * Author: Kurenai Network
 */
use WHMCS\Database\Capsule;
use Carbon\Carbon;
add_hook('AfterCronJob', 1, function ($vars) {
    require 'api/database.php';
    try {
        check_traffic();
        $adminUser = '';//Administrator username
        $command = 'GetOrders';
        $postData = [
            'limitnum' => 10000,
            'status' => 'Pending'
        ];
        $results = localAPI($command, $postData, $adminUser);
        if (isset($results['orders']['order'])) {
            foreach ($results['orders']['order'] as $order) {
                if ($order['paymentstatus'] === 'Paid' || $order['amount'] === '0.00') {
                    $command = 'AcceptOrder';
                    $postData = [
                        'orderid' => $order['id']
                    ];
                    $results = localAPI($command, $postData, $adminUser);
                    if ($results['result'] !== 'success') {
                        $postData = array(
                            'description' => 'Accept order failed #' . $order['id'],
                        );
                        $command = 'LogActivity';
                        localAPI($command, $postData, $adminUser);
                    }
                }
            }
        }
    }catch (Exception $e){
        file_put_contents('after_cron_log.txt',var_export($e,true), FILE_APPEND);
    }
    
    
});

add_hook('DailyCronJob', 1, function($vars) {
    require 'api/database.php';
    try {
        reset_traffic_month();
    }catch (Exception $e){
        file_put_contents('after_cron_log.txt',var_export($e,true), FILE_APPEND);
    }
});

add_hook('ClientAreaPageProductDetails', 1, function($var)
{
	$email_ca = new WHMCS_ClientArea();
	if ($email_ca->isLoggedIn()) {
		$result = Capsule::table('tblusers')->where('id', '=', $email_ca->getUserID())->get()[0]->email_verified_at;
		if ($result == NULL) { 
			echo '<script>if(confirm("You have not verified your email, access is denied!\nPlease click confirm to back to homepage and verify email!\nPlease click cancel to modify account information!")){location.href=\'clientarea.php\';}else{location.href=\'clientarea.php?action=details\';};</script>';
			exit();
		}
	}
});
add_hook('DailyCronJob', 1, function ($vars) {
    $adminUser = '';//Administrator username
    $orderOverdueDays = 3;//Allow order overdue time(day)
    $invoiceOverdueDays = 3;//Allow bill overdue time(day)

    $now = Carbon::now();
    $now->hour = 0;
    $now->minute = 0;
    $now->second = 0;

    //Cancel order start
    $command = 'GetOrders';
    $postData = [
        'limitnum' => 10000,
        'status' => 'Pending'
    ];
    $results = localAPI($command, $postData, $adminUser);
    if (isset($results['orders']['order'])) {
        foreach ($results['orders']['order'] as $order) {
            $createDate = Carbon::parse($order['date']);
            $createDate->hour = 0;
            $createDate->minute = 0;
            $createDate->second = 0;
            if ($now->diffInDays($createDate) >= $orderOverdueDays) {
                $command = 'CancelOrder';
                $postData = [
                    'orderid' => $order['id']
                ];
                $results = localAPI($command, $postData, $adminUser);
                if ($results['result'] !== 'success') {
                    $postData = array(
                        'description' => 'Cancel order failed #' . $order['id'],
                    );
                    $command = 'LogActivity';
                    localAPI($command, $postData, $adminUser);
                }
            }
        }
    }
    //Cancel order end


    //Cancel invoices start
    $command = 'GetInvoices';
    $postData = [
        'limitnum' => 10000,
        'status' => 'Overdue'
    ];
    $results = localAPI($command, $postData, $adminUser);
    if (isset($results['invoices']['invoice'])) {
        foreach ($results['invoices']['invoice'] as $invoice) {
            $dueDate = Carbon::parse($invoice['duedate']);
            $dueDate->hour = 0;
            $dueDate->minute = 0;
            $dueDate->second = 0;
            if ($now->diffInDays($dueDate) >= $invoiceOverdueDays) {
                $command = 'UpdateInvoice';
                $postData = [
                    'invoiceid' => $invoice['id'],
                    'status' => 'Cancelled'
                ];
                $results = localAPI($command, $postData, $adminUser);
                if ($results['result'] === 'success') {
                    $postData = array(
                        'description' => 'Cancel invoice succeed #' . $invoice['id'],
                    );
                } else {
                    $postData = array(
                        'description' => 'Cancel invoice failed #' . $invoice['id'] . ';' . $results['message'],
                    );
                }
                $command = 'LogActivity';
                localAPI($command, $postData, $adminUser);
            }
        }
    }
    //Cancel invoices end
});