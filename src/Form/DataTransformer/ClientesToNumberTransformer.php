<?php

namespace App\Form\DataTransformer;

use App\Entity\Clientes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ClientesToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (clientes) to a string (number).
     *
     * @param  Clientes|null $cliente
     */
    public function transform($cliente): string
    {
        if (null === $cliente) {
            return '';
        }

        return $cliente->getId();
    }

    /**
     * Transforms a string (number) to an object (cliente).
     *
     * @param  string $clienetNumber
     * @throws TransformationFailedException if object (cliente) is not found.
     */
    public function reverseTransform($clienteNumber): ?Clientes
    {
        // no issue number? It's optional, so that's ok
        if (!$clienteNumber) {
            return null;
        }

        $cliente = $this->entityManager
            ->getRepository(Clientes::class)
            // query for the issue with this id
            ->find($clienteNumber);

        if (null === $cliente) {
            $privateErrorMessage = sprintf('no existe un cliente con el id "%s" ', $clienteNumber);
            $publicErrorMessage = 'el  "{{ value }}"  no es un valor valido para cliente.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $clienteNumber,
            ]);

            throw $failure;
        }

        return $cliente;
    }
}
