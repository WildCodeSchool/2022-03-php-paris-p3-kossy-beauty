<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[Vich\Uploadable]
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

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[Vich\UploadableField(mapping: 'service_file', fileNameProperty: 'image')]
    #[Assert\File(
        maxSize: '1M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
    )]
    private ?File $serviceFile = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->providerServices = new ArrayCollection();
        $this->updatedAt = new DateTime();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of serviceFile
     */
    public function getServiceFile(): ?File
    {
        return $this->serviceFile;
    }

    /**
     * Set the value of serviceFile
     *
     * @return  self
     */
    public function setServiceFile($image): self
    {
        $this->serviceFile = $image;
        if ($image) {
            $this->updatedAt = new DateTime('now');
        }
        return $this;
    }
}
