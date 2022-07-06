<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: ProviderService::class)]
    private $providerServices;

    public function __construct()
    {
        $this->providerServices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, ProviderService>
     */
    public function getProviderServices(): Collection
    {
        return $this->providerServices;
    }

    public function addProviderService(ProviderService $providerService): self
    {
        if (!$this->providerServices->contains($providerService)) {
            $this->providerServices[] = $providerService;
            $providerService->setService($this);
        }

        return $this;
    }

    public function removeProviderService(ProviderService $providerService): self
    {
        if ($this->providerServices->removeElement($providerService)) {
            // set the owning side to null (unless already changed)
            if ($providerService->getService() === $this) {
                $providerService->setService(null);
            }
        }

        return $this;
    }
}
