<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use App\Repository\FotoRepository;

class GeneralController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(Request $request): Response
    {
        $request->setLocale('es');

        return $this->render('general/index.html.twig');
    }

    /**
     * @Route("/terminos-y-condiciones", name="app_terminos")
     */
    public function terminos(Request $request): Response
    {
        $request->setLocale('es');

        return $this->render('general/terminosYCondiciones.html.twig');
    }

    /**
     * @Route("/politica-de-privacidad", name="app_politica")
     */
    public function politica(Request $request): Response
    {
        $request->setLocale('es');

        return $this->render('general/politicaPrivacidad.html.twig');
    }

    /**
     * @Route("/aviso-legal", name="app_aviso")
     */
    public function aviso(Request $request): Response
    {
        $request->setLocale('es');

        return $this->render('general/avisoLegal.html.twig');
    }

    /**
     * @Route("/servicios", name="app_servicios")
     */
    public function servicios(Request $request): Response
    {
        $request->setLocale('es');
        return $this->render('servicios/index.html.twig');
    }

    /**
     * @Route("/tienda", name="app_tienda")
     */
    public function tienda(Request $request,
        ProductoRepository $productoRepository,
        CategoriaRepository $categoriaRepository,
        FotoRepository $fotoRepository
    ): Response {
        $request->setLocale('es');
        
        $productos = $productoRepository->findAll();
        $categorias = $categoriaRepository->findAll();
        $novedades = $productoRepository->findBy(['novedad' => true]);
        $fotos = $fotoRepository->findAll();

        return $this->render('tienda/index.html.twig', [
            'productos' => $productos,
            'categorias' => $categorias,
            'novedades' => $novedades,
            'fotos' => $fotos,
        ]);
    }

    /**
     * @Route("/contacto", name="app_contacto")
     */
    public function contacto(Request $request): Response
    {
        $request->setLocale('es');
        return $this->render('contacto/index.html.twig');
    }
}
