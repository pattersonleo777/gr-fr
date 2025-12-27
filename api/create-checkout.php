<?php
session_start();
require '../../vendor/autoload.php'; // Ensure stripe-php is installed via composer
\Stripe\Stripe::setApiKey('sk_live_your_actual_key');

$checkout_session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  'line_items' => [[
    'price' => 'price_your_actual_id',
    'quantity' => 1,
  ]],
  'mode' => 'subscription',
  'success_url' => 'https://godsrods.online/profile.php?session_id={CHECKOUT_SESSION_ID}',
  'cancel_url' => 'https://godsrods.online/index.php',
]);

header("Location: " . $checkout_session->url);
?>
