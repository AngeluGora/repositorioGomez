<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Repository\CategoriaRepository;
use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductosController extends AbstractController
{
    #[Route('/productos', name: 'productos_index', methods: ['GET'])]
    public function index(ProductoRepository $productoRepository): Response
    {
        $productos = $productoRepository->findAll();

        return $this->render('productos/index.html.twig', [
            'productos' => $productos,
        ]);
    }

    #[Route('/producto/nuevo', name: 'producto_nuevo', methods: ['GET'])]
    public function new(CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();

        return $this->render('productos/nuevo.html.twig', [
            'categorias' => $categorias,
        ]);
    }

    #[Route('/producto/create', name: 'producto_create', methods: ['POST'])]
    public function create(Request $request, ProductoRepository $productoRepository, CategoriaRepository $categoriaRepository): Response
    {
        $producto = new Producto();
        $producto->setNombre($request->request->get('nombre'));
        $producto->setPrecio($request->request->get('precio'));
        $producto->setDescripcion($request->request->get('descripcion'));
        $producto->setNovedad($request->request->get('novedad') ? true : false);

        $categoriaId = $request->request->get('categoria');
        $categoria = $categoriaRepository->find($categoriaId);

        if (!$categoria) {
            throw $this->createNotFoundException('La categoría seleccionada no existe.');
        }

        $producto->setCategoria($categoria);

        // Utilizar el método del repositorio para guardar el producto
        $productoRepository->guardar($producto);

        return $this->redirectToRoute('productos_index');
    }

    #[Route('/producto/{id}', name: 'producto_ver', methods: ['GET'])]
    public function show(Producto $producto): Response
    {
        return $this->render('productos/ver.html.twig', [
            'producto' => $producto,
        ]);
    }

    #[Route('/producto/{id}/editar', name: 'producto_editar', methods: ['GET'])]
    public function edit(Producto $producto, CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();

        return $this->render('productos/editar.html.twig', [
            'producto' => $producto,
            'categorias' => $categorias,
        ]);
    }

    #[Route('/producto/{id}/actualizar', name: 'producto_actualizar', methods: ['POST'])]
    public function update(Request $request, Producto $producto, ProductoRepository $productoRepository, CategoriaRepository $categoriaRepository): Response
    {
        $producto->setNombre($request->request->get('nombre'));
        $producto->setPrecio($request->request->get('precio'));
        $producto->setDescripcion($request->request->get('descripcion'));
        $producto->setNovedad($request->request->get('novedad') ? true : false);

        $categoriaId = $request->request->get('categoria');
        $categoria = $categoriaRepository->find($categoriaId);

        if (!$categoria) {
            throw $this->createNotFoundException('La categoría seleccionada no existe.');
        }

        $producto->setCategoria($categoria);

        // Utilizar el método del repositorio para actualizar el producto
        $productoRepository->actualizar($producto);

        return $this->redirectToRoute('productos_index');
    }

    #[Route('/producto/{id}/eliminar', name: 'producto_eliminar', methods: ['POST'])]
    public function delete(Request $request, Producto $producto, ProductoRepository $productoRepository): Response
    {
        // Utilizar el método del repositorio para eliminar el producto
        $productoRepository->eliminar($producto);

        return $this->redirectToRoute('productos_index');
    }
}
