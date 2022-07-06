<?php

namespace App\Entity;

use App\Repository\ProviderServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderServiceRepository::class)]
class ProviderService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'providerServices')]
    #[ORM\JoinColumn(nullable: false)]
    private $provider;

    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'providerServices')]
    #[ORM\JoinColumn(nullable: false)]
    private $service;

    #[ORM\Column(type: 'integer')]
    private $price;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $duration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): ?User
    {
        return $this->provider;
    }

    public function setProvider(?User $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}
