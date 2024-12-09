<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate input
if (!isset($_REQUEST['order_id']) || !isset($_REQUEST['transaction_id'])) {
    error_log('Missing order_id or transaction_id.');
    return;
}

$test_mode = get_option('flutterwave_testmode', 'payments') === 'y';
$secret_key = $test_mode ? 'FLWSECK_TEST-f5d5d46097e14a775a381578f18ba648-X' : get_option('flutterwave_secret_key', 'payments');

// Verify transaction
$transaction_id = $_REQUEST['transaction_id'];
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array("Authorization: Bearer $secret_key"),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    error_log('cURL Error: ' . $err);
    return;
}

$response_data = json_decode($response, true);

if (!$response_data || $response_data['status'] !== 'success' || $response_data['data']['status'] !== 'successful') {
    error_log('Transaction verification failed: ' . json_encode($response_data));
    return;
}

// Update order
$order_id = $_REQUEST['order_id'];
$query = array('id' => $order_id, 'single' => true);
$order = mw()->shop_manager->get_orders($query);

if (!$order) {
    error_log("Order not found for ID: $order_id");
    return;
}

$update_order = array(
    'transaction_id' => $response_data['data']['id'],
    'payment_amount' => $response_data['data']['amount'],
    'payment_currency' => $response_data['data']['currency'],
    'payment_email' => $response_data['data']['customer']['email'],
    'payment_name' => $response_data['data']['customer']['name'],
    'payment_status' => 'completed',
    'is_paid' => 1,
    'order_completed' => 1,
    'success' => 'Payment was successful! Transaction ID: ' . $response_data['data']['id'],
);

$result = mw()->shop_manager->save_order($update_order);
error_log('Order update result: ' . json_encode($result));
