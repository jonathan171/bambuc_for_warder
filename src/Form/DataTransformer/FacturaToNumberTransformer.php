<?php

namespace App\Form\DataTransformer;

use App\Entity\Factura;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FacturaToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (facturaItems) to a string (number).
     *
     * @param  Factura|null $factura
     */
    public function transform($factura): string
    {
        if (null === $factura) {
            return '';
        }

        return $factura->getId();
    }

    /**
     * Transforms a string (number) to an object (factura).
     *
     * @param  string $facturaNumber
     * @throws TransformationFailedException if object (factura) is not found.
     */
    public function reverseTransform($facturaNumber): ?factura
    {
        // no issue number? It's optional, so that's ok
        if (!$facturaNumber) {
            return null;
        }

        $factura = $this->entityManager
            ->getRepository(factura::class)
            // query for the issue with this id
            ->find($facturaNumber);

        if (null === $factura) {
            $privateErrorMessage = sprintf('no existe un factura con el id "%s" ', $facturaNumber);
            $publicErrorMessage = 'el  "{{ value }}"  no es un valor valido para facturaItems.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $facturaNumber,
            ]);

            throw $failure;
        }

        return $factura;
    }
}
