<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    protected $requestStack;
    protected $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $session = $this->requestStack->getSession();
        return $session->set('cart', $cart);
    }

    public function empty()
    {
        $this->saveCart([]);
    }

    public function add(int $id)
    {
        // $session = $this->requestStack->getSession();

        // 1. Retrouver le panier dans la session et s'il n'existe pas, alors prendre un tableau vide
        // $cart = $session->get('cart', []);
        
        // Refactoring
        $cart = $this->getCart();
        // 2. Voir si le produit ($id) existe déjà dans le tableau, si c'est le cas, simplement augmenter la quantité, 
        // sinon ajouter le produit avec la quantité 1
        // if (array_key_exists($id, $cart)) {
        //     $cart[$id]++;
        // } else {
        //     $cart[$id] = 1;
        // }

        // Refactoring
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;

        // 3. Enregistrer le tableau dans la session
        // $session->set('cart', $cart);
        $this->saveCart($cart);
    }

    public function remove(int $id)
    {       
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();
        
        if (!array_key_exists($id, $cart)) {
            return;
        }

        // Soit le produit = 1, alors il faut le supprimer
        if ($cart[$id] === 1) {
            $this->remove($id);
        }
        // Soit le produit est à plus de 1, alors il faut décrementer
        $cart[$id]--;

        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $session = $this->requestStack->getSession();

        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }
            
            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $session = $this->requestStack->getSession();

        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);

            // $detailedCart[] = [
            //     'product' => $product,
            //     'qty' => $qty
            // ];
        }

        return $detailedCart;
    }
}