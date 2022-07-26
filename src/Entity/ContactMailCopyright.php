<?php

namespace App\Entity;

use App\Repository\ContactMailCopyrightRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactMailCopyrightRepository::class)]
class ContactMailCopyright
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private $email = 'Vous pouvez nous contacter à l\'adresse : test@test.com';

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private $copyright = 'Kossy Beauty © 2022. Tous droits réservés.';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setCopyright(?string $copyright): self
    {
        $this->copyright = $copyright;

        return $this;
    }
}