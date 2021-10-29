<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager){

        $ad = new Ad();
        //$title = $request->request->get('annonce');
        // dump($title);
        /*
        $image1 = new Image();
        $image1->setUrl('http://placehold.it/400x200')
            ->setCaption('Titre 1');
        
        $ad->addImage($image1);

        $image2 = new Image();
        $image2->setUrl('http://placehold.it/400x200')
            ->setCaption('Titre 2');

        $ad->addImage($image2);    
        */

        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //gestion des images
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            // on ajoute l'auteur mais attention risque de bug jusqu'a security
            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée! "
            );
   
            return $this->redirectToRoute('ads_show',[
                'slug' => $ad->getSlug()
            ]);

        }



        return $this->render("ad/new.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier une annonce
     * @Route("/ads/{slug}/edit", name="ads_edit")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Ad $ad
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Ad $ad)
    {
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($ad->getImages() as $image)
            {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifiée!"
            );

        }

        return $this->render("ad/edit.html.twig",[
            "ad" => $ad,
            "myForm" => $form->createView()
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
