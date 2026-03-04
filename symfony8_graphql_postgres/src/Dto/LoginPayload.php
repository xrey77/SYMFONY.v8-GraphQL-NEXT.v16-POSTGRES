<?php

namespace App\Dto;

use App\Entity\User;
use ApiPlatform\Metadata\GraphQl\Mutation;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(graphQlOperations: [])]
final class LoginPayload
{
    public function __construct(        

        #[ApiProperty(identifier: true)]
        #[Groups(['user:read'])]
        public string $id,

        #[Groups(['user:read'])]
        public string $firstname,

        #[Groups(['user:read'])]
        public string $lastname,

        #[Groups(['user:read'])]
        public string $email,

        #[Groups(['user:read'])]
        public string $mobile,

        #[Groups(['user:read'])]
        public string $username,

        #[Groups(['user:read'])]
        public array $roles,

        #[Groups(['user:read'])]
        public Int $isactivated,
        
        #[Groups(['user:read'])]
        public Int $isblocked,

        #[Groups(['user:read'])]
        public string $userpic,

        #[Groups(['user:read'])]
        public ?string $qrcodeurl,

        #[Groups(['user:read'])]
        public string $token,

        #[Groups(['user:read'])]
        public string $message,

        #[Groups(['user:read'])]
        public User $user


    ) {}
}
