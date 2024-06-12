<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Form\EditarUsuarioFormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PerfilController extends AbstractController
{
    #[Route('/perfil', name: 'app_perfil')]
    public function index(): Response
    {
        return $this->render('perfil/index.html.twig', [
            'controller_name' => 'PerfilController',
        ]);
    }

    #[Route('/user/{id}/delete', name: 'delete_user')]
    public function deleteUser(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No se encontró ningún usuario con el ID proporcionado.');
        }
        $userRepository->deleteUser($user);
        return new RedirectResponse($this->generateUrl('app_index'));
    }
    
    #[Route('/user/{id}/irEdit', name: 'app_irEditar')]
    public function irEditar($id, UserRepository $userRepository): Response
    {
        // Obtener el usuario por su id
        $usuario = $userRepository->find($id);
    
        // Comprobar si el usuario existe
        if (!$usuario) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }
    
        // Crear el formulario para editar el usuario
        $formulario = $this->createForm(EditarUsuarioFormType::class, $usuario);
    
        return $this->render('perfil/editar.html.twig', [
            'formulario' => $formulario->createView(),
            'controller_name' => 'PerfilController',
        ]);
    }
    
    #[Route('/user/{id}/editar', name: 'app_editar_perfil')]
    public function editarPerfil(
        int $id,
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Usuario no encontrado');
        }

        $form = $this->createForm(EditarUsuarioFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verificar si el campo de contraseña no está vacío
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $plainPassword
                );
                $user->setPassword($hashedPassword);
            }

            $userRepository->save($user); // Guardar los cambios utilizando el repositorio

            $this->addFlash('success', 'Perfil actualizado con éxito');

            return $this->redirectToRoute('app_perfil', ['id' => $user->getId()]);
        }

        return $this->render('user/editar_perfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}