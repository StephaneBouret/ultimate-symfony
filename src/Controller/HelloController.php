<?php 

namespace App\Controller;

use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    private $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @Route("/hello/{name?World}", name="hello")
     */
    public function hello(Request $request, $name, Slugify $slugify) {
        dump($slugify->slugify("Hello World"));
        $tva = $this->calculator->calcul(100);
        dump($tva);
        return new Response("Hello $name");
    }
}
