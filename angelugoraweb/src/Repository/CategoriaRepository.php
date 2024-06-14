<?php

namespace App\Repository;

use App\Entity\Categoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

class CategoriaRepository extends ServiceEntityRepository
{

    private $productoRepository;

    public function __construct(ManagerRegistry $registry, ProductoRepository $productoRepository)
    {
        parent::__construct($registry, Categoria::class);
        $this->productoRepository = $productoRepository;
    }

    public function save(Categoria $categoria): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($categoria);
        $entityManager->flush();
    }

    public function eliminarCategoriaYProductos(Categoria $categoria): void
    {
        $productos = $this->productoRepository->findByCategoria($categoria);

        $entityManager = $this->getEntityManager();

        foreach ($productos as $producto) {
            $entityManager->remove($producto);
        }

        $entityManager->flush();

        $entityManager->remove($categoria);
        $entityManager->flush();
    }
}
