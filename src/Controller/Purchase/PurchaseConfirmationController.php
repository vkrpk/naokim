<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{
    private $cartService;
    private $em;
    private $persister;

    public function __construct(CartService $cartService, EntityManagerInterface $em, PurchasePersister $persister) {
        $this->cartService = $cartService;
        $this->em = $em;
        $this->persister = $persister;
    }
    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour effectuer une commande")
     */
    public function confirm(Request $request) {
        $cartItems = $this->cartService->getDetailedCartItems();
        if (count($cartItems) === 0) {
            $this->addFlash("warning", 'Vous ne pouvez pas effectuer une commande avec un panier vide');
            return $this->redirectToRoute('cart_show');
        }
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            /** @var Purchase */
            $purchase = $form->getData();
            $this->persister->storePurchase($purchase);
            $this->em->flush();
            return $this->redirectToRoute('payment', [
                'id' => $purchase->getId()
            ]);
        }
        return $this->render('purchase/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}