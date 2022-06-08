<?php

namespace App\Entity;

use App\Repository\ProviderPlanningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderPlanningRepository::class)]
class ProviderPlanning
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'providerPlannings')]
    #[ORM\JoinColumn(nullable: false)]
    private $provider;

    #[ORM\Column(type: 'datetimetz')]
    private $workStart;

    #[ORM\Column(type: 'datetimetz')]
    private $workEnd;

    #[ORM\OneToMany(mappedBy: 'providerPlanning', targetEntity: Booking::class)]
    private $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getWorkStart(): ?\DateTimeInterface
    {
        return $this->workStart;
    }

    public function setWorkStart(\DateTimeInterface $workStart): self
    {
        $this->workStart = $workStart;

        return $this;
    }

    public function getWorkEnd(): ?\DateTimeInterface
    {
        return $this->workEnd;
    }

    public function setWorkEnd(\DateTimeInterface $workEnd): self
    {
        $this->workEnd = $workEnd;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setProviderPlanning($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getProviderPlanning() === $this) {
                $booking->setProviderPlanning(null);
            }
        }

        return $this;
    }
}
