<?php

namespace App\Controller;

use App\Entity\Categorias;
use App\Repository\CategoriasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriasController extends AbstractController
{
    #[Route('/categorias', name: 'categorias_index')]
    public function index(CategoriasRepository $categoriasRepository): Response
    {
        $categorias = $categoriasRepository->findAll();

        return $this->render('categorias/index.html.twig', [
            'categorias' => $categorias,
        ]);
    }

    #[Route('/categoria/{id}', name: 'categoria_show')]
    public function show(Categorias $categoria): Response
    {
        return $this->render('categorias/show.html.twig', [
            'categoria' => $categoria,
        ]);
    }
}
