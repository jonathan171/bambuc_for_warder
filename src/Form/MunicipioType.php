<?php

namespace App\Form;

use App\Entity\Municipio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MunicipioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo',null, [
                'attr' => [
                    'class' => 'form-control',
                ]
              ])
            ->add('nombre',null, [
                'attr' => [
                    'class' => 'form-control',
                ]
              ])
            ->add('departamento',null, [
                'attr' => [
                    'class' => 'form-control',
                ]
              ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Municipio::class,
        ]);
    }
}
