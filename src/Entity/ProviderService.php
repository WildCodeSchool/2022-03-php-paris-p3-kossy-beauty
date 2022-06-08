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

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'providerServices')]
    #[ORM\JoinColumn(nullable: false)]
    private $provider;

    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'providerServices')]
    #[ORM\JoinColumn(nullable: false)]
    private $service;

    #[ORM\Column(type: 'integer')]
    private $price;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $duration;

    #[ORM\OneToMany(mappedBy: 'providerService', targetEntity: Booking::class)]
    private $bookings;

    #[ORM\OneToMany(mappedBy: 'providerService', targetEntity: Message::class)]
    private $messages;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->messages = new ArrayCollection();
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
            $booking->setProviderService($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getProviderService() === $this) {
                $booking->setProviderService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setProviderService($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getProviderService() === $this) {
                $message->setProviderService(null);
            }
        }

        return $this;
    }
}
