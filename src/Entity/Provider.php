<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'provider', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $companyName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $companyNumber;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $taxNumber;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Favorite::class)]
    private $favorites;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: ProviderService::class)]
    private $providerServices;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Catalog::class)]
    private $catalogs;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: ProviderPlanning::class)]
    private $providerPlannings;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Comment::class)]
    private $comments;

    public function __construct()
    {
        $this->favorites = new ArrayCollection();
        $this->providerServices = new ArrayCollection();
        $this->catalogs = new ArrayCollection();
        $this->providerPlannings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCompanyNumber(): ?string
    {
        return $this->companyNumber;
    }

    public function setCompanyNumber(?string $companyNumber): self
    {
        $this->companyNumber = $companyNumber;

        return $this;
    }

    public function getTaxNumber(): ?string
    {
        return $this->taxNumber;
    }

    public function setTaxNumber(?string $taxNumber): self
    {
        $this->taxNumber = $taxNumber;

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    // public function getFavorites(): Collection
    // {
    //     return $this->favorites;
    // }

    // public function addFavorite(Favorite $favorite): self
    // {
    //     if (!$this->favorites->contains($favorite)) {
    //         $this->favorites[] = $favorite;
    //         $favorite->setProvider($this);
    //     }

    //     return $this;
    // }

    // public function removeFavorite(Favorite $favorite): self
    // {
    //     if ($this->favorites->removeElement($favorite)) {
    //         // set the owning side to null (unless already changed)
    //         if ($favorite->getProvider() === $this) {
    //             $favorite->setProvider(null);
    //         }
    //     }

    //     return $this;
    // }

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
            $providerService->setProvider($this);
        }

        return $this;
    }

    public function removeProviderService(ProviderService $providerService): self
    {
        if ($this->providerServices->removeElement($providerService)) {
            // set the owning side to null (unless already changed)
            if ($providerService->getProvider() === $this) {
                $providerService->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Catalog>
     */
    public function getCatalogs(): Collection
    {
        return $this->catalogs;
    }

    public function addCatalog(Catalog $catalog): self
    {
        if (!$this->catalogs->contains($catalog)) {
            $this->catalogs[] = $catalog;
            $catalog->setProvider($this);
        }

        return $this;
    }

    public function removeCatalog(Catalog $catalog): self
    {
        if ($this->catalogs->removeElement($catalog)) {
            // set the owning side to null (unless already changed)
            if ($catalog->getProvider() === $this) {
                $catalog->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProviderPlanning>
     */
    public function getProviderPlannings(): Collection
    {
        return $this->providerPlannings;
    }

    public function addProviderPlanning(ProviderPlanning $providerPlanning): self
    {
        if (!$this->providerPlannings->contains($providerPlanning)) {
            $this->providerPlannings[] = $providerPlanning;
            $providerPlanning->setProvider($this);
        }

        return $this;
    }

    public function removeProviderPlanning(ProviderPlanning $providerPlanning): self
    {
        if ($this->providerPlannings->removeElement($providerPlanning)) {
            // set the owning side to null (unless already changed)
            if ($providerPlanning->getProvider() === $this) {
                $providerPlanning->setProvider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setProvider($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProvider() === $this) {
                $comment->setProvider(null);
            }
        }

        return $this;
    }
}
