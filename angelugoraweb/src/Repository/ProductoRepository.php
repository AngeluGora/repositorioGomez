<?php

namespace App\Repository;

use App\Entity\Producto;
use App\Entity\Categoria;
use App\Entity\Foto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\LineaPedidoRepository;

/**
 * @method Producto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Producto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Producto[]    findAll()
 * @method Producto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoRepository extends ServiceEntityRepository
{
    private $lineaPedidoRepository;

    public function __construct(ManagerRegistry $registry, LineaPedidoRepository $lineaPedidoRepository)
    {
        parent::__construct($registry, Producto::class);
        $this->lineaPedidoRepository = $lineaPedidoRepository;
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

        // Eliminar fotos asociadas al producto
        $fotoRepository = $entityManager->getRepository(Foto::class);
        $fotosDelProducto = $fotoRepository->findBy(['producto' => $producto]);

        foreach ($fotosDelProducto as $foto) {
            $rutaImagen = $foto->getNombre();
            if ($rutaImagen) {
                $rutaCompleta = '/imagenes/productos/' . $rutaImagen; // Ajusta la ruta según tu estructura de archivos
                if (file_exists($rutaCompleta)) {
                    unlink($rutaCompleta); // Eliminar archivo físico si existe
                }
            }

            $entityManager->remove($foto);
        }

        // Eliminar líneas de pedido asociadas al producto usando LineaPedidoRepository
        $lineasPedido = $this->lineaPedidoRepository->findByProducto($producto);

        foreach ($lineasPedido as $lineaPedido) {
            $entityManager->remove($lineaPedido);
        }
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
