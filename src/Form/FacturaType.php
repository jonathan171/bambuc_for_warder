<?php

namespace App\Form;

use App\Entity\Factura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroFactura')
            ->add('estado')
            ->add('fecha')
            ->add('fechaVencimiento')
            ->add('fechaValidacion')
            ->add('total')
            ->add('anulado')
            ->add('anuladoUsuario')
            ->add('anuladoFecha')
            ->add('formaDePago')
            ->add('tipoFactura')
            ->add('observaciones')
            ->add('facturaResolucionId')
            ->add('hora')
            ->add('subtotal')
            ->add('totalIva')
            ->add('totalReteIva')
            ->add('totalReteIca')
            ->add('totalReteFuente')
            ->add('totalReteFuenteG')
            ->add('reteFuente')
            ->add('reteIca')
            ->add('reteIva')
            ->add('descuento')
            ->add('tipoRegimen')
            ->add('respuestaDian')
            ->add('cufe')
            ->add('pdf')
            ->add('xml')
            ->add('cuerpoJsonf')
            ->add('cuerpoJsonc')
            ->add('respuestaCorreo')
            ->add('condDePago')
            ->add('idTributo')
            ->add('cliente')
            ->add('idObligacion')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Factura::class,
        ]);
    }
}
