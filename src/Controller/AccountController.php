<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    /**
     * Permet à l'utilisateur de se connecter
     * @Route("/login", name="account_login")
     */
    public function index(AuthenticationUtils $utils): Response
    {

        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/index.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }


    /**
     * Permet de se déconnecter
     * @Route("/logout", name="account_logout")
     */
    public function logout(){
        // ...
    }

    /**
     * Permet d'afficher le formulaire d'inscription et d'inscrire un utilisateur dans le site
     * @Route("/register", name="account_register")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function register(Request $request,EntityManagerInterface $manager,UserPasswordHasherInterface $hasher)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé"
            );

            return $this->redirectToRoute('account_login');
        }


        return $this->render("account/registration.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier son profil 
     * @Route("/account/profile", name="account_profile")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser(); // récup l'utilisateur connecté
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les données ont été enregistrées avec succès"
            );
        }

        return $this->render("account/profile.html.twig",[
            'myForm' => $form->createView()
        ]);


    }

    /**
     * Permet de modifier le mot de passe
     * @Route("/account/password-update", name="account_password")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    public function updatePassword(Request $request,EntityManagerInterface $manager,UserPasswordHasherInterface $hasher)
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() &&  $form->isValid())
        {
            // vérif que le mot de passe correspond à l'ancien
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword()))
            {
                // gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));

            }else{
                $newPassword= $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié'
                );

                return $this->redirectToRoute('homepage');

            }
        }

        return $this->render("account/password.html.twig",[
            'myForm'=>$form->createView()
        ]);


    }


}
