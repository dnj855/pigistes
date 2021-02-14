<?php

namespace App\Controller;

use App\Entity\Tarifs;
use App\Entity\SiteParameters;
use App\Form\TarifsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tarifs", name="tarifs_")
 */
class TarifsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $lastUpdate = $this->getDoctrine()->getRepository(SiteParameters::class)->findLastTarifsUpdate();
        if ($lastUpdate == null) {
            $date = null;
        } else {
            $date = $lastUpdate[0]->getTarifsUpdate();
        }
        $tarifs = $this->getDoctrine()->getRepository(Tarifs::class)->findAll();
        return $this->render('tarifs/index.html.twig', [
            'active_menu' => 'tarifs',
            'last_update' => $date,
            'tarifs' => $tarifs
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Tarifs $tarifs, Request $request) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(TarifsType::class, $tarifs);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tarifs = $form->getData();
            $lastUpdate = new SiteParameters();
            $lastUpdate->setTarifsUpdate(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tarifs);
            $entityManager->persist($lastUpdate);
            $entityManager->flush();
            return $this->redirectToRoute('tarifs_index');
        }

        return $this->render('tarifs/edit.html.twig', [
            'active_menu' => 'tarifs',
            'form' => $form->createView(),
            'tarif' => $tarifs
        ]);
    }
}
