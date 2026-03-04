<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use App\Dto\UserResponse;
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
 
public function __invoke($item, array $context): UserResponse
{
    if (!$item instanceof User) {
        throw new \RuntimeException('Expected User entity.');
    }

    $hashedPassword = $this->passwordHasher->hashPassword($item, $item->getPassword());
    $item->setPassword($hashedPassword);
    $item->setRoles(['ROLE_USER']);
    $this->entityManager->persist($item);
    $this->entityManager->flush();


    // Ensure the order matches your DTO constructor: (string $message, User $user)
    return new UserResponse($item,'You have registered successfully, please login now.');
}

}

// =============REQUEST=================
// mutation CreateUser(
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
//             user{
//                 id
//             }
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
