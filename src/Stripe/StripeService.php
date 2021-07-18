<?php

namespace App\Stripe;

use App\Entity\Purchase;
use App\Cart\CartService;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class StripeService
{
    protected $secretKey;

    public function __construct() {
        if($_ENV['APP_ENV'] === 'dev') {
            $this->secretKey = $_ENV['STRIPE_SECRET_KEY_DEV'];
        } else {
            $this->secretKey = $_ENV['STRIPE_SECRET_KEY_PROD'];
        }
    }

    public function getPaymentIntent(Purchase $purchase)
    {
        \Stripe\Stripe::setApiKey($this->secretKey);

        return \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => Purchase::DEVISE,
            'payment_method_types' => ['card']
        ]);
    }

    public function payment($amount, $currency, $description, array $stripeParameter)
    {
        \Stripe\Stripe::setApiKey($this->secretKey);

        $payment_intent = null;

        if (isset($stripeParameter['stripeIntentId'])) {
            $payment_intent = \Stripe\PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
        }

        if ($stripeParameter['stripeIntentStatus'] === 'succeeded') {
            // TODO
        } else {
            $payment_intent->cancel();
        }

        return $payment_intent;
    }

    public function stripe(array $stripeParameter, Purchase $purchase)
    {
        return $this->payment(
            $purchase->getTotal(),
            Purchase::DEVISE,
            $purchase->getReference(),
            $stripeParameter
        );
    }
}