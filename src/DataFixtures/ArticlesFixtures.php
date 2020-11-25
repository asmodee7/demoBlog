<?php

namespace App\DataFixtures;


use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticlesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i <= 10; $i++) 
        { 
            $article = new Article;

            $article->setTitle("Titre de l'article n°$i")
                    ->setContent("<p>Contenue de l'article n°$i</p>")
                    ->setImage("https://picsum.photos/200/300?random=$i")
                    ->setCreatedAt(new \Datetime());
            $manager->persist($article);    //Prepare la requête d'insertion en BDD
        }
        $manager->flush();                  //Executé la requête d'insertion en BDD (liberer)
    }
}
