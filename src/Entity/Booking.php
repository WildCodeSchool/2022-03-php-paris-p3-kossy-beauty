<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: ProviderService::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private $providerService;

    #[ORM\ManyToOne(targetEntity: ProviderPlanning::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private $providerPlanning;

    #[ORM\Column(type: 'datetimetz')]
    private $serviceStart;

    #[ORM\Column(type: 'datetimetz')]
    private $serviceEnd;

    #[ORM\OneToOne(mappedBy: 'booking', targetEntity: Invoice::class, cascade: ['persist', 'remove'])]
    private $invoice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProviderService(): ?ProviderService
    {
        return $this->providerService;
    }

    public function setProviderService(?ProviderService $providerService): self
    {
        $this->providerService = $providerService;

        return $this;
    }

    public function getProviderPlanning(): ?ProviderPlanning
    {
        return $this->providerPlanning;
    }

    public function setProviderPlanning(?ProviderPlanning $providerPlanning): self
    {
        $this->providerPlanning = $providerPlanning;

        return $this;
    }

    public function getServiceStart(): ?\DateTimeInterface
    {
        return $this->serviceStart;
    }

    public function setServiceStart(\DateTimeInterface $serviceStart): self
    {
        $this->serviceStart = $serviceStart;

        return $this;
    }

    public function getServiceEnd(): ?\DateTimeInterface
    {
        return $this->serviceEnd;
    }

    public function setServiceEnd(\DateTimeInterface $serviceEnd): self
    {
        $this->serviceEnd = $serviceEnd;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): self
    {
        // set the owning side of the relation if necessary
        if ($invoice->getBooking() !== $this) {
            $invoice->setBooking($this);
        }

        $this->invoice = $invoice;

        return $this;
    }
}
