<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Permet d'afficher les réservations
     * @Route("/admin/bookings", name="admin_bookings_index")
     * @param BookingRepository $repo
     * @return Response
     */
    public function index(BookingRepository $repo): Response
    {
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $repo->findAll() 
        ]);
    }

    /**
     * Permet d'éditer une réservation
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking, [
            'validation_groups' => ['Default']
        ]);
        $form->handleRequest($request);
        // vérif et enregristrement
        if($form->isSubmitted() && $form->isValid())
        {
            $booking->setAmount(0); // 0 = empty -> donc la fonction dans le PrePersist de l'entity Booking va s'activer.

            $manager->persist($booking); /* pas obligatoire parce que le booking est déjà créé */
            $manager->flush();
            $this->addFlash(
                'success',
                "La réservation n°<strong>{$booking->getId()}</strong> a bien été modifiée"
            );
        }
        return $this->render("admin/booking/edit.html.twig",[
            "booking"=>$booking,
            "myForm"=>$form->createView()
        ]);
    }

    /**
     * Permet de supprimer une réservation
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager)
    {

    }
}
