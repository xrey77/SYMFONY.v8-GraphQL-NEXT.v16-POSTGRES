<!-- <?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Dto\CreateUserInput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

        public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
        {
            if (!$data instanceof CreateUserInput) {
                return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
            }

            $user = new User();
            $user->setFirstname($data->firstname);
            $user->setLastname($data->lastname);
            $user->setUsername($data->username); // Fixed typo from $data->Username()
            $user->setEmail($data->email);
            $user->setMobile($data->mobile);
            
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data->password);
            $user->setPassword($hashedPassword);
            
            $user->setRoles(['ROLE_USER']);

            return $this->persistProcessor->process($user, $operation, $uriVariables, $context);
        }

    // public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    // {
    //     $user = new User();
    //     $user->setFirstname($data->firstname);
    //     $user->setLastname($data->lastname);
    //     $user->Username($data->username);
    //     $user->setEmail($data->email);
    //     $user->setMobile($data->mobile);
    //     $hashedPassword = $this->passwordHasher->hashPassword($user, $data->password);
    //     $user->setPassword($hashedPassword);
        
    //     return $this->persistProcessor->process($user, $operation, $uriVariables, $context);
    // }
} -->
