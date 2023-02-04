<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RecetteController extends AbstractController
{
    /**
     * @Route("/ajoutRecette", name="ajoutRecette", methods={"GET", "POST"})
     */
    public function ajoutRecette(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recette = new Recette();
        $file = $request->files->get('imageName');

        $recetteForm = $this->createForm(RecetteType::class, $recette);

        $recetteForm->handleRequest($request);

        if($recetteForm->isSubmitted()){
            $recette->setImageFile($file);

            $entityManager->persist($recette);
            $entityManager->flush();

            $this->addFlash('success', 'Recette ajoutée avec succès !');
            return $this->redirectToRoute('main_accueil', ['id' => $recette->getId()]);
        }

        return $this->render('recette/ajoutRecette.html.twig', [
            'recetteForm' => $recetteForm->createView()
        ]);
    }

    /**
     * @Route("/detailsRecette/{id}", name="detailsRecette")
     */
    public function detailsRecette(int $id, RecetteRepository $recetteRepository): Response
    {
        $recette = $recetteRepository->find($id);

        if (!$recette)
            throw $this->createNotFoundException('Pas de recette avec cet identifiant');

        return $this->render('recette/detailsRecette.html.twig', [
            "recette" => $recette
        ]);
    }
}
