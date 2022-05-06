<?php

namespace App\Form;

use App\Entity\FacturaResolucion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturaResolucionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroResolucion')
            ->add('fechaHabilitacion')
            ->add('inicioConsecutivo')
            ->add('finConsecutivo')
            ->add('prefijo')
            ->add('activo')
            ->add('fechaVencimiento')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FacturaResolucion::class,
        ]);
    }
}
