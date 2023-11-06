<?php

namespace App\Form;

use App\Entity\EnviosNacionales;
use App\Form\DataTransformer\ClientesToNumberTransformer;
use App\Form\DataTransformer\FacturaItemsToNumberTransformer;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnviosNacionalesType extends AbstractType
{ 
    private $transformer;
    private $transformerItems;

    public function __construct(ClientesToNumberTransformer $transformer, FacturaItemsToNumberTransformer $transformerItems)
    {
        $this->transformer = $transformer;
        $this->transformerItems = $transformerItems;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cliente', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('fecha', DateTimeType::class, [
                'widget' => 'single_text',
                //'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                //'data' => new DateTime(),
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
            ->add('observacion',null,[
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
                    'Contado' => 'CONTADO',
                    'Crédito' => 'CRÉDITO',
                    'Contra Entrega' => 'CONTRA ENTREGA'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('contraEntrega')
            ->add('estado')
            ->add('facturado')
            ->add('facturaItems', TextType::class, [
                'required'=> false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('numero_guia',null,[
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
        ;
        $builder->get('cliente')
        ->addModelTransformer($this->transformer);
        $builder->get('facturaItems')
        ->addModelTransformer($this->transformerItems);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EnviosNacionales::class,
        ]);
    }
}
