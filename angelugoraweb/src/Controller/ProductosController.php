<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Repository\CategoriaRepository;
use App\Repository\ProductoRepository;
use App\Repository\FotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Foto;

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
    public function create(Request $request, ProductoRepository $productoRepository, CategoriaRepository $categoriaRepository, FotoRepository $fotoRepository): Response
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

        $productoRepository->guardar($producto);

        $imagenPrincipalFile = $request->files->get('imagenPrincipal');
        if ($imagenPrincipalFile) {
            $nombreArchivo = md5(uniqid()) . '.' . $imagenPrincipalFile->guessExtension();
            $imagenPrincipalFile->move(
                $this->getParameter('fotos_directorio'),
                $nombreArchivo
            );

            $fotoPrincipal = new Foto();
            $fotoPrincipal->setNombre($nombreArchivo);
            $fotoPrincipal->setPrincipal(true);
            $fotoPrincipal->setProducto($producto);

            $fotoRepository->guardar($fotoPrincipal);
        }

        $imagenesAdicionalesFiles = $request->files->get('imagenesAdicionales');
        if ($imagenesAdicionalesFiles) {
            foreach ($imagenesAdicionalesFiles as $imagenFile) {
                $nombreArchivo = md5(uniqid()) . '.' . $imagenFile->guessExtension();
                $imagenFile->move(
                    $this->getParameter('fotos_directorio'),
                    $nombreArchivo
                );

                $fotoAdicional = new Foto();
                $fotoAdicional->setNombre($nombreArchivo);
                $fotoAdicional->setPrincipal(false);
                $fotoAdicional->setProducto($producto);

                $fotoRepository->guardar($fotoAdicional);
            }
        }

        return $this->redirectToRoute('productos_index');
    }

    #[Route('/producto/{id}', name: 'producto_ver', methods: ['GET'])]
    public function show(Producto $producto, FotoRepository $fotoRepository): Response
    {
        $fotos = $fotoRepository->findBy(['producto' => $producto]);

        return $this->render('productos/ver.html.twig', [
            'producto' => $producto,
            'fotos' => $fotos,
        ]);
    }


    #[Route('/producto/{id}/editar', name: 'producto_editar', methods: ['GET'])]
    public function edit(Producto $producto, CategoriaRepository $categoriaRepository, FotoRepository $fotoRepository): Response
    {
        $categorias = $categoriaRepository->findAll();
        
        $fotoPrincipal = null;
        foreach ($fotoRepository->findAll() as $foto) {
            if ($foto->getProducto()->getId() === $producto->getId() && $foto->isPrincipal()) {
                $fotoPrincipal = $foto;
                break;
            }
        }

        $fotos = $fotoRepository->findAll();

        return $this->render('productos/editar.html.twig', [
            'producto' => $producto,
            'categorias' => $categorias,
            'fotos' => $fotos,
            'fotoPrincipal' => $fotoPrincipal,
        ]);
    }

    #[Route('/producto/{id}/actualizar', name: 'producto_actualizar', methods: ['POST'])]
    public function update(Request $request, Producto $producto, ProductoRepository $productoRepository, CategoriaRepository $categoriaRepository, FotoRepository $fotoRepository): Response
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
        $productoRepository->actualizar($producto);

        return $this->redirectToRoute('productos_index');
    }

    #[Route('/producto/{id}/eliminar', name: 'producto_eliminar', methods: ['POST'])]
    public function delete(Request $request, Producto $producto, ProductoRepository $productoRepository): Response
    {
        $productoRepository->eliminar($producto);

        return $this->redirectToRoute('productos_index');
    }

    #[Route('/foto/{id}/eliminar', name: 'eliminar_foto', methods: ['POST'])]
    public function eliminarFoto(Request $request, Foto $foto, FotoRepository $fotoRepository): Response
    {
        $this->validateCsrfToken($request);

        $fotoRepository->eliminarFoto($foto);

        return $this->redirectToRoute('productos_index');
    }

    private function validateCsrfToken(Request $request)
    {
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('eliminar_foto', $token)) {
            throw $this->createAccessDeniedException('CSRF token no válido.');
        }
    }
}
