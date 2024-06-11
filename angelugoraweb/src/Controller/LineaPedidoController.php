<?php

namespace App\Controller;

use App\Entity\LineaPedido;
use App\Repository\LineaPedidoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LineaPedidoController extends AbstractController
{
    #[Route('/lineas-pedido', name: 'lineas_pedido_index')]
    public function index(LineaPedidoRepository $lineaPedidoRepository): Response
    {
        $lineasPedido = $lineaPedidoRepository->findAll();
        
        return $this->render('linea_pedido/index.html.twig', [
            'lineas_pedido' => $lineasPedido,
        ]);
    }

    #[Route('/linea-pedido/{id}', name: 'linea_pedido_show')]
    public function show(LineaPedido $lineaPedido): Response
    {
        return $this->render('linea_pedido/show.html.twig', [
            'linea_pedido' => $lineaPedido,
        ]);
    }

    #[Route('/pedido/{id}/lineas-pedido', name: 'lineas_pedido_por_pedido')]
    public function lineasPorPedido($id, LineaPedidoRepository $lineaPedidoRepository): Response
    {
        $lineasPedido = $lineaPedidoRepository->findBy(['pedido' => $id]);
        
        return $this->render('linea_pedido/index.html.twig', [
            'lineas_pedido' => $lineasPedido,
        ]);
    }
}
