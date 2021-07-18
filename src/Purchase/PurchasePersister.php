<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    private $security;
    private $cartService;
    private $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em) {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Purchase $purchase) {
        $purchase
            ->setUser($this->security->getUser())
            ->setReference('', false)
            ->setPurchasedAt(new \DateTime())
            ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        foreach($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $table = $cartItem->item->getEntity();
            $purchaseItem->setPurchase($purchase)
                ->setItemName($cartItem->item->getName())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setService($table === 'service' ? $cartItem->item : null)
                ->setproduct($table === 'product' ? $cartItem->item : null)
                ->setItemPrice($cartItem->item->getPrice())
            ;
            $this->em->persist($purchaseItem);
        }
    }
}