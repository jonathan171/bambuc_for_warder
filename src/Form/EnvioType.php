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
            ->add('numeroEnvio')
            ->add('descripcion')
            ->add('PesoEstimado')
            ->add('PesoReal')
            ->add('totalPesoCobrar')
            ->add('totalACobrar')
            ->add('fechaEstimadaEntrega')
            ->add('empresa')
            ->add('paisOrigen')
            ->add('paisDestino')
            ->add('quienEnvia')
            ->add('quienRecibe')
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
