<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use App\Dto\CreatePayload;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserResolver implements MutationResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @param User|null $item The deserialized user from the input
     */
 
public function __invoke($item, array $context): CreatePayload
{
    if (!$item instanceof User) {
        throw new \RuntimeException('Expected User entity.');
    }

    $plainPassword = $item->getPassword() ?? $context['args']['input']['password'] ?? null;

    if (!$plainPassword) {
        throw new \InvalidArgumentException('Password is required.');
    }

    $hashedPassword = $this->passwordHasher->hashPassword($item, $item->getPassword());
    $item->setPassword($hashedPassword);
    $item->setRoles(['ROLE_USER']);
    $this->entityManager->persist($item);
    $this->entityManager->flush();

    $item->setMessage('You have registered successfully, please login now.');
    return new CreatePayload(
        (string) $item->getId(),
        $item->getMessage(),
        $item
    );    
 }
}

// =============REQUEST=================
// mutation create(
//     $firstname: String!,
//     $lastname: String!,
//     $email: String!,
//     $mobile: String!,
//     $username: String!,
//     $password: String!
// ) {    
//     createUser(
//         input: {
//         firstname: $firstname,
//         lastname: $lastname,
//         email: $email,
//         mobile: $mobile,
//         username: $username,
//         password: $password        
//         }
//      ) {
//           user{
//               id
//               message
//           }
//     			clientMutationId
//     }
// }


// ===============VARIABLES================
// {
//     "firstname": "Rey",
//     "lastname": "Gragasin",
//     "email": "zrey@yahoo.com",
//     "mobile": "32423423",
//     "username": "Rey",
//     "password": "rey"
// }
