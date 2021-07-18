<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\Purchase;
use App\Stripe\StripeService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseManager extends AbstractController
{
    private $stripeService;
    private $em;

    public function __construct(StripeService $stripeService, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->stripeService = $stripeService;
    }

    public function intentSecret(Purchase $purchase) {
        $intent = $this->stripeService->getPaymentIntent($purchase);
        return $intent['client_secret'] ?? null;
    }

    public function stripe(array $stripeParameter, Purchase $purchase)
    {
        $ressource = null;
        $data = $this->stripeService->stripe($stripeParameter, $purchase);
        if ($data) {
            $ressource = [
                'stripeBrand' => $data['charges']['data'][0]['payment_method_details']['card']['brand'],
                'stripeLast4' => $data['charges']['data'][0]['payment_method_details']['card']['last4'],
                'stripeId' => $data['charges']['data'][0]['id'],
                'stripeStatus' => $data['charges']['data'][0]['status'],
                'stripeToken' => $data['client_secret']
            ];
        }
        return $ressource;
    }

    public function purchase_new(array $ressource, Purchase $purchase, User $user)
    {
        $purchase->setUser($user);
        $purchase->setTotal($purchase->getTotal());
        $purchase->setStatus($purchase->getStatus());
        $purchase->setPurchasedAt(new \DateTime());
        $purchase->setReference(uniqid('', false));
        $purchase->setBrandStripe($ressource['stripeBrand']);
        $purchase->setLast4Stripe($ressource['stripeLast4']);
        $purchase->setIdChargeStripe($ressource['stripeId']);
        $purchase->setStripeToken($ressource['stripeToken']);
        $purchase->setStatusStripe($ressource['stripeStatus']);

        $this->em->persist($purchase);
        $this->em->flush();
    }
}