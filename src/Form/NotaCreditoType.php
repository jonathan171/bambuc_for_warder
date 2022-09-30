<?php

namespace App\Form;

use App\Entity\NotaCredito;
use App\Form\DataTransformer\FacturaToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotaCreditoType extends AbstractType
{   
    private $transformer;

    public function __construct(FacturaToNumberTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fecha', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('hora', null, [
                'widget' => 'single_text',
                'required' => false])
            ->add('numeroNota', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('subtotal', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('total', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('totalIva', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('reteFuente', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('totalReteFuenteG', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('observaciones',null,[
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('tipo')
            ->add('conceptoDebito',ChoiceType::class, [
                'choices'  => [
                    'Intereses' => '1' ,
                    'Gastos por cobrar' => '2' ,
                    'Cambio del valor' => '3',
                    'Otros'=> '4'
                ],
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('conceptoCredito',ChoiceType::class, [
                'choices'  => [
                    'Devolución de parte de los bienes; no aceptación de partes del servicio'  => '1',
                    'Anulación de factura electrónica' => '2' ,
                    'Rebaja total aplicada' => '3' ,
                    'Descuento total aplicado' => '4',
                    'Rescisión: nulidad por falta de requisitos'=> '5',
                    'Otros'=>'6'    
                ],
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('respuestaDian')
            ->add('cufe')
            ->add('cuerpoJsonf')
            ->add('cuerpoJsonc')
            ->add('respuestaCorreo')
            ->add('facturaCliente',TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);

            $builder->get('facturaCliente')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NotaCredito::class,
        ]);
    }
}
