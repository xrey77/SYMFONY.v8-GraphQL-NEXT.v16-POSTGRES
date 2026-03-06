<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks] 
#[ApiResource] 
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
