<?php

namespace App\Form;

use App\Entity\NotaCredito;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotaCreditoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fecha')
            ->add('hora')
            ->add('numeroNota')
            ->add('subtotal')
            ->add('total')
            ->add('totalIva')
            ->add('reteFuente')
            ->add('totalReteFuenteG')
            ->add('observaciones')
            ->add('tipo')
            ->add('conceptoDebito')
            ->add('conceptoCredito')
            ->add('respuestaDian')
            ->add('cufe')
            ->add('cuerpoJsonf')
            ->add('cuerpoJsonc')
            ->add('respuestaCorreo')
            ->add('facturaCliente')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NotaCredito::class,
        ]);
    }
}
