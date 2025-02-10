<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PublicationType;


class PublicationController extends AbstractController
{
    #[Route('/publication', name: 'publication_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $publications = $entityManager->getRepository(Publication::class)->findAll();

        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
        ]);
    }

    #[Route('/publication/new', name: 'publication_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publication = new Publication();
        $publication->setDate(new \DateTime());
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('publication_index');
        }

        return $this->render('publication/new.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/publication/{id}', name: 'publication_show')]
    public function show(Publication $publication, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setPublication($publication);
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('publication_show', ['id' => $publication->getId()]);
        }

        $commentaires = $entityManager->getRepository(Commentaire::class)->findBy(['publication' => $publication]);

        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
            'commentaire_form' => $form->createView(),
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/publication/{id}/edit', name: 'publication_edit')]
public function edit(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(PublicationType::class, $publication);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $publication->setDate(new \DateTime()); 
        $entityManager->flush();
        return $this->redirectToRoute('publication_index');
    }
    return $this->render('publication/edit.html.twig', [
        'publication' => $publication,
        'form' => $form->createView(),
    ]);
}


    #[Route('/publication/{id}/delete', name: 'publication_delete')]
    public function delete(Publication $publication, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($publication);
        $entityManager->flush();

        return $this->redirectToRoute('publication_index');
    }
}
