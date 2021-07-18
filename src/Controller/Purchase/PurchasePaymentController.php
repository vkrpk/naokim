<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Manager\PurchaseManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentController extends AbstractController
{
    /**
     * @Route("/purchase/payment/{id}", name="payment", methods={"GET", "POST"})
     * @param Purchase $purchase
     * @return Response
     */
    public function payment(Purchase $purchase, PurchaseManager $purchaseManager): Response
    {
        if (!$purchase ||
        ($purchase && $purchase->getUser() !== $this->getUser()) ||
        ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)) {
            return $this->redirectToRoute('cart_show');
        }
        return $this->render('purchase/payment.html.twig', [
            'user' => $this->getUser(),
            'intentSecret' => $purchaseManager->intentSecret($purchase),
            'purchase' => $purchase
        ]);
    }

    /**
     * @Route("/user/subscription/{id}/paiement/load", name="subscription_paiement", methods={"GET", "POST"})
     * @param Purchase $purchase
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function subscription(
        Purchase $purchase,
        Request $request,
        PurchaseManager $purchaseManager
    ){
        $user = $this->getUser();

        if($request->getMethod() === "POST") {
            $resource = $purchaseManager->stripe($_POST, $purchase);
            if(null !== $resource) {
                $purchaseManager->purchase_new($resource, $purchase, $user);
                return $this->redirectToRoute('purchase_payment_success', [
                    'id' => $purchase->getId(),
                    'intentSecret' => $purchaseManager->intentSecret($purchase)
                ]);
            }
        }
        return $this->redirectToRoute('payment', ['id' => $purchase->getId()]);
    }
}