<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Pedido;
use App\Entity\LineaPedido;
use App\Repository\ProductoRepository;
use App\Repository\PedidoRepository;
use App\Repository\LineaPedidoRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class CarritoController extends AbstractController
{
    #[Route('/carrito/agregar/{id}', name: 'carrito_agregar', methods: ['POST'])]
    public function agregarProducto(
        int $id,
        Request $request,
        ProductoRepository $productoRepository,
        PedidoRepository $pedidoRepository,
        LineaPedidoRepository $lineaPedidoRepository,
        UserRepository $usuarioRepository
    ): Response {
        $producto = $productoRepository->find($id);
        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        $cantidad = (int) $request->request->get('cantidad', 1);
        $usuarioId = $request->request->get('usuario_id');

        $usuario = $usuarioRepository->find($usuarioId);
        if (!$usuario) {
            $this->addFlash('error', 'Usuario no encontrado.');
            return $this->redirectToRoute('app_login');
        }

        $pedido = $pedidoRepository->findOneBy(['estado' => 'pendiente', 'usuario' => $usuario]);
        if (!$pedido) {
            $pedido = new Pedido();
            $pedido->setFecha(new \DateTime());
            $pedido->setEstado('pendiente');
            $pedido->setTotalPrecio(0);
            $pedido->setUsuario($usuario);
            $pedidoRepository->add($pedido, true);
        }

        $lineaPedido = new LineaPedido();
        $lineaPedido->setProducto($producto);
        $lineaPedido->setCantidad($cantidad);
        $lineaPedido->setPedido($pedido);
        $lineaPedidoRepository->add($lineaPedido, true);

        return $this->redirectToRoute('carrito_ver');
    }

    #[Route('/carrito', name: 'carrito_ver')]
    public function verCarrito(PedidoRepository $pedidoRepository): Response
    {
        $usuario = $this->getUser();
        if (!$usuario) {
            return $this->render('carrito/ver.html.twig', [
                'pedido' => null,
                'lineasPedido' => [],
                'totalCarrito' => 0,
            ]);
        }

        $pedido = $pedidoRepository->findOneBy(['estado' => 'pendiente', 'usuario' => $usuario]);
        if (!$pedido) {
            return $this->render('carrito/ver.html.twig', [
                'pedido' => null,
                'lineasPedido' => [],
                'totalCarrito' => 0,
            ]);
        }

        $lineasPedido = $pedido->getLineaPedidos();

        $totalCarrito = $this->calcularTotalCarrito($lineasPedido);

        return $this->render('carrito/ver.html.twig', [
            'pedido' => $pedido,
            'lineasPedido' => $lineasPedido,
            'totalCarrito' => $totalCarrito,
        ]);
    }

    private function calcularTotalCarrito($lineasPedido)
    {
        $total = 0;
        foreach ($lineasPedido as $lineaPedido) {
            $total += $lineaPedido->getCantidad() * $lineaPedido->getProducto()->getPrecio();
        }
        return $total;
    }

    /**
     * @Route("/eliminar-linea-pedido/{id}", name="eliminar_linea_pedido", methods={"GET"})
     */
    public function eliminarLineaPedido($id, LineaPedidoRepository $lineaPedidoRepository, PedidoRepository $pedidoRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Encontrar la línea de pedido por su ID
            $lineaPedido = $lineaPedidoRepository->find($id);

            // Verificar si la línea de pedido existe
            if (!$lineaPedido) {
                return new JsonResponse(['mensaje' => 'Línea de pedido no encontrada'], 404);
            }

            // Obtener el pedido asociado a la línea de pedido
            $pedido = $lineaPedido->getPedido();

            // Remover la línea de pedido del pedido
            $pedido->removeLineaPedido($lineaPedido);
            
            // Persistir los cambios
            $entityManager->flush();

            return new JsonResponse(['mensaje' => 'Línea de pedido eliminada']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }
}