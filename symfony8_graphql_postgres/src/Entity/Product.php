<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ApiResource]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks] 
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    private ?string $descriptions;

    #[ORM\Column(length: 5, options: ["default" => 0])]
    private ?int $qty;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $unit;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
    private ?string $costprice;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
    private ?string $sellprice;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ["default" => 0])]
    private ?string $saleprice;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $productpicture;

    #[ORM\Column(length: 5, options: ["default" => 0])]
    private ?int $alertstocks;

    #[ORM\Column(length: 5, options: ["default" => 0])]
    private ?int $criticalstocks;

    #[ORM\Column(type: 'datetime_immutable')]    
    private ?\DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private \DateTimeImmutable $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getDescriptions(): ?string
    {
        return $this->descriptions;
    }

    public function setDescriptions(string $descriptions): static
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): static
    {
        $this->qty = $qty;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getCostprice(): ?string
    {
        return $this->costprice;
    }

    public function setCostprice(string $costprice): static
    {
        $this->costprice = $costprice;

        return $this;
    }

    public function getSellprice(): ?string
    {
        return $this->sellprice;
    }

    public function setSellprice(string $sellprice): static
    {
        $this->sellprice = $sellprice;

        return $this;
    }

    public function getSaleprice(): ?string
    {
        return $this->saleprice;
    }

    public function setSaleprice(string $saleprice): static
    {
        $this->saleprice = $saleprice;

        return $this;
    }

    public function getProductpicture(): ?string
    {
        return $this->productpicture;
    }

    public function setProductpicture(string $productpicture): static
    {
        $this->productpicture = $productpicture;

        return $this;
    }

    public function getAlertstocks(): ?int
    {
        return $this->alertstocks;
    }

    public function setAlertstocks(int $alertstocks): static
    {
        $this->alertstocks = $alertstocks;

        return $this;
    }

    public function getCriticalstocks(): ?int
    {
        return $this->criticalstocks;
    }

    public function setCriticalstocks(int $criticalstocks): static
    {
        $this->criticalstocks = $criticalstocks;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }


}
