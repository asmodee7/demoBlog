<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{


    // Chaque méthode du controller est associé a une route bien spécifique.
    // Lorsque nous envoyons la route '/blog' dans l'URL du navigateur,
    // Cela execute automatiquement dans le controller la méthode associé a celle-ci.
    // Chaque méthode renvoi un template sur le navigateur en fonction de la route transmise
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        // $repo = $this->getDoctrine()->getRepository(Article::class); // Injection de dépendance dans public function index()
        $articles = $repo->findAll();
        // // dump($articles);


        return $this->render(
            'blog/index.html.twig',
            [
                'articles' => $articles
            ]
        );
    }
    /**
     * @Route("/", name = "home")
     *  
     */

    public function home(): Response
    {
        return $this->render(
            'blog/home.html.twig',
            [
                'title' => 'Bienvenue sur mon blog Symfony',
                'age' => 30
            ]
        );
    }    
    /**
     * @Route("/blog/new", name = "blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */

    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)

    {
            if (!$article) 
            {                
                $article = new Article;

                // Ajout Automatique d'un texte dans les input

                $article ->setTitle("Le passage de Lorem Ipsum standard, utilisé depuis 1500")
                         ->setContent("Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.")
                         ->setImage("https://picsum.photos/200/300?random=1");

            }



        // $form = $this->createFormBuilder($article)
        //              ->add('title')
        //              ->add('content')
        //              ->add('image')
        //              ->getForm();

        $form= $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        dump($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            if (!$article->getId()) 
            {
                
                $article->setCreatedAt(new \dateTime());
            }
            
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', 
            [
                         'id'=> $article->getId()
            ]);
        }

        return $this->render('blog/create.html.twig', 
        [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !==null
        ]);
    }

    /**
     * @Route("/blog/{id}", name = "blog_show")
     *  
     */

    public function show(Article $article,Request $request, EntityManagerInterface $manager): Response
    {
        //// $repo = $this->getDoctrine()->getRepository(Article::class); // Injection de dépendance dans public function index() + $id 
        //// $article = $repo->find($id);
        // // dump($article);

        $comment = new Comment;
        //// dump($request);
        //// $user = $this->getUser();
        ////     dump($user);

        $formComment = $this->createForm(CommentType::class,$comment);
        $formComment->handleRequest($request);
        if ($formComment->isSubmitted() && $formComment-> isValid()) 
        {
            $username = $this->getUser()->getUsername();
            dump($username);

            $comment->setAuthor($username);
            $comment-> setCreatedAt(new \DateTime);
            $comment->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('Success', 'le commentaire a bien été posté');

            return $this->redirectToRoute('blog_show', [
                'id'=>$article->getId()
            ]);
        }

        return $this->render(
            'blog/show.html.twig',
            [
                'article' => $article,
                'formComment' => $formComment->createView()
            ]
        );

        

    }
}
