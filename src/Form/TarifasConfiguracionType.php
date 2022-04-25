<?php

namespace App\Form;

use App\Entity\TarifasConfiguracion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifasConfiguracionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('valorDolar')
            ->add('tasaConbustible')
            ->add('porcentajeGanacia')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TarifasConfiguracion::class,
        ]);
    }
}
