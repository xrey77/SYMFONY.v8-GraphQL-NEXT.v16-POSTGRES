<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\GraphQl\Mutation;
use App\Repository\SaleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use App\Resolver\SalesDataResolver;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
#[ORM\Table(name: '`sale`')]
#[ORM\HasLifecycleCallbacks] 
#[ApiResource(
    graphQlOperations: [
        new QueryCollection(
            name: 'saledata',
            resolver: SalesDataResolver::class,
            paginationEnabled: false,
            read: false
        ),         
    ]
)]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
    #[Groups(['user:read'])]
    private ?string $saleamount;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['user:read'])]
    private \DateTimeImmutable $saledate;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getSaleAmount(): ?string
    {
        return $this->saleamount;
    }

    public function setSaleAmount(string $saleamount): static
    {
        $this->saleamount = $saleamount;

        return $this;
    }

    public function getSaleDate(): ?\DateTimeImmutable
    {
        return $this->saledate;
    }

    public function setSaleDate(\DateTimeImmutable $saledate): self
    {
        $this->saledate = $saledate;
        return $this;
    }

}
