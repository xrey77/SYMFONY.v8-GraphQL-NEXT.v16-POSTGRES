<?php

namespace App\Dto;

use App\Entity\User;
// use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Mutation;
use Symfony\Component\Serializer\Annotation\Groups;

// #[ApiResource(graphQlOperations: [])]
final class CreatePayload
{
    public function __construct(

        #[ApiProperty(identifier: true)]
        #[Groups(['user:read'])]
        public string $id,

        #[Groups(['user:read'])]
        public string $message,

        #[Groups(['user:read'])]
        public User $user,

    ) {}
}
