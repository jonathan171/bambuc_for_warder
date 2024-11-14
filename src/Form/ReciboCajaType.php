<?php

namespace App\Form;

use App\Entity\ReciboCaja;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReciboCajaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_recibo', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('fecha', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('municipio',null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('creada_por',null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('pagada_a',null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReciboCaja::class,
        ]);
    }
}
