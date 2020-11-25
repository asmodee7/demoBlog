<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    

    // Chaque méthode du controller est associé a une route bien spécifique.
    // Lorsque nous envoyons la route '/blog' dans l'URL du navigateur,
    // Cela execute automatiquement dans le controller la méthode associé a celle-ci.
    // Chaque méthode renvoi un template sur le navigateur en fonction de la route transmise

    /**
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    /**
     * @Route("/", name = "home")
     *  
     */

    public function home(): Response
    {
        return $this->render('blog/home.html.twig',[
            'title'=>'Bienvenue sur mon blog Symfony',
            'age' => 30
        ]);
    }

    /**
     * @Route("/blog/12", name = "blog_show")
     *  
     */

    public function show(): Response
    {
        return $this->render('blog/show.html.twig');
    }
}
