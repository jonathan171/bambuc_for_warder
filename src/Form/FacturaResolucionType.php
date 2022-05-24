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
            ->add('numeroResolucion',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('fechaHabilitacion', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('inicioConsecutivo',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('finConsecutivo',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('prefijo',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('activo',null, [
                'attr' => [
                    'class' => 'form-check-input  success check-outline outline-success',  
                ]
              ])
            ->add('fechaVencimiento', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FacturaResolucion::class,
        ]);
    }
}
