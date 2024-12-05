<?php

namespace App\Form\DataTransformer;

use App\Entity\FacturaItems;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ReciboItemsToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (reciboItems) to a string (number).
     *
     * @param  ReciboItems|null $reciboItems
     */
    public function transform($reciboItems): string
    {
        if (null === $reciboItems) {
            return '';
        }

        return $reciboItems->getId();
    }

    /**
     * Transforms a string (number) to an object (facturaItems).
     *
     * @param  string $reciboItemsNumber
     * @throws TransformationFailedException if object (facturaItems) is not found.
     */
    public function reverseTransform($reciboItemsNumber): ?facturaItems
    {
        // no issue number? It's optional, so that's ok
        if (!$reciboItemsNumber) {
            return null;
        }

        $reciboItems = $this->entityManager
            ->getRepository(facturaItems::class)
            // query for the issue with this id
            ->find($reciboItemsNumber);

        if (null === $reciboItems) {
            $privateErrorMessage = sprintf('no existe un reciboItems con el id "%s" ',$reciboItemsNumber);
            $publicErrorMessage = 'el  "{{ value }}"  no es un valor valido para facturaItems.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $reciboItemsNumber,
            ]);

            throw $failure;
        }

        return $reciboItems;
    }
}
