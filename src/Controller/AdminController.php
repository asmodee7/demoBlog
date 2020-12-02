<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/articles", name="admin_articles")
     */
    public function adminArticles(EntityManagerInterface $manager, ArticleRepository $repo): Response
    {
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();
        // dump($colonnes);

        $articles = $repo->findAll();
        dump($articles);


        return $this->render('admin/admin_articles.html.twig', [
            'colonnes' => $colonnes,
            'articles' => $articles
        ]);
    }
    /**
     * @Route("/admin/category", name="admin_category")
     */
    public function adminCategory(): Response
    {
        return $this->render('admin/admin_category.html.twig');
    }
    /**
     * @Route("/admin/comment", name="admin_comment")
     */
    public function admincomment(): Response
    {
        return $this->render('admin/admin_comment.html.twig');
    }
    /**
     * @Route("/admin/membre", name="admin_membre")
     */
    public function adminmembre(): Response
    {
        return $this->render('admin/admin_membre.html.twig');
    }
    /**
     * @Route("/admin/article/new", name="admin_new_article")
     * @Route("/admin/{id}/edit-article", name="admin_edit_article")
     */
    public function adminForm(Request $request, EntityManagerInterface $manager, Article $article = null): Response
    {

        /*
            1. Importer le form de création d'articles (form/ArticleType).
            2. Transmettre le form à la méthode render & l'afficher dans le template admin_create.html.twig
            3. Faites de récupérer les données du formulaire & les afdficher dans un dump().
            4. Réaliser le traitement php permettant d'insert un nouvel article à la validation du form. 
            5. Executer une redirection après insertion vers l'affichage des articles dans le Back-Office      
            6. Afficher un message de validation        
        */
        if (!$article) {
            $article = new Article;
        }

        $formArticle = $this->createForm(ArticleType::class, $article);
        $formArticle->handleRequest($request);
        if ($formArticle->isSubmitted() && $formArticle->isValid()) {
            if (!$article->getId()) {

                $article->setCreatedAt(new \DateTime);
                $this->addFlash(
                    'success',
                    'L\'article a bien été enregistré !'
                    );
            }
            else
            {
                $this->addFlash(
                    'success',
                    'L\'article a bien été modifié !'
                );
            }
            $manager->persist($article);
            $manager->flush();


            return $this->redirectToRoute('admin_articles');
        }



        return $this->render('admin/admin_create.html.twig', [
            'formArticle' => $formArticle->createView()
        ]);
    }
}
