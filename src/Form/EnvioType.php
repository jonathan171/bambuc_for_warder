<?php

namespace App\Form;

use App\Entity\Envio;
use App\Form\DataTransformer\FacturaItemsToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnvioType extends AbstractType
{

    private $transformer;

    public function __construct(FacturaItemsToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo', null, [
                'attr' => [
                    'class' => 'form-control'  
                ]
            ])
            ->add('estado',ChoiceType::class, [
                'choices'  => [
                    'Enviado'=>'1' ,
                    'En proceso'=> '2',
                    'Entregado' =>'3',
                ],
                'attr' => [
                    'class' => 'form-control']
            ])
            ->add('numeroEnvio', null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('descripcion',  TextType::class, [
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
            ->add('fechaEnvio', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('fechaEstimadaEntrega', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('empresa',ChoiceType::class, [
                'choices'  => [
                    'DHL' => 'DHL',
                    'FEDEX' => 'FEDEX',
                    'UPS' => 'UPS',
                ],
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
            ->add('verificado')
            ->add('referencia', TextType::class, [
                'required'=> false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('facturaItems', TextType::class, [
                'required'=> false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('reciboCajaItem', TextType::class, [
                'required'=> false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('facturadoTransportadora')
            ->add('facturado_recibo')
            ->add('facturaTransportadora', TextType::class, [
                'required'=> false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('municipio',null, [
                'attr' => [
                    'class' => 'form-control', 
                ]
              ]);

            $builder->get('facturaItems')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Envio::class,
        ]);
    }
}
