<?php

namespace App\Controller;

use App\Entity\Pigistes;
use App\Form\PigistesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pigistes", name="pigistes_")
 */
class PigistesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $pigistes = $this->getDoctrine()
            ->getRepository(Pigistes::class)
            ->findBy([
                'active' => true
            ], [
                'nom' => 'ASC'
            ]);
        return $this->render('pigistes/index.html.twig', [
            'pigistes' => $pigistes,
            'active_menu' => 'pigistes'
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $pigiste = new Pigistes();

        $form = $this->createForm(PigistesType::class, $pigiste);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pigiste = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pigiste);
            $entityManager->flush();
            $this->addFlash('info', 'Le pigiste ' . $pigiste->getNom() . ' a bien été enregistré.');
            return $this->redirectToRoute('pigistes_index');
        }

        return $this->render('pigistes/add.html.twig', [
            'form' => $form->createView(),
            'active_menu' => 'pigistes'
        ]);
    }

    /**
     * @Route("/deactivate/{id}", name="deactivate")
     */
    public function deactivate(Pigistes $pigiste) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $pigiste->setActive(false);
        $this->getDoctrine()->getManager()->persist($pigiste);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute("pigistes_index");
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Pigistes $pigiste, Request $request) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(PigistesType::class, $pigiste);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pigiste = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pigiste);
            $entityManager->flush();
            $this->addFlash('info', 'Le pigiste ' . $pigiste->getNom() . ' a bien été enregistré.');
            return $this->redirectToRoute('pigistes_index');
        }

        return $this->render('pigistes/edit.html.twig', [
            'form' => $form->createView(),
            'active_menu' => 'pigistes',
            'pigiste' => $pigiste
        ]);
    }
}
