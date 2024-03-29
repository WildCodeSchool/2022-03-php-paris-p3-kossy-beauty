<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['telephone'], message: 'Il y a déjà un compte avec ce numéro de téléphone')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Regex(
        pattern: '/[^0-9]/',
        match: false,
        message: 'Votre numéro de téléphone ne peut pas contenir de lettre ou de caractères spéciaux.',
    )]
    #[Assert\Length(
        min: 8,
        minMessage: 'Le numéro de téléphone saisi est trop court, il doit faire {{ limit }} caractères au minimum',
        max: 10,
        maxMessage: 'Le numéro de téléphone saisi est trop long, il ne doit pas dépasser {{ limit }} caractères',
    )]
    private $telephone;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    // #[Assert\NotBlank(message: 'Veuillez remplir ce champs. password')]
    // #[Assert\Length(
    //     min: 8,
    // )]
    #[Assert\Type(
        type: 'string',
        message: 'Le format du mot de passe est incorrect',
    )]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Type(
        type: 'string',
        message: 'Le format du prénom est incorrect',
    )]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Votre prénom ne peut pas contenir de chiffre.',
    )]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    #[Assert\Type(
        type: 'string',
        message: 'Le format du nom de famille est incorrect',
    )]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Votre nom ne peut pas contenir de chiffre.',
    )]
    private $lastname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $companyName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Email(
        message: 'Veuillez renseigner ce champ avec un email valide.'
    )]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Veuillez remplir ce champ.')]
    private $town;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $district;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[Vich\UploadableField(mapping: 'provider_file', fileNameProperty: 'image')]
    #[Assert\File(
        maxSize: '1M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
    )]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'boolean')]
    private $isTop = false;

    #[ORM\Column(type: 'boolean')]
    private $isArchived = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private $companyDescription;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProviderService::class)]
    private $providerServices;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\ManyToMany(targetEntity: Conversation::class, mappedBy: 'users')]
    private $conversations;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Message::class)]
    private $messages;

    public function __construct()
    {
        $this->providerServices = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->telephone;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): self
    {
        $this->district = $district;

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

    public function setImageFile(File $image = null): self
    {
        $this->imageFile = $image;
        if ($image) {
            $this->updatedAt = new DateTime('now');
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function isIsTop(): ?bool
    {
        return $this->isTop;
    }

    public function setIsTop(bool $isTop): self
    {
        $this->isTop = $isTop;

        return $this;
    }

    public function isIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getCompanyDescription(): ?string
    {
        return $this->companyDescription;
    }

    public function setCompanyDescription(?string $companyDescription): self
    {
        $this->companyDescription = $companyDescription;

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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->addUser($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeUser($this);
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
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }
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

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->telephone,
            $this->roles,
            $this->password,
            $this->firstname,
            $this->lastname,
            $this->companyName,
            $this->email,
            $this->town,
            $this->district,
            $this->image,
            $this->updatedAt,
            $this->isTop,
            $this->isArchived,
            $this->companyDescription,
            $this->isVerified
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->telephone,
            $this->roles,
            $this->password,
            $this->firstname,
            $this->lastname,
            $this->companyName,
            $this->email,
            $this->town,
            $this->district,
            $this->image,
            $this->updatedAt,
            $this->isTop,
            $this->isArchived,
            $this->companyDescription,
            $this->isVerified
        ) = unserialize($serialized);
    }
}