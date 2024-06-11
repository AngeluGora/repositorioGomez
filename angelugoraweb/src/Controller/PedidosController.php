<?php

namespace App\Controller;

use App\Entity\Pedidos;
use App\Repository\PedidosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PedidosController extends AbstractController
{
    #[Route('/pedidos', name: 'pedidos_index')]
    public function index(PedidosRepository $pedidosRepository): Response
    {
        $pedidos = $pedidosRepository->findAll();

        return $this->render('pedidos/index.html.twig', [
            'pedidos' => $pedidos,
        ]);
    }

    #[Route('/pedido/{id}', name: 'pedido_show')]
    public function show(Pedidos $pedido): Response
    {
        return $this->render('pedidos/show.html.twig', [
            'pedido' => $pedido,
        ]);
    }
}
