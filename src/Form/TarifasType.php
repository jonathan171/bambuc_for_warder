<?php

namespace App\Form;

use App\Entity\Tarifas;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pesoMinimo', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true,
                'data'=> '0'
            ])
            ->add('pesoMaximo', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true,
                'data'=> '0'
            ])
            ->add('costoFlete', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true,
                'data'=> '0'
            ])
            ->add('total', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true,
                'data'=> '0'
            ])
            ->add('tarifasConfiguracion',null, [
                'attr' => [
                    'class' => 'form-control',  
                ],
                'placeholder' => false,
               
              ])
            ->add('zona',null, [
                'attr' => [
                    'class' => 'form-control',  
                ],
                'placeholder' => 'Por favor seleccione uno',
              ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tarifas::class,
        ]);
    }
}
