<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/", name="reservation_index", methods={"GET"})
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        //IF NOT ADMIN SHOW MY RESERVATIONS
        $user = $this->getUser();
        if (!in_array("ROLE_ADMIN", $user->getRoles())) {

            return $this->render('reservation/index.html.twig', [
                'reservations' => $reservationRepository->findBy(
                    ['borrower' => $user, 'active' => true]
                )
            ]);

        }
        //SHOW ALL RESERVATIONS

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/past", name="reservation_past", methods={"GET"})
     */
    public function reservationPast(ReservationRepository $reservationRepository): Response
    {
        //IF NOT ADMIN SHOW MY RESERVATIONS
        $user = $this->getUser();
        if (!in_array("ROLE_ADMIN", $user->getRoles())) {

            return $this->render('reservation/past_reservation.html.twig', [
                'reservations' => $reservationRepository->findBy(
                    ['borrower' => $user, 'active' => false]
                )
            ]);

        }
        //SHOW ALL RESERVATIONS

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/let/{id}", name="reservation_let", methods={"GET","POST"})
     */
    public function let(Reservation $reservation): Response
    {
        $reservation->setActive(false);
        $annonce = $reservation->getAnnonce();
        $annonce->setActive(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reservation,$annonce);
        $entityManager->flush();
        return $this->redirectToRoute('reservation_index');
    }
    /**
     * @Route("/{id}", name="reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reservation_index');
    }
}
