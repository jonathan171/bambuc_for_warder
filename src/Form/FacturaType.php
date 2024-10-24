<?php

namespace App\Form;

use App\Entity\Factura;
use App\Form\DataTransformer\ClientesToNumberTransformer;
use App\Repository\FacturaResolucionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturaType extends AbstractType
{
    private $transformer;
    private $facturaResolucionRepository;
   

    public function __construct(ClientesToNumberTransformer $transformer , FacturaResolucionRepository $facturaResolucionRepository)
    {
        $this->transformer = $transformer;
        $this->facturaResolucionRepository = $facturaResolucionRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
         // Aquí verificamos si la opción `use_alternate_function` está activada
         $choices = $options['use_alternate_function'] 
         ? $this->facturaResolucionRepository->findAlternate() 
         : $this->facturaResolucionRepository->findActive();


        $builder
            ->add('numeroFactura', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('estado')
            ->add('fecha', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('fechaVencimiento', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'class' => 'form-control',
                ],
                'html5' => true
            ])
            ->add('fechaValidacion')
            ->add('total', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('anulado')
            ->add('anuladoUsuario')
            ->add('anuladoFecha')
            ->add('formaDePago', ChoiceType::class, [
                'choices'  => [
                    'Contado' => 'DEBITO',
                    'Crédito' => 'CREDITO'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('tipoFactura')
            ->add('observaciones',null,[
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('facturaResolucion',null,[
                'choices' =>  $choices,
                'attr' => [
                    'class' => 'form-control',
                ],
                'required'  => true,
            ])
            ->add('hora', null, [
                'widget' => 'single_text',
                'required' => false])
            ->add('subtotal', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('totalIva')
            ->add('totalReteIva')
            ->add('totalReteIca')
            ->add('totalReteFuente')
            ->add('totalReteFuenteG')
            ->add('reteFuente', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('reteIca', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('reteIva')
            ->add('descuento', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('respuestaDian')
            ->add('cufe')
            ->add('pdf')
            ->add('xml')
            ->add('cuerpoJsonf')
            ->add('cuerpoJsonc')
            ->add('respuestaCorreo')
            ->add('condDePago',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('cliente', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('regimen',ChoiceType::class, [
                'choices'  => [
                    'Simple' => 'SIMPLE',
                    'Ordinario' => 'ORDINARIO',
                    'Gran Contribuyente' => 'GRAN_CONTRIBUYENTE',
                    'Autorretenedor'   => 'AUTORRETENEDOR',
                    'Agente Retención Iva' => 'AGENTE_RETENCION_IVA'
                ],
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])->add('taxLevelCode',ChoiceType::class, [
                'choices'  => [
                    'Responsable De Iva' => 'RESPONSABLE_DE_IVA',
                    'No Responsable De Iva' => 'NO_RESPONSABLE_DE_IVA'
                ],
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])->add('soportePago')
              ->add('facturado');

        $builder->get('cliente')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Factura::class,
            'use_alternate_function' => false,
        ]);
    }
}
