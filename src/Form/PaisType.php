<?php

namespace App\Form;

use App\Entity\Pais;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code',null, [
                'attr' => [
                    'class' => 'form-control',
                ]
              ])
            ->add('nombre',null, [
                'attr' => [
                    'class' => 'form-control',
                ]
              ])
            ->add('zona',null, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'placeholder' => false,
              ])
            ->add('zonaImportacion',null, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'placeholder' => false,
              ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pais::class,
        ]);
    }
}
