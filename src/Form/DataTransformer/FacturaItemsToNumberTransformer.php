<?php

namespace App\Form\DataTransformer;

use App\Entity\FacturaItems;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FacturaItemsToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (facturaItems) to a string (number).
     *
     * @param  FacturaItems|null $facturaItems
     */
    public function transform($facturaItems): string
    {
        if (null === $facturaItems) {
            return '';
        }

        return $facturaItems->getId();
    }

    /**
     * Transforms a string (number) to an object (facturaItems).
     *
     * @param  string $facturaItemsNumber
     * @throws TransformationFailedException if object (facturaItems) is not found.
     */
    public function reverseTransform($facturaItemsNumber): ?facturaItems
    {
        // no issue number? It's optional, so that's ok
        if (!$facturaItemsNumber) {
            return null;
        }

        $facturaItems = $this->entityManager
            ->getRepository(facturaItems::class)
            // query for the issue with this id
            ->find($facturaItemsNumber);

        if (null === $facturaItems) {
            $privateErrorMessage = sprintf('no existe un facturaItems con el id "%s" ', $facturaItemsNumber);
            $publicErrorMessage = 'el  "{{ value }}"  no es un valor valido para facturaItems.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $facturaItemsNumber,
            ]);

            throw $failure;
        }

        return $facturaItems;
    }
}
