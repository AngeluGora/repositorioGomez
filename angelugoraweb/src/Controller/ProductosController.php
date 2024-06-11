<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Repository\ProductosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductosController extends AbstractController
{
    #[Route('/productos', name: 'productos_index')]
    public function index(ProductosRepository $productosRepository): Response
    {
        $productos = $productosRepository->findAll();

        return $this->render('productos/index.html.twig', [
            'productos' => $productos,
        ]);
    }

    #[Route('/producto/{id}', name: 'producto_show')]
    public function show(Productos $producto): Response
    {
        return $this->render('productos/show.html.twig', [
            'producto' => $producto,
        ]);
    }
}
