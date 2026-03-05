<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use App\Dto\UpdateProfileUserPayload;
use Doctrine\ORM\EntityManagerInterface;

final class UpdateProfileResolver implements MutationResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @param User|null $item The deserialized user from the input
     */
 
public function __invoke($item, array $context): UpdateProfileUserPayload
{
    if (!$item instanceof User) {
        throw new \RuntimeException('Expected User entity.');
    }

    $input = $context['args']['input'];
    $item->setLastname($input['lastname'] ?? $item->getLastname());
    $item->setMobile($input['mobile'] ?? $item->getMobile());
        
    $this->entityManager->persist($item);
    $this->entityManager->flush();

    $item->setMessage('You have updated your profile successfully');
    return new UpdateProfileUserPayload(
        (string) $item->getId(),
        $item->getMessage(),
        $item
    );    
 }
}


// ===========REQUEST=================
// mutation updateProfile(
//   $id: ID!,
//   $firstname: String!,
// 	$lastname: String!,
// 	$mobile: String!) {
//     updateProfileUser(
//       input: {
//         id: $id,
//     		firstname: $firstname,
//     		lastname: $lastname,
//     		mobile: $mobile}) {
//       user {
//         id
//         message
//       }
//     }
//   }

// ===========VARIABLES===========
// {
//   "id": "1",
//   "firstname": "Reynaldo",
//   "lastname": "Marquez-Gragasin",
//   "mobile": "+63234234234"
// }