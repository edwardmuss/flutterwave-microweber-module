<?php
include __DIR__ . '/../lib/legacy_fields.php';

// Validate input
if (!isset($_REQUEST['transaction_id']) || empty($_REQUEST['transaction_id'])) {
    \Log::info('Transaction ID is missing or empty.');
    $update_order['is_paid'] = 0;
    $update_order['order_completed'] = 0;
    // header("Location: $mw_cancel_url");
    return;
}

$transaction_id = $_REQUEST['transaction_id'];
$test_mode = get_option('flutterwave_testmode', 'payments') === 'y';
$secret_key = $test_mode
    ? 'FLWSECK_TEST-f5d5d46097e14a775a381578f18ba648-X'
    : get_option('flutterwave_secret_key', 'payments');

// Verify transaction
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
    \Log::info('cURL Error: ' . $err);
    return;
}

$response_data = json_decode($response, true);

if (!$response_data) {
    \Log::info('Invalid response data.');
    return;
}

// Log response
\Log::info($response);

if ($response_data['status'] === 'success') {
    \Log::info('Transaction successful.');

    $order_id = $_REQUEST['order_id'] ?? null;
    if (!$order_id) {
        \Log::info('Order ID is missing.');
        return;
    }

    $query = array(
        'id' => $order_id,
        'payment_verify_token' => $_REQUEST['payment_verify_token'] ?? '',
        'single' => true,
    );
    $order = mw()->shop_manager->get_orders($query);

    if ($order) {
        $update_order = [
            'transaction_id' => $transaction_id,
            'payment_amount' => $response_data['data']['amount'] ?? 0,
            'payment_status' => 'completed',
            'payment_email' => $response_data['data']['customer']['email'] ?? null,
            'is_paid' => 1,
            'order_completed' => 1,
            'success' => 'Your payment was successful! Transaction ID: ' . ($response_data['data']['id'] ?? 'N/A'),
        ];

        // Update order logic goes here, e.g., mw()->shop_manager->save_order($update_order);
        \Log::info('Order updated: ' . json_encode($update_order));
    } else {
        \Log::info('Order not found for ID: ' . $order_id);
    }
} elseif ($response_data['status'] === 'cancelled' || ($_REQUEST['status'] ?? '') === 'cancelled') {
    \Log::info('Transaction cancelled.');
    $place_order['redirect'] = $mw_cancel_url ?? site_url('shop/cart');
} elseif ($response_data['status'] === 'error') {
    \Log::info('Transaction error: ' . json_encode($response_data));
    $place_order['redirect'] = $mw_cancel_url ?? site_url('shop/cart');
} else {
    \Log::info('Unhandled transaction status: ' . $response_data['status']);
}

// Final log for debugging
\Log::info($place_order);
