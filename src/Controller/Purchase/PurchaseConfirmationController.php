<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseConfirmationController extends AbstractController
{
    protected $em;
    protected $cartService;

    public function __construct(EntityManagerInterface $em, CartService $cartService)
    {
       $this->em = $em; 
       $this->cartService = $cartService;
    }

    #[Route('/purchase/confirm', name: 'purchase_confirm')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour confirmer une commande')]
    public function confirm(Request $request): Response
    {
        // 1. Nous voulons lire les données du formulaire
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        // 2. Si le formulaire n'est pas soumis : redirection
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }

        // 3. Si l'utilisateur n'est pas connecté : redirection
        $user = $this->getUser();

        // 4. S'il n'y a pas de produits dans le panier : redirection (CartService)
        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
            return $this->redirectToRoute('cart_show');
        }

        // 5. Nous allons créer une Purchase
        /** @var Purchase */
        $purchase = $form->getData();

        // 6. Nous allons la lier avec l'utilisateur actuellement connecté
        $purchase->setUser($user)
                ->setPurchasedAt(new DateTime())
                ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService)
        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                        ->setProduct($cartItem->product)
                        ->setProductName($cartItem->product->getName())
                        ->setQuantity($cartItem->qty)
                        ->setTotal($cartItem->getTotal())
                        ->setProductPrice($cartItem->product->getPrice());

            $this->em->persist($purchaseItem);
        }

        // 8. Enregistrement de la commande
        $this->em->flush();

        $this->cartService->empty();

        $this->addFlash('success', 'La commande a bien été enregistrée');
        return $this->redirectToRoute('purchase_index');
    }
}