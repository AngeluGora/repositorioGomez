<?php

namespace App\Repository;

use App\Entity\Pedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pedido>
 */
class PedidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pedido::class);
    }

    public function add(Pedido $pedido, bool $flush = false): void
    {
        $this->getEntityManager()->persist($pedido);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Pedido $pedido, bool $flush = false): void
    {
        $this->getEntityManager()->remove($pedido);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPedidosByUserId(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.usuario = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findPedidoByIdAndUserId(int $pedidoId, int $userId): ?Pedido
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :pedidoId')
            ->andWhere('p.usuario = :userId')
            ->setParameter('pedidoId', $pedidoId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
            
    }
}
