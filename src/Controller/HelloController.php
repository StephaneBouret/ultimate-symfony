<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    /**
     * @Route("/hello/{name?World}", name="hello")
     */
    public function hello(Request $request, $name) {

        return $this->render('hello.html.twig', [
            'prenom' => $name
        ]);
    }
}
