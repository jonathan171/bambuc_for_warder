<?php

namespace App\Form;

use App\Entity\TarifasConfiguracion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function PHPSTORM_META\type;

class TarifasConfiguracionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('valorDolar', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('tasaConbustible', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])
            ->add('porcentajeGanacia', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'step' => '0.01'
                ],
                'html5' => true
            ])->add('empresa',ChoiceType::class, [
                'choices'  => [
                    'DHL' => 'DHL',
                    'FEDEX' => 'FEDEX',
                    'UPS' => 'UPS',
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ])->add('tipo',ChoiceType::class, [
                'choices'  => [
                    'Exportación' => 'exportacion',
                    'Importación' => 'importacion',
                    'Especial Importación' => 'especial_importacion',
                    'Especial Exportacion' => 'especial_exportacion',
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TarifasConfiguracion::class,
        ]);
    }
}
