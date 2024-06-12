<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Necesitas importar TextType
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class EditarUsuarioFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Correo electrónico',
            ])
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
            ])
            ->add('apellidos', TextType::class, [
                'label' => 'Apellidos',
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
            ])
            ->add('telefono', TextType::class, [
                'label' => 'Teléfono',
            ])
            ->add('poblacion', TextType::class, [
                'label' => 'Población',
            ])
            ->add('provincia', TextType::class, [
                'label' => 'Provincia',
            ])
            ->add('codigoPostal', TextType::class, [
                'label' => 'Código Postal',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                'required' => false, // La contraseña no es obligatoria en el formulario de edición
                'mapped' => false, // Este campo no está mapeado a una propiedad de la entidad User
                'help' => 'Dejar en blanco si no se desea cambiar la contraseña', // Ayuda opcional para el usuario
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
