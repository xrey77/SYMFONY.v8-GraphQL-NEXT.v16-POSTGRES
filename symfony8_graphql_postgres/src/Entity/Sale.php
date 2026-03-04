<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ApiResource]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks] 
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
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
