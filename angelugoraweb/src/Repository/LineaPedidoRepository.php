<?php

namespace App\Repository;

use App\Entity\LineaPedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LineaPedido>
 */
class LineaPedidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LineaPedido::class);
    }

    public function add(LineaPedido $lineaPedido, bool $flush = false): void
    {
        $this->getEntityManager()->persist($lineaPedido);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LineaPedido $lineaPedido, bool $flush = false): void
    {
        $this->getEntityManager()->remove($lineaPedido);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
