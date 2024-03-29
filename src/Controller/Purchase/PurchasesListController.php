<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasesListController extends AbstractController
{
    #[Route('/purchases', name: 'purchase_index')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à vos commandes')]
    public function index(): Response
    {
        // 1. Nous devons nous assurer que la personne est connectée sinon redirection vers la page d'accueil
        /** @var User */
        $user = $this->getUser();

        // if (!$user) {
        //     // $url = $this->routerInterface->generate('homepage');
        //     // return new RedirectResponse($url);
        //     throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes");
        // }
        // 2. Nous voulons savoir QUI est connecté

        // 3. Nous voulons passé l'utilisateur à twig afin d'afficher ses commandes
        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}