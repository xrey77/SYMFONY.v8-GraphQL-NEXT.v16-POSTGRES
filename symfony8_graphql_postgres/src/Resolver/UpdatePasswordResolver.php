<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use App\Dto\UpdateProfileUserPayload;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


final class UpdatePasswordResolver implements MutationResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @param User|null $item The deserialized user from the input
     */
 
public function __invoke($item, array $context): UpdateProfileUserPayload
{
    if (!$item instanceof User) {
        throw new \RuntimeException('Expected User entity.');
    }

    $hashedPassword = $this->passwordHasher->hashPassword($item, $item->getPassword());
    $item->setPassword($hashedPassword);

    
    $this->entityManager->persist($item);
    $this->entityManager->flush();

    $item->setMessage('You have changed your password successfully');
    return new UpdateProfileUserPayload(
        (string) $item->getId(),
        $item->getMessage(),
        $item
    );    
 }
}


// ===========REQUEST=================
// mutation updatePassword(
//   $id: ID!,
// 	$password: String!) {
//     updatePasswordUser(
//       input: {
//         id: $id,
//     		password: $password}) {
//       user {
//         id
//         message
//       }
//     }
//   }


// ===========VARIABLES===========
// {
//   "id": "1",
//   "password": "nald",
// }