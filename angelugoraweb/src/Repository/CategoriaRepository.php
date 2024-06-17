<?php

namespace App\Repository;

use App\Entity\Categoria;
use App\Repository\ProductoRepository;
use App\Repository\LineaPedidoRepository;
use App\Repository\FotoRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoriaRepository extends ServiceEntityRepository
{
    private $productoRepository;
    private $lineaPedidoRepository;
    private $fotoRepository;

    public function __construct(
        ManagerRegistry $registry,
        ProductoRepository $productoRepository,
        LineaPedidoRepository $lineaPedidoRepository,
        FotoRepository $fotoRepository
    ) {
        parent::__construct($registry, Categoria::class);
        $this->productoRepository = $productoRepository;
        $this->lineaPedidoRepository = $lineaPedidoRepository;
        $this->fotoRepository = $fotoRepository;
    }

    public function save(Categoria $categoria): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($categoria);
        $entityManager->flush();
    }

    public function eliminarCategoriaYProductos(Categoria $categoria): void
    {
        $entityManager = $this->getEntityManager();

        $productos = $this->productoRepository->findByCategoria($categoria);

        foreach ($productos as $producto) {
            $lineasPedido = $this->lineaPedidoRepository->findBy(['producto' => $producto]);
            foreach ($lineasPedido as $lineaPedido) {
                $entityManager->remove($lineaPedido);
            }

            $fotosDelProducto = $this->fotoRepository->findBy(['producto' => $producto]);
            foreach ($fotosDelProducto as $foto) {
                $rutaImagen = $foto->getNombre();
                if ($rutaImagen) {
                    $rutaCompleta = '/imagenes/productos/' . $rutaImagen;
                    if (file_exists($rutaCompleta)) {
                        unlink($rutaCompleta);
                    }
                }
                $entityManager->remove($foto);
            }

            $entityManager->remove($producto);
        }

        $entityManager->flush();

        $entityManager->remove($categoria);

        $entityManager->flush();
    }
}
