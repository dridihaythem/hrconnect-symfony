<?php

namespace App\Controller;

use App\Entity\Seminaire;
use App\Form\SeminaireType;
use App\Repository\SeminaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/seminaire')]
final class SeminaireController extends AbstractController
{
    #[Route(name: 'app_seminaire_index', methods: ['GET'])]
    public function index(SeminaireRepository $seminaireRepository): Response
    {
        return $this->render('seminaire/index.html.twig', [
            'seminaires' => $seminaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_seminaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seminaire = new Seminaire();
        $form = $this->createForm(SeminaireType::class, $seminaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seminaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_seminaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seminaire/new.html.twig', [
            'seminaire' => $seminaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seminaire_show', methods: ['GET'])]
    public function show(Seminaire $seminaire): Response
    {
        return $this->render('seminaire/show.html.twig', [
            'seminaire' => $seminaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seminaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seminaire $seminaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeminaireType::class, $seminaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_seminaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('seminaire/edit.html.twig', [
            'seminaire' => $seminaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seminaire_delete', methods: ['POST'])]
    public function delete(Request $request, Seminaire $seminaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seminaire->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($seminaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_seminaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
