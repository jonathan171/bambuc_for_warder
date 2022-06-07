<?php

namespace App\Form;

use App\Entity\Zonas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZonasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre',null, [
                'attr' => [
                    'class' => 'form-control',
                 //   'placeholder' => 'el nombre la zona normalmente es ZONA #'
                ]
              ])
            ->add('tipo',ChoiceType::class, [
                'choices'  => [
                    'Exportación' => 'exportacion',
                    'Importación' => 'importacion',
                    'Especial Importación' => 'especial_importacion',
                    'Especial Exportacion' => 'especial_exportacion',
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])->add('situacionEmergencia', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Zonas::class,
        ]);
    }
}
