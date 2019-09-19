<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Entity\User;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * @Route("/annonce")
 */
class AnnonceController extends AbstractController
{
    /**
     * @Route("/", name="annonce_index", methods={"GET"})
     */
    public function index(AnnonceRepository $annonceRepository): Response
    {
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonceRepository->findAll(),
        ]);
    }

    /**
     * @Route("annonce/active", name="annonce_active", methods={"GET"})
     */
    public function activeAnnonces(AnnonceRepository $annonceRepository): Response
    {
        return $this->render('annonce/activeannonces.html.twig', [
            'annonces' => $annonceRepository->findBy(
                ['active' => true]
            )
        ]);
    }

    /**
     * @Route("annonce/myannonces", name="annonce_my", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function myAnnonces(AnnonceRepository $annonceRepository): Response
    {
        $user = $this->getUser();
        return $this->render('annonce/myannonces.html.twig', [
            'annonces' => $annonceRepository->findBy(
                ['lender' => $user]
            )
        ]);
    }


    /**
     * @Route("/new", name="annonce_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $annonce = new Annonce();
        $annonce->setLender($user);
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('annonce_index');
        }

        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="annonce_show", methods={"GET"})
     */
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="annonce_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Annonce $annonce): Response
    {
        $user = $this->getUser();
        if($user==$annonce->getLender()) {

            $form = $this->createForm(AnnonceType::class, $annonce);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('annonce_index');
            }

            return $this->render('annonce/edit.html.twig', [
                'annonce' => $annonce,
                'form' => $form->createView(),
            ]);
        }
        return $this->render('annonce/index.html.twig');
    }

    /**
     * @Route("/reserve/{id}", name="annonce_reserve", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function reserve(Request $request, Annonce $annonce): Response
    {
        $active = $annonce->getActive();
        if ($active) {
            $annonce->reserve();
            $user = $this->getUser();
            $reservation = new Reservation($annonce, $user);
            $annonce->addReservation($reservation);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();
        }
        return $this->redirectToRoute('annonce_index');
    }

    /**
     * @Route("/{id}", name="annonce_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Annonce $annonce): Response
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_index');
    }
}
