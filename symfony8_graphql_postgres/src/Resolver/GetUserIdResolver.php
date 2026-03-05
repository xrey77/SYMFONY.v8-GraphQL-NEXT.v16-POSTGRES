<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\QueryItemResolverInterface;
use App\Entity\User;
use App\Repository\UserRepository;

final class GetUserIdResolver implements QueryItemResolverInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * @param User
     */
    // public function __invoke($item, array $context): ?User
    public function __invoke(?object $item, array $context): User

    {
        $id = $context['args']['id'];
        $user = $this->userRepository->find($id);

        if (!$user) {            
           throw new \Exception('User ID not found.');
        } 
        
        return $user;        
    }
}


// ======REQUEST======
// query getUserId($id: ID!) {
//   user(id: $id) {
//     id
//     firstname
//     lastname
//     email
//     mobile
//     username
//     isactivated
//     isblocked
//     userpic
//     qrcodeurl
//   }
// }


// ======VARIABLES=========
// {
//   "id": "1"
// }
