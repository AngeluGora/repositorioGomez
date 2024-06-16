<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PedidoRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Pedido;

class PagoController extends AbstractController
{
    /**
     * @Route("/ir-a-pagar", name="ir_a_pagar", methods={"POST"})
     */
    public function irAPagar(Request $request): Response
    {
        $userId = $request->request->get('userId');
        $pedidoId = $request->request->get('pedidoId');
        $precioFinal = $request->request->get('precioFinal');

        return $this->render('carrito/pagar.html.twig', [
            'userId' => $userId,
            'pedidoId' => $pedidoId,
            'precioFinal' => $precioFinal,
        ]);
    }

    /**
     * @Route("/procesar-pago", name="procesar_pago", methods={"POST"})
     */
    public function procesarPago(
        Request $request,
        PedidoRepository $pedidoRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $nombre = $request->request->get('nombre');
        $numero = $request->request->get('numero');
        $fecha = $request->request->get('fecha');
        $cvv = $request->request->get('cvv');
        $userId = intval($request->request->get('userId'));
        $pedidoId = intval($request->request->get('pedidoId'));
        $precioFinal = (float) $request->request->get('precioFinal');

        if (!$nombre || !$numero || !$fecha || !$cvv) {
            $this->addFlash('error', 'Por favor, completa todos los campos.');
            return $this->redirectToRoute('ir_a_pagar', [
                'userId' => $userId,
                'pedidoId' => $pedidoId,
                'precioFinal' => $precioFinal,
            ]);
        }

        if (!preg_match('/^[0-9]{16}$/', $numero)) {
            $this->addFlash('error', 'El número de tarjeta debe contener 16 dígitos numéricos.');
            return $this->redirectToRoute('ir_a_pagar', [
                'userId' => $userId,
                'pedidoId' => $pedidoId,
                'precioFinal' => $precioFinal,
            ]);
        }

        if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $fecha)) {
            $this->addFlash('error', 'El formato de la fecha de vencimiento debe ser MM/AA.');
            return $this->redirectToRoute('ir_a_pagar', [
                'userId' => $userId,
                'pedidoId' => $pedidoId,
                'precioFinal' => $precioFinal,
            ]);
        }

        if (!preg_match('/^[0-9]{3}$/', $cvv)) {
            $this->addFlash('error', 'El CVV debe contener 3 dígitos numéricos.');
            return $this->redirectToRoute('ir_a_pagar', [
                'userId' => $userId,
                'pedidoId' => $pedidoId,
                'precioFinal' => $precioFinal,
            ]);
        }

        try {
            $pedido = $pedidoRepository->findPedidoByIdAndUserId($pedidoId, $userId);

            if (!$pedido) {
                throw $this->createNotFoundException('Pedido no encontrado');
            }

            $pedido->setTotalPrecio($precioFinal);

            $pedido->setEstado('confirmado');
            $entityManager->flush();

            $this->addFlash('success', '¡Enhorabuena! El pedido ha sido confirmado.');

            return $this->redirectToRoute('app_perfil');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Ha ocurrido un error al procesar el pedido. Por favor, inténtalo de nuevo.');
            return $this->redirectToRoute('ir_a_pagar', [
                'userId' => $userId,
                'pedidoId' => $pedidoId,
                'precioFinal' => $precioFinal,
            ]);
        }
    }
}
