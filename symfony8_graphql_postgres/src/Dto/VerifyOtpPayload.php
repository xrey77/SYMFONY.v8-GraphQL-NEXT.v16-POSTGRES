<?php

namespace App\Dto;

use App\Entity\User;
use ApiPlatform\Metadata\GraphQl\Mutation;
// use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(graphQlOperations: [])]
final class VerifyOtpPayload
{
    public function __construct(        

        #[ApiProperty(identifier: true)]
        #[Groups(['user:read'])]
        public string $id,

        #[Groups(['user:read'])]
        public ?string $username,

        #[Groups(['user:read'])]
        public string $message,

        #[Groups(['user:read'])]
        public User $user

    ) {}
}
