<?php

namespace App\Form\DataTransformer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserToNumberTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms an object (courses) to a string (number).
     *
     * @param  user|null $user
     */
    public function transform($user): string
    {
        if (null === $user) {
            return '';
        }

        return $user->getId();
    }

    /**
     * Transforms a string (number) to an object (user).
     *
     * @param  string $clienetNumber
     * @throws TransformationFailedException if object (user) is not found.
     */
    public function reverseTransform($userNumber): ?user
    {
        // no issue number? It's optional, so that's ok
        if (!$userNumber) {
            return null;
        }

        $user = $this->entityManager
            ->getRepository(User::class)
            // query for the issue with this id
            ->find($userNumber);

        if (null === $user) {
            $privateErrorMessage = sprintf('no existe un user con el id "%s" ', $userNumber);
            $publicErrorMessage = 'el  "{{ value }}"  no es un valor valido para user.';

            $failure = new TransformationFailedException($privateErrorMessage);
            $failure->setInvalidMessage($publicErrorMessage, [
                '{{ value }}' => $userNumber,
            ]);

            throw $failure;
        }

        return $user;
    }
}
