<?php

namespace App\Form;

use App\Entity\Envio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnvioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo')
            ->add('estado')
            ->add('numeroOrden')
            ->add('descripcion')
            ->add('totalPesoEstimado')
            ->add('totalPesoReal')
            ->add('totalDimencional')
            ->add('totalCostoPrevisto')
            ->add('totalCostoReal')
            ->add('totalACobrar')
            ->add('fechaEstimadaEntrega')
            ->add('piezas')
            ->add('empresa')
            ->add('cantidadPiezas')
            ->add('jsonRecibido')
            ->add('facturado')
            ->add('facturaItems')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Envio::class,
        ]);
    }
}
