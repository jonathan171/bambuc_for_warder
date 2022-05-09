<?php

namespace App\Form;

use App\Entity\Zonas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                    'Especial' => 'especial',
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Zonas::class,
        ]);
    }
}
