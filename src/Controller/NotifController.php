<?php

namespace App\Controller;

use App\Entity\Notif;
use App\Form\NotifType;
use App\Repository\NotifRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notif")
 */
class NotifController extends AbstractController
{
    /**
     * @Route("/", name="notif_index", methods={"GET"})
     */
    public function index(NotifRepository $notifRepository): Response
    {
        return $this->render('notif/index.html.twig', [
            'notifs' => $notifRepository->findAll(),
        ]);
    }



    public function myNotifs(NotifRepository $notifRepository): Response
    {
        return $this->render('notif/_notif_dashboard.html.twig', [
            'notifs' => $notifRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="notif_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $notif = new Notif();
        $form = $this->createForm(NotifType::class, $notif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notif);
            $entityManager->flush();

            return $this->redirectToRoute('notif_index');
        }

        return $this->render('notif/new.html.twig', [
            'notif' => $notif,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="notif_show", methods={"GET"})
     */
    public function show(Notif $notif): Response
    {
        return $this->render('notif/show.html.twig', [
            'notif' => $notif,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="notif_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Notif $notif): Response
    {
        $form = $this->createForm(NotifType::class, $notif);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('notif_index');
        }

        return $this->render('notif/edit.html.twig', [
            'notif' => $notif,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="notif_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Notif $notif): Response
    {
        if ($this->isCsrfTokenValid('delete'.$notif->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($notif);
            $entityManager->flush();
        }

        return $this->redirectToRoute('notif_index');
    }
}
