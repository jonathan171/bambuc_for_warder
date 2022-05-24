<?php

namespace App\Form;

use App\Entity\Clientes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('razonSocial',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('nit',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('direccion',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('telefono',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('correo',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('tipoReceptor',ChoiceType::class, [
                'choices'  => [
                    'Persona Natural' => '2',
                    'Persona Jurídica' => '1',
                    
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('tipoDocumento',ChoiceType::class, [
                'choices'  => [
                    'Cedula Ciudadania' => 'CC',
                    'Nit' => 'NIT',
                    'Pasaporte' => 'PASAPORTE',
                    'Registro Civil' => 'RC',
                    'Tarjeta de identidad' => 'TI',
                    'Tarjeta de extranjería'=>'TE',
                    'Cédula de Extranjería' => 'CE',
                    'Nit otro pais'=> 'NIT_OTRO_PAIS'
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('tipoRegimen',ChoiceType::class, [
                'choices'  => [
                    'No responsable de Iva' => '49',
                    'Impuesto sobre las ventas - IVA' => '48'
                ],
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('nombres',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('apellidos',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('municipio',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('idTributo',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
            ->add('idObligacion',null, [
                'attr' => [
                    'class' => 'form-control',  
                ]
              ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clientes::class,
        ]);
    }
}
