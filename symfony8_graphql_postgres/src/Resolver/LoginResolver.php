<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use App\Repository\UserRepository;
use App\Dto\LoginPayload;

final class LoginResolver implements MutationResolverInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    /**
     * @param User|null $item
     */
    public function __invoke($item, array $context): LoginPayload
    {
        $input = $context['args']['input'];
        $user = $this->userRepository->findOneBy(['username' => $input['username']]);
        if ($user) {            
            if (!password_verify($input['password'],$user->getPassword())) {
                throw new AuthenticationException('Invalid password, please try again.');
            }
            $token = $this->jwtManager->create($user);
            $user->setToken($token);
            $user->setMessage('You have logged-in successfully, please wait.');
            return new LoginPayload(
                    (string) $user->getId(),
                    $user->getFirstname(),
                    $user->getLastname(),
                    $user->getEmail(),
                    $user->getMobile(),
                    $user->getUsername(),
                    $user->getRoles(),
                    $user->getIsactivated(),
                    $user->getIsblocked(),
                    $user->getUserpic(),
                    $user->getQrcodeurl(),
                    $user->getToken(),
                    $user->getMessage(),
                    $user
                );

        } else {
            throw new AuthenticationException('Username not found, please register first.');
        }
    }
}


// ================REQUEST===============================
// mutation login($username: String!, $password: String!) {
//   loginUser(input: {username: $username, password: $password}) {    
//     user {
//       id
//       firstname
//       lastname
//       email
//       mobile
//       username
//       roles
//       isactivated
//       isblocked
//       userpic
//       qrcodeurl
//       token
//       message
//     }    
//   }
// }


// ===============VARIABLES=========================
// {
//   "username": "Rey",
//   "password": "rey"
// }