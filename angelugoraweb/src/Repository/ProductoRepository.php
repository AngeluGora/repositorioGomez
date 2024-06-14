<?php

namespace App\Repository;

use App\Entity\Producto;
use App\Entity\Categoria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Producto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Producto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Producto[]    findAll()
 * @method Producto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    /**
     * Guarda un producto en la base de datos.
     *
     * @param Producto $producto
     * @return Producto
     */
    public function guardar(Producto $producto): Producto
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($producto);
        $entityManager->flush();

        return $producto;
    }

    /**
     * Actualiza un producto en la base de datos.
     *
     * @param Producto $producto
     * @return Producto
     */
    public function actualizar(Producto $producto): Producto
    {
        $entityManager = $this->getEntityManager();
        $entityManager->flush();

        return $producto;
    }

    /**
     * Elimina un producto de la base de datos.
     *
     * @param Producto $producto
     */
    public function eliminar(Producto $producto): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($producto);
        $entityManager->flush();
    }

    /**
     * Busca productos marcados como novedades.
     *
     * @return Producto[] Returns an array of Producto objects
     */
    public function buscarNovedades(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.novedad = true')
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra todos los productos asociados a una categoría específica.
     *
     * @param Categoria $categoria La categoría para la cual se buscan los productos.
     * @return Producto[] Un array de objetos Producto.
     */
    public function findByCategoria(Categoria $categoria): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.categoria = :categoria')
            ->setParameter('categoria', $categoria)
            ->getQuery()
            ->getResult();
    }
}
