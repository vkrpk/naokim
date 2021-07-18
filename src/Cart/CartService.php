<?php 

namespace App\Cart;

use App\Repository\ProductRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService extends AbstractController
{
    private $session;
    private $requestStack;
    private $productRepository;
    private $serviceRepository;

    public function __construct(SessionInterface $session, RequestStack $requestStack, ProductRepository $productRepository, ServiceRepository $serviceRepository)                             
    {
        $this->session = $session;
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
        $this->serviceRepository = $serviceRepository;
    }

    protected function getCart(): array {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart) {
        return $this->session->set('cart', $cart);
    }

     public function isExistingItem(int $id, string $table): bool {
        $repository = $table . 'Repository';
        try {
            $item = $this->$repository->find($id);
        } catch (\ErrorException $e) {
            throw $this->createNotFoundException("Erreur");
        }
        if(!$item) {
            throw $this->createNotFoundException("L'objet $id dans la table $repository n'existe pas");
            return false;
        } else return true;
    }
    

    public function add(int $id, string $table) {
        if(!$this->isExistingItem($id, $table)) {
           throw $this->createNotFoundException("Not found");
        } else {
            $cart = $this->getCart();
            if(!array_key_exists($table . '_' . $id, $cart)) {
                $cart[$table . '_' . $id] = 0;
            } 
            if ($table === 'product') {
                $cart[$table . '_' . $id]++;
            } else {
                $cart[$table . '_' . $id] = 1;
            }
            $this->saveCart($cart);
        }
    }

    public function getTotal(): int {
        $total = 0;
        foreach ($this->getCart() as $id => $qty) {
            $repository = explode('_', $id);
            $repositoryName = $repository[0] . 'Repository';
            $item = $this->$repositoryName->find($repository[1]);
            if(!$item) {
                continue;
            }
            $total += $item->getPrice() * $qty;
        }
        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array {
        $detailedCart = [];
        foreach ($this->getCart() as $id => $qty) {
            $repository = explode('_', $id);
            $repositoryName = $repository[0] . 'Repository';
            $item = $this->$repositoryName->find($repository[1]);
            if(!$item) {
                continue;
            }
            $detailedCart[] = new CartItem($item, $qty);
        }
        return $detailedCart;
    }

    public function remove(string $key) {
        $cart = $this->getCart();
        unset($cart[$key]);
        $this->saveCart($cart);
    }

    public function decrement(string $key) {
        $cart = $this->getCart();
        if (!array_key_exists($key, $cart)) {
            return;
        }
        if ($cart[$key] === 1) {
            $this->remove($key);
            return;
        }
        $cart[$key]--;
        $this->saveCart($cart);
    }

    public function empty() {
        $this->saveCart([]);
    }
}