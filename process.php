<?php
// Obtain test cards here: https://developer.flutterwave.com/docs/integration-guides/testing-helpers/
include __DIR__ . '/../lib/legacy_fields.php';

$currency = get_option('flutterwave_currency', 'payments') ?: 'USD';
$test_mode = get_option('flutterwave_testmode', 'payments') === 'y';
// \Log::info($mw_return_url);
$redirect_url = $mw_return_url;
// \Log::info($place_order['mw_payment_fields']);
// die;

// Check if test mode is enabled
if ($test_mode) {
    // Hardcoded test keys
    $public_key = 'FLWPUBK_TEST-f618760f51c799b4ce0ef2516b79e235-X';
    $secret_key = 'FLWSECK_TEST-f5d5d46097e14a775a381578f18ba648-X';
    $encryption_key = 'FLWSECK_TESTee5a907305fb';
} else {
    // Retrieve live keys from options
    $secret_key = get_option('flutterwave_secret_key', 'payments');
    $public_key = get_option('flutterwave_public_key', 'payments');
    // $encryption_key = get_option('flutterwave_encryption_key', 'payments');
}

// Ensure necessary data is set
if (!$public_key) {
    $place_order['error'] = 'Flutterwave Public Key is not set.';
    return;
}


// Get order details
$order_id = $place_order['id'];
$amount = $place_order['amount'];
$customer_email = $place_order['email'];
$customer_name = $place_order['first_name'] . ' ' . $place_order['last_name'];
$store_name = get_option('flutterwave_title', 'payments') ?? 'Online Store';

// Generate payment form
$gateway_url = $test_mode ? "https://api.flutterwave.com/v3/payments" : "https://api.flutterwave.com/v3/payments";
$tx_ref = uniqid("TXREF_");

$post_data = array(
    'tx_ref' => $tx_ref,
    'amount' => $amount,
    'currency' => $currency,
    'redirect_url' => $redirect_url,
    'payment_options' => 'card, mobilemoney, ussd',
    'customer' => array(
        'email' => $customer_email,
        'name' => $customer_name
    ),
    'customizations' => array(
        'title' => $store_name,
        'description' => 'Payment for order #' . $order_id,
        'failure_url' => 'test.com',
    )
);

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($post_data),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer FLWSECK_TEST-f5d5d46097e14a775a381578f18ba648-X', // hard code for testing
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);

// echo $response;
// exit;

curl_close($curl);

$res = json_decode($response);
if ($res->status == 'success') {
    $html = _e('Thank you for your order', true);

    if (get_option('flutterwave_show_msg', 'payments') == 'y') {
        $html .= '<br />' . get_option('flutterwave_msg', 'payments');
    }

    // $place_order['order_completed'] = 1;
    // $place_order['is_paid'] = 0;
    // $place_order['success'] = $html;

    $link = $res->data->link;
    $place_order['redirect'] = $link;
} else {
    $place_order['error'] = $response;
}


// $place_order['order_completed'] = 1;
// $place_order['is_paid'] = 0;
// $place_order['success'] =  _e('Thank you for your order',true);

// Sample Success Response
$succes_response = '{
    "status": "success",
    "message": "Hosted Link",
    "data": {
        "link": "https://checkout-v2.dev-flutterwave.com/v3/hosted/pay/fd33c636b0d38239527d"
    }
}';

$success_verify_transaction_response = '
{
    "status": "success",
    "message": "Transaction fetched successfully",
    "data": {
        "id": 8258122,
        "tx_ref": "TXREF_6756ddaa6a16d",
        "flw_ref": "FLW-MOCK-dd9d321e912ecdcb5e59485012db6ebf",
        "device_fingerprint": "1ef353abd2babf1a9f02cac6bed837d7",
        "amount": 1,
        "currency": "USD",
        "charged_amount": 1,
        "app_fee": 0.04,
        "merchant_fee": 0,
        "processor_response": "Approved. Successful",
        "auth_model": "VBVSECURECODE",
        "ip": "52.209.154.143",
        "narration": "CARD Transaction ",
        "status": "successful",
        "payment_type": "card",
        "created_at": "2024-12-09T12:09:13.000Z",
        "account_id": 1268257,
        "card": {
            "first_6digits": "553188",
            "last_4digits": "2950",
            "issuer": " CREDIT",
            "country": "NIGERIA NG",
            "type": "MASTERCARD",
            "token": "flw-t1nf-414a2ab8233d6b17c1da6f03ea2c2a2d-m03k",
            "expiry": "01/27"
        },
        "meta": {
            "__CheckoutInitAddress": "https://checkout-v2.dev-flutterwave.com/v3/hosted/pay"
        },
        "amount_settled": 0.96,
        "customer": {
            "id": 2554952,
            "name": "Edward Miss",
            "phone_number": "N/A",
            "email": "edwardmuss5@gmail.com",
            "created_at": "2024-12-09T12:09:13.000Z"
        }
    }
}';
