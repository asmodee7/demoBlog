<?php

namespace App\DataFixtures;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticlesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        // Création de catégorie. 
        for ($i=1; $i <=3 ; $i++) 
        { 
            $category = new Category;

            $category ->setTitle($faker->sentence())
                      ->setDescription($faker->paragraph());
            $manager->persist($category);

            // Création de 4 à 6 articles.
            for ($j=1; $j <= mt_rand(4,6) ; $j++) 
            { 
                $article = new Article;

                $content ='<p>'. join($faker->paragraphs(5) ,'<p/><p>').'</p>';

                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
                $manager->persist($article);
                
                // Créations de 4 à 10 commentaires.
                for ($k=1; $k <= mt_rand(4,10) ; $k++) 
                { 
                    $comment = new Comment;

                    $content = '<p>'. join($faker->paragraphs(2), '</p><p>'). '</p>';

                    $now = new \dateTime;
                    $interval = $now-> diff($article->getCreatedAt()); // Retour le temps entre la date de création des articles à aujourd hui
                    $days = $interval->days; // Nombre de jour entre la date de création des articles & maintenant
                    $minimum='-'.$days. 'days'; // Moins de 100 days
                    
                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setArticle($article);
                    $manager->persist($comment);


                }
            }
        }
        $manager->flush();

    }
}
