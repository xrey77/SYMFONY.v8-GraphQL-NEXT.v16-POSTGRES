<?php

namespace App\Dto;

use App\Entity\User;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Mutation;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    graphQlOperations: []
)]
final class UserResponse
{
    public function __construct(        
        #[Groups(['user:read'])]
        public User $user,

        #[Groups(['user:read'])]
        public string $message,
    ) {}
}
