<?php
session_start();
require '../../vendor/autoload.php'; // Ensure stripe-php is installed via composer
\Stripe\Stripe::setApiKey('YOUR_STRIPE_SECRET_KEY');

$checkout_session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  'line_items' => [[
    'price' => 'PRICE_ID_FROM_STRIPE',
    'quantity' => 1,
  ]],
  'mode' => 'subscription',
  'success_url' => 'https://godsrods.online/profile.php?session_id={CHECKOUT_SESSION_ID}',
  'cancel_url' => 'https://godsrods.online/index.php',
]);

header("Location: " . $checkout_session->url);
?>
