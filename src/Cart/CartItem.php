<?php 

namespace App\Cart;

use App\Entity\Product;
use App\Entity\Service;

class CartItem 
{
    public $item;
    public $qty;

    public function __construct(Object $item, int $qty)
    {
        $this->item = $item;
        $this->qty = $qty;
    }

    public function getTotal(): int
    {
        return $this->item->getPrice() * $this->qty;
    }
}