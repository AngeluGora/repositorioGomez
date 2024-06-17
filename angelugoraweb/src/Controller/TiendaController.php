<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use App\Repository\FotoRepository;

class TiendaController extends AbstractController
{
    /**
     * @Route("/todos-los-productos", name="ver_todos_los_productos")
     */
    public function verTodosLosProductos(ProductoRepository $productoRepository, FotoRepository $fotoRepository): Response
    {
        $productos = $productoRepository->findAll();
        $fotos = $fotoRepository->findAll();
        return $this->render('tienda/gridTodosProductos.html.twig', [
            'productos' => $productos,
            'fotos' => $fotos,
        ]);
    }

    /**
     * @Route("/todas-las-categorias", name="ver_todas_las_categorias")
     */
    public function verTodasLasCategorias(CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();
        return $this->render('tienda/gridTodasCategorias.html.twig', [
            'categorias' => $categorias,
        ]);
    }

    /**
     * @Route("/todas-las-novedades", name="ver_todas_las_novedades")
     */
    public function verTodasLasNovedades(ProductoRepository $productoRepository, FotoRepository $fotoRepository): Response
    {
        $fotos = $fotoRepository->findAll();
        $novedades = $productoRepository->buscarNovedades();
        return $this->render('tienda/gridTodasNovedades.html.twig', [
            'novedades' => $novedades,
            'fotos' => $fotos,
        ]);
    }
}
