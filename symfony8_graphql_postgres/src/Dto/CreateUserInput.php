<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserInput
{
    public ?string $mobile = null;

    #[Assert\NotBlank]
    public string $firstname;

    #[Assert\NotBlank]
    public string $lastname;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public string $username;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;
}
