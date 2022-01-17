<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{
    protected $em;
    protected $cartService;

    public function __construct(EntityManagerInterface $em, CartService $cartService)
    {
        $this->em = $em;
        $this->cartService = $cartService;
    }

    #[Route('/purchase/terminate/{id}', name: 'purchase_payment_success')]
    #[IsGranted('ROLE_USER')]
    public function success($id, PurchaseRepository $purchaseRepository): Response
    {
        // 1. Je récupère la commande
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase || 
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
            ) {
            $this->addFlash("warning", "La commande n'existe pas !");
            return $this->redirectToRoute('purchase_index');
        }

        // 2. Je la fais passer au statut PAYEE (PAID)
        $purchase->setStatus(Purchase::STATUS_PAID);
        $this->em->flush();

        // 3. Je vide la panier
        $this->cartService->empty();

        // 4. Je redirige avec un flash vers la liste des commandes
        $this->addFlash("success", "La commande a été payée et confirmée");
        return $this->redirectToRoute('purchase_index');
    }
}