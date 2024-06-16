<?php

namespace App\Repository;


use App\Entity\Foto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FotoRepository extends ServiceEntityRepository
{
    private string $fotosDirectorio;

    public function __construct(ManagerRegistry $registry, string $fotosDirectorio)
    {
        parent::__construct($registry, Foto::class);
        $this->fotosDirectorio = $fotosDirectorio;
    }


    public function guardar(Foto $foto): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($foto);
        $entityManager->flush();
    }

    /**
     * Encuentra la foto principal asociada a un producto.
     *
     * @param int $productoId El ID del producto
     * @return Foto|null La foto principal encontrada o null si no existe
     */
    public function findFotoPrincipalByProducto(int $productoId): ?Foto
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.producto = :productoId')
            ->andWhere('f.principal = true')
            ->setParameter('productoId', $productoId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
