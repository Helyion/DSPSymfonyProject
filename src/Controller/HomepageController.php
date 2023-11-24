<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /*
        requête HTTP:
        - contenue dans une classe RequestStack
        - injection de dépendances : accéder à une classe dans une autre classe
        - dans symfony, l'injection de dépendances se fait par le constructeur
    */

    public function __construct(private RequestStack $requestStack)
    {}

    #[Route('/', name: 'homepage.index')]
    public function index(): Response
    {
        /*
            débogage :
                dump() : afficher la donnée dans la page
                dd() : afficher la donnée puis stop le script

            $this->requestStack->getMainRequest() : récupérer la requête HTTP exécutée par le PHP

            propriété de la requête
                - request : $_POST
                - query : $_GET
         */

        // récupération d'une donnée envoyée en $_POST
//        $post = $this->requestStack->getMainRequest()->get(('key'));
//        dd($post);
//        return new Response('{ "key" : "value" }', Response::HTTP_CREATED, ['content-type' => 'application/json']);

        // la clé du tableau associatif est le nom de la variable dans le template
        return $this->render('homepage/index.html.twig', [
            'my_array' => ['value0', 'value1', 'value2'],
            'assoc_array' => [
                'key0' => 'value0',
                'key1' => 'value1',
                'key2' => 'value2'
            ],
            'now' => new \DateTime(),
        ]);
    }

    #[Route('/hello/{name}', name: 'homepage.hello')]
    public function hello(string $name): Response
    {
        return $this->render('homepage/hello.html.twig', [
            'name' => $name
        ]);
    }
}