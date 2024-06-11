<?php

namespace App\Controller;

use App\Entity\Foto;
use App\Repository\FotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FotoController extends AbstractController
{
    #[Route('/fotos', name: 'fotos_index')]
    public function index(FotoRepository $fotoRepository): Response
    {
        $fotos = $fotoRepository->findAll();

        return $this->render('fotos/index.html.twig', [
            'fotos' => $fotos,
        ]);
    }

    #[Route('/foto/{id}', name: 'foto_show')]
    public function show(Foto $foto): Response
    {
        return $this->render('fotos/show.html.twig', [
            'foto' => $foto,
        ]);
    }
}
