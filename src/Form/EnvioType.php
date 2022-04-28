<?php

namespace App\Form;

use App\Entity\Envio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnvioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo', null, [
                'attr' => [
                    'class' => 'form-control'  
                ]
            ])
            ->add('estado', null, [
                'attr' => [
                    'class' => 'form-control']
            ])
            ->add('numeroEnvio', null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('descripcion', null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('PesoEstimado', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('PesoReal', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('totalPesoCobrar', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('totalACobrar', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('fechaEstimadaEntrega', null, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('empresa', null, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('paisOrigen', null, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('paisDestino', null, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('quienEnvia', null, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('quienRecibe', null, [
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('jsonRecibido')
            ->add('facturado')
            ->add('facturaItems', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Envio::class,
        ]);
    }
}
