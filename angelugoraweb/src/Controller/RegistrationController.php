<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends AbstractController
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/irARegister', name: 'app_irARegister')]
    public function irARegister($error = null): Response
    {
        return $this->render('registration/register.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response
    {
        $nombre = $request->request->get('nombre');
        $apellidos = $request->request->get('apellidos');
        $telefono = $request->request->get('telefono');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $confirmPassword = $request->request->get('confirm_password');
        $direccion = $request->request->get('direccion');
        $poblacion = $request->request->get('poblacion');
        $provincia = $request->request->get('provincia');
        $codigoPostal = $request->request->get('codigo_postal');
        $agreeTerms = $request->request->get('agree_terms');
        
        $error = null;

        $existingUser = $this->userRepository->findOneByEmail($email);
        
        if (
            $nombre === null || $apellidos === null || $telefono === null || $email === null || $password === null ||
            $confirmPassword === null || $direccion === null || $poblacion === null || $provincia === null ||
            $codigoPostal === null || $agreeTerms === null ||
            trim($nombre) === '' || trim($apellidos) === '' || trim($telefono) === '' || trim($email) === '' || trim($password) === '' ||
            trim($confirmPassword) === '' || trim($direccion) === '' || trim($poblacion) === '' || trim($provincia) === '' ||
            trim($codigoPostal) === '' || trim($agreeTerms) === ''
        ) {
            $error = 'Todos los campos son obligatorios';
        } elseif ($existingUser) {
            $error = 'Ya existe una cuenta con este email.';
            return $this->render('registration/register.html.twig', [
                'error' => $error,
            ]);
        } elseif ($password !== $confirmPassword) {
            $error = 'Las contraseñas no coinciden';
        } elseif (strlen($password) < 8) {
            $error = 'La contraseña debe tener al menos 8 caracteres';
        } elseif (!preg_match('/^[0-9+]+$/', $telefono)) {
            $error = 'El teléfono solo puede contener números y el símbolo "+"';
        }elseif ($telefono >= 9 && $telefono <= 12) {
            $error = 'La longitud del telefono no es válida.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no tiene un formato válido';
        }

        if ($error) {
            return $this->render('registration/register.html.twig', [
                'error' => $error,
            ]);
        }

        // Si no hay errores, proceder con la creación del usuario
        $user = new User();
        $user->setEmail($email);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setNombre($nombre);
        $user->setApellidos($apellidos);
        $user->setDireccion($direccion);
        $user->setTelefono($telefono);
        $user->setPoblacion($poblacion);
        $user->setProvincia($provincia);
        $user->setCodigoPostal($codigoPostal);
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_tienda');
    }
}

