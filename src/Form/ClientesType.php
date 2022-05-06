<?php

namespace App\Form;

use App\Entity\Clientes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('razonSocial')
            ->add('nit')
            ->add('direccion')
            ->add('telefono')
            ->add('correo')
            ->add('tipoReceptor')
            ->add('tipoDocumento')
            ->add('tipoRegimen')
            ->add('nombres')
            ->add('apellidos')
            ->add('municipio')
            ->add('idTributo')
            ->add('idObligacion')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clientes::class,
        ]);
    }
}
