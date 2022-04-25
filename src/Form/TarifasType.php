<?php

namespace App\Form;

use App\Entity\Tarifas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pesoMinimo')
            ->add('pesoMaximo')
            ->add('costoFlete')
            ->add('total')
            ->add('tarifasConfiguracion')
            ->add('zona')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tarifas::class,
        ]);
    }
}
