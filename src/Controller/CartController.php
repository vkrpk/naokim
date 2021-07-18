<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ServiceRepository;
use App\Cart\CartService;
use App\Form\CartConfirmationType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class CartController extends AbstractController
{
    private $productRepository;
    private $serviceRepository;
    private $cartService;
    private $translator;
    private $requestStack;

    public function __construct(CartService $cartService, TranslatorInterface $translator,
    ProductRepository $productRepository, ServiceRepository $serviceRepository, RequestStack $requestStack)
    {
        $this->productRepository = $productRepository;
        $this->serviceRepository = $serviceRepository;
        $this->translator = $translator;
        $this->cartService = $cartService;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id"="\d+"})
     */
    public function add($id)
    {
        $table = $this->requestStack->getCurrentRequest()->query->get('table');
        $this->cartService->add($id, $table);
        $repositoryTrans = $this->translator->trans($table);
        $this->addFlash('success', "Le $repositoryTrans a bien été ajouté au panier");
        $referer = $this->requestStack->getCurrentRequest()->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show()
    {
        $form = $this->createForm(CartConfirmationType::class);
        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();
        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id": "\d+"})
     */
    public function delete($id) {
        $table = $this->requestStack->getCurrentRequest()->query->get('table');
        $repository = $table . 'Repository';
        $item = $this->$repository->find($id);
        if (!$item) {
            throw $this->createNotFoundException("L'article n'existe pas et ne peut pas être supprimé");
        }
        $key = $table . '_' . $item->getId();
        $this->cartService->remove($key);
        $repositoryTrans = $this->translator->trans($table);
        $this->addFlash("success", "Le $repositoryTrans a bien été supprimé du panier");
        $referer = $this->requestStack->getCurrentRequest()->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id": "\d+"})
     */
    public function decrement($id){
        $table = $this->requestStack->getCurrentRequest()->query->get('table');
        $repository = $table . 'Repository';
        $item = $this->$repository->find($id);
        if (!$item) {
            throw $this->createNotFoundException("L'article n'existe pas et ne peut pas être décrémenté");
        }
        $key = $table . '_' . $item->getId();
        $this->cartService->decrement($key);
        $repositoryTrans = $this->translator->trans($table);
        $this->addFlash("success", "Le $repositoryTrans a bien été retiré");
        $referer = $this->requestStack->getCurrentRequest()->headers->get('referer');
        return $this->redirect($referer);
    }

}
