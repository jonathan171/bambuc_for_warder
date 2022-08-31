<?php

namespace App\Form;

use App\Entity\EnviosNacionales;
use App\Form\DataTransformer\ClientesToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnviosNacionalesType extends AbstractType
{ 
    private $transformer;

    public function __construct(ClientesToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cliente', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('fecha', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('numero', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('municipioOrigen',null, [
                'attr' => [
                    'class' => 'form-control', 
                ]
              ])
            ->add('direccionOrigen',null, [
                'attr' => [
                    'class' => 'form-control', 
                ]
              ])
            ->add('unidades', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('municipioDestino',null, [
                'attr' => [
                    'class' => 'form-control', 
                ]
              ])
            ->add('destinatario', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('direccionDestino', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('telefonoDestinatario', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('peso', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('descripcion',null,[
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('seguro', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('valorTotal', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('formaPago', ChoiceType::class, [
                'choices'  => [
                    'Contado' => 'DEBITO',
                    'Crédito' => 'CRÉDITO'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('contraEntrega')
        ;
        $builder->get('cliente')
        ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnviosNacionales::class,
        ]);
    }
}
