<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    private $em;
    private $categoryRepository;

    public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepository)
    {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
    }

    public function renderMenuList(): Response
    {
        // 1. Aller chercher les catégories dans la BDD (pas de Repository dans les paramètres de la fonction car absence de route - uniquement par injection de dépendance)
        $categories = $this->categoryRepository->findAll();
        // 2. Renvoyer le rendu HTML sous la forme d'une Response ($this->render)
        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }
    
    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, SluggerInterface $slugger): Response
    {
        $category = new Category;

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // car DoctrineListener :
            // $category->setSlug(strtolower($slugger->slug($category->getName())));

            $this->em->persist($category);
            $this->em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/category/{id}/edit', name: 'category_edit')]
    // #[IsGranted('ROLE_ADMIN', message:"Vous n'avez pas le droit d'accéder à cette ressource")]
    public function edit($id, CategoryRepository $categoryRepository, Request $request): Response
    {
        // Méthode 1 pour interdire l'accès à une méthode
        // $user = $security->getUser();
        // $user = $this->getUser();

        // if ($this->isGranted("ROLE_ADMIN") === false) {
        //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette ressource");
        // }

        // Méthode 2 pour interdire l'accès à une méthode
        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Vous n\'avez pas le droit d\'accéder à cette ressource');

        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }
        // Méthode 1 pour autoriser à modifier les catégories en fonction du créateur de la catégorie
        // $user = $this->getUser();

        // if (!$user) {
        //     $this->redirectToRoute('security_login');
        // }

        // if ($user !== $category->getOwner()) {
        //     throw new NotFoundHttpException("Vous n'êtes pas le propriétaire de cette catégorie");
        // }

        // Méthode 2 pour autoriser à modifier les catégories en fonction du créateur de la catégorie
        // $this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous n'êtes pas le propriétaire de cette catégorie");

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }
}
