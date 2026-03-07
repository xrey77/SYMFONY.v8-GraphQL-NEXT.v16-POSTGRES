<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\GraphQl\Mutation;
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfiguration;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfigurationInterface;
use App\Dto\CreatePayload;
use App\Resolver\CreateUserResolver;
use App\Resolver\LoginResolver;
use App\Resolver\GetUserIdResolver;
use App\Resolver\UpdateProfileResolver;
use App\Resolver\UpdatePasswordResolver;
use App\Resolver\ActivateMfaResolver;
use App\Resolver\VerifyOtpResolver;
use App\Resolver\UploadPictureResolver;
use App\Dto\LoginPayload;
use App\Dto\UpdateProfileUserPayload;
use App\Dto\ActivateMfaPayload;
use App\Dto\VerifyOtpPayload;
use App\Dto\UploadPictureUserPayload;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Metadata\ApiProperty;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'address is already taken.')]
#[UniqueEntity(fields: ['username'], message: 'is already taken.')]
#[Vich\Uploadable]
#[ApiResource(
    graphQlOperations: [
        new Query(
            name: 'item_query',
            resolver: GetUserIdResolver::class,
            args: [
                'id' => ['type' => 'ID!'],
                'extraParam' => ['type' => 'String']
            ],
            read: false
        ), 
        new QueryCollection(),  // Automatically becomes 'users'
        new Mutation(
            name: 'create',
            resolver: CreateUserResolver::class,
            args: [
                'firstname' => ['type' => 'String!'],
                'lastname'  => ['type' => 'String!'],
                'email'     => ['type' => 'String!'],
                'mobile'    => ['type' => 'String!'],
                'username'  => ['type' => 'String!'],
                'password'  => ['type' => 'String!'],
            ],            
            output: CreatePayload::class,
            read: false,
            serialize: true,
            description: 'Creates a new user with a custom success message'             
        ),
        new Mutation(
            name: 'login',
            resolver: LoginResolver::class,
            args: [
                'username' => ['type' => 'String!'],
                'password' => ['type' => 'String!'],
            ],
            output: LoginPayload::class,
            read: false,
            serialize: true,
            validate: false,
            description: 'User login with a custom success message'
        ),        
        new Mutation(
            name: 'updateProfile',
            resolver: UpdateProfileResolver::class,
            args: [
                'id' => ['type' => 'ID!'],
                'extraParam' => ['type' => 'String'],
                'firstname' => ['type' => 'String!'],
                'lastname' => ['type' => 'String!'],
                'mobile' => ['type' => 'String!'],
            ],
            output: UpdateProfileUserPayload::class,
            read: false,
            serialize: true,
            validate: false,
            description: 'Update User Profile with a custom success message'
        ),
        new Mutation(
            name: 'updatePassword',
            resolver: UpdatePasswordResolver::class,
            args: [
                'id' => ['type' => 'ID!'],
                'extraParam' => ['type' => 'String'],
                'password' => ['type' => 'String!'],
            ],
            output: UpdateProfileUserPayload::class,
            read: false,
            serialize: true,
            validate: false,
            description: 'Update Password with a custom success message'
        ),
        new Mutation(
            name: 'activateMfa',
            resolver: ActivateMfaResolver::class,
            args: [
                'id' => ['type' => 'ID!'],
                'extraParam' => ['type' => 'String'],
                'twofactorenabled' => ['type' => 'Boolean!'],
            ],
            output: ActivateMfaPayload::class,
            read: false,
            serialize: true,
            validate: false,
            description: 'Activate Multi-Factor Authenticator with a custom success message'
        ),
        new Mutation(
            name: 'verifyOtp',
            resolver: VerifyOtpResolver::class,
            args: [
                'id' => ['type' => 'ID!'],
                'extraParam' => ['type' => 'String'],
                'otp' => ['type' => 'String!'],
            ],
            output: VerifyOtpPayload::class,
            read: false,
            serialize: true,
            validate: false,
            description: 'Verify TOTP code with a custom success message'
        ),
        new Mutation(
            name: 'uploadPicture',
            resolver: UploadPictureResolver::class,
            args: [
                'id' => ['type' => 'ID!'],
                'extraParam' => ['type' => 'String'],
                'file' => ['type' => 'Upload'],
            ],
            output: UploadPictureUserPayload::class,
            read: false,
            write: false,
            validate: false,
            deserialize: false,
            description: 'Upload user profile picture code with a custom success message'
        ),


    ]
)]
class User implements TwoFactorInterface, UserInterface, PasswordAuthenticatedUserInterface 
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    #[ApiProperty(identifier: true)]
    private ?int $id;

    #[ORM\Column(length: 20, nullable: true)]
    #[ApiProperty(readable: true)]
    #[Groups(['user:read'])]
    private ?string $firstname;

    #[ORM\Column(length: 20, nullable: true)]    
    #[ApiProperty(readable: true)]
    #[Groups(['user:read'])]
    private ?string $lastname;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    #[ApiProperty(readable: true)]
    #[Groups(['user:read'])]
    private ?string $email;

    #[ORM\Column(length: 15, nullable: true)]    
    #[ApiProperty(readable: true)]
    #[Groups(['user:read'])]
    private ?string $mobile;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    #[ApiProperty(readable: true)]
    #[Groups(['user:read'])]
    private ?string $username;

    #[ORM\Column(type: 'string')]
    #[Groups(['user:create', 'user:read'])]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];
    
    #[ORM\Column(length: 3, options: ["default" => 1])]
    #[ApiProperty(readable: true)]
    #[Groups(['user:read'])]
    private ?int $isactivated = 1;
    
    #[ORM\Column(length: 3, options: ["default" => 0])]
    #[ApiProperty(readable: true)]
    #[Groups(['user:read'])]
    private ?int $isblocked = 0;

    #[ORM\Column(type: 'text',nullable: true)]    
    private ?string $secretkey;

    #[ORM\Column(length: 6, options: ["default" => 0])]    
    private ?int $mailtoken = 0;

    #[ORM\Column(type: 'text',nullable: true)]    
    #[ApiProperty(readable: true)]
    #[Groups(['user:read'])]
    private ?string $qrcodeurl;

    // #[ORM\Column(type: 'string', length: 10, options: ["default" => 'pix.png'])]
    // #[ApiProperty(readable: true)]
    // #[Groups(['user:create','user:read'])]
    // private ?string $userpic = "pix.png";
    #[ORM\Column(type: 'string', length: 10, options: ["default" => 'pix.png'])]
    #[ApiProperty(
        readable: true,
        openapiContext: ['default' => 'pix.png'], // For OpenAPI/Swagger docs
        jsonldContext: ['default' => 'pix.png'],
        default: 'pix.png' // Sets the default in the GraphQL schema
    )]
    #[Groups(['user:create', 'user:read'])]
    private ?string $userpic = 'pix.png';


    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $totpSecret = null;

    #[Groups(['user:read'])]
    #[ApiProperty(identifier: false, readable: true, writable: false)]
    private ?string $token = null;

    #[Groups(['user:read'])]
    #[ApiProperty(identifier: false, readable: true, writable: false)]
    private ?string $message = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSecretkey(): ?string
    {
        return $this->secretkey;
    }

    public function setSecretkey(?string $secretkey): self
    {
        $this->secretkey = $secretkey;
        return $this;
    }


    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;
        return $this;
    }

    public function getMailtoken(): ?int
    {        
        return $this->mailtoken;
    }

    public function setMailtoken(?int $mailtoken): self
    {
        $this->mailtoken = $mailtoken;
        return $this;
    }

    public function getQrcodeurl(): ?string
    {
        return $this->qrcodeurl;
    }

    public function setQrcodeurl(?string $qrcodeurl): self
    {
        $this->qrcodeurl = $qrcodeurl;
        return $this;
    }

    public function getIsactivated(): ?int
    {
        return $this->isactivated;
    }

    public function setIsactivated(?int $isactivated): self
    {
        $this->isactivated = $isactivated;
        return $this;
    }

    public function getUserpic(): ?string
    {
        return $this->userpic;
    }

    public function setUserpic(?string $userpic): self
    {
        $this->userpic = $userpic;
        return $this;
    }

    public function getIsblocked(): ?int
    {
        return $this->isblocked;
    }

    public function setIsblocked(?int $isblocked): self
    {
        $this->isblocked = $isblocked;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PrePersist]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }


    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }    

    #[Deprecated] 
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
        $this->setUsername = null;
        $this->setPassword = null;
    }

    public function isTotpAuthenticationEnabled(): bool
    {
        return $this->totpSecret ? true : false;
    }

    public function getTotpAuthenticationUsername(): string
    {
        return $this->username;
    }

    public function getTotpAuthenticationConfiguration(): ?TotpConfigurationInterface
    {
        return new TotpConfiguration($this->totpSecret, TotpConfiguration::ALGORITHM_SHA1, 20, 8);
    }    

    public function getTotpSecret(): ?string
    {
        return $this->totpSecret;
    }

    public function setTotpSecret(?string $totpSecret): void
    {
        $this->totpSecret = $totpSecret;
    }

    public function isTwoFactorAuthEnabled(): bool
    {
        return $this->totpSecret !== null;
    }

    public function getToken(): ?string {
        return $this->token;
    }

    public function setToken(string $token): self {
        $this->token = $token;
        return $this;
    }

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(string $message): self {
        $this->message = $message;
        return $this;
    }

   #[Vich\UploadableField(mapping: 'user_avatar', fileNameProperty: 'userpic')]
    public ?File $file = null;


}


// ===============REQUEST - RETRIEVE ALL USERS===============
// query Users {
//   users {
//     edges {
//       node {
//         id
//         firstname
//         lastname
//         email
//         mobile
//         username
//         isactivated
//         isblocked
//         userpic
//         qrcodeurl
//       }
//     }
//     totalCount
//   }
// }