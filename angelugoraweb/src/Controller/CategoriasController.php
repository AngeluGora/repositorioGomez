<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriasController extends AbstractController
{
    #[Route('/categorias', name: 'categorias_index')]
    public function index(CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();

        return $this->render('categorias/index.html.twig', [
            'categorias' => $categorias,
        ]);
    }

    #[Route('/categoria/nuevo', name: 'categoria_nuevo')]
    public function new(): Response
    {
        return $this->render('categorias/nuevo.html.twig');
    }

    #[Route('/categoria/crear', name: 'categoria_crear', methods: ['POST'])]
    public function create(Request $request, CategoriaRepository $categoriaRepository): Response
    {
        $nombre = strtoupper($request->request->get('nombre'));
        $descripcion = $request->request->get('descripcion');

        $categoria = new Categoria();
        $categoria->setNombre($nombre);
        $categoria->setDescripcion($descripcion);

        $categoriaRepository->save($categoria);

        return $this->redirectToRoute('categorias_index');
    }

    #[Route('/categoria/{id}', name: 'categoria_ver')]
    public function show(Categoria $categoria, ProductoRepository $productoRepository): Response
    {
        $productos = $productoRepository->findByCategoria($categoria);
        return $this->render('categorias/ver.html.twig', [
            'categoria' => $categoria,
            'productos' => $productos,
        ]);
    }

    #[Route('/categoria/{id}/editar', name: 'categoria_editar')]
    public function edit(Categoria $categoria): Response
    {
        return $this->render('categorias/editar.html.twig', [
            'categoria' => $categoria,
        ]);
    }

    #[Route('/categoria/{id}/update', name: 'categoria_update', methods: ['POST'])]
    public function update(Request $request, Categoria $categoria, CategoriaRepository $categoriaRepository): Response
    {
        $nombre = strtoupper($request->request->get('nombre'));
        $descripcion = $request->request->get('descripcion');

        $categoria->setNombre($nombre);
        $categoria->setDescripcion($descripcion);

        $categoriaRepository->save($categoria);

        return $this->redirectToRoute('categorias_index');
    }

    #[Route('/categoria/{id}/eliminar', name: 'categoria_eliminar', methods: ['POST'])]
    public function delete(Categoria $categoria, CategoriaRepository $categoriaRepository): Response
    {
        $categoriaRepository->eliminarCategoriaYProductos($categoria);


        return $this->redirectToRoute('categorias_index');
    }
}
