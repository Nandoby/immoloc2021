<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * Permet d'afficher l'ensemble des annonces 
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo): Response
    {

        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }


    /**
     * Permet de créer une annonce
     * @Route("/ads/new", name="ads_create")
     * @return void
     */
    public function create(){

        $ad = new Ad();
        /*
        $form = $this->createFormBuilder($ad)
                    ->add('title', TextType::class, [
                        "label" => "mon titre",
                        "attr" => [
                            "class" => "test"
                        ]
                    ])
                    ->add('introduction')
                    ->add('content')
                    ->add('rooms')
                    ->add('price')
                    ->getForm();
                    return $this->render("ad/new.html.twig",[
                        'myForm' => $form->createView()
                    ]);
             
        */
        $form = $this->createForm(AnnonceType::class, $ad);
        return $this->render("ad/new.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher une seule annonce
     * @Route("/ads/{slug}", name="ads_show")
     * @return Response
     */
    public function show($slug, Ad $ad)
    {

        // $repo = $this->getDoctrine()->getRepository(Ad::class);
        // $ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig',[
            'ad' => $ad
        ]);
    }

    
}
