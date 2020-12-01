<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityController extends AbstractController
{
   /**
    * @Route("/inscription", name="security_registration")
    */
    
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, AuthorizationCheckerInterface $authChecker): Response
    {
        if ($authChecker->isGranted('ROLE_USER')) 
        {
            return $this->redirectToRoute('home');
            
        }
        $user = new User;

        dump($request);

        $formRegistration = $this->createForm(RegistrationType::class,$user);

        $formRegistration->handleRequest($request);

        if ($formRegistration->isSubmitted() && $formRegistration->isValid()) 
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute("security_login");

        }

        return $this->render('security/registration.html.twig',[
            'formRegistration'=> $formRegistration->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $AuthenticationUtils, AuthorizationCheckerInterface $authChecker): Response
    {
        if ($authChecker->isGranted('ROLE_USER')) 
        {
            return $this->redirectToRoute('home');
           
        }
        // Récuperation du message d'erreur en cas de mauvais connexion
        $error = $AuthenticationUtils->getLastAuthenticationError();

        // Récuperation du dernier Username(Email) saisie par l'internaute en cas d'erreur
        $lastUsername = $AuthenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig',[
            'error'=>$error,
            'last_username' =>$lastUsername
        ]);
    }

    /**
     * @Route("/deconnection", name="security_logout")
     */

    public function logout()
    {
        // Cette méthode ne retourne rien, il nous suffit d'avoir une route pour se déconnecter.
    }

}
