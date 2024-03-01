<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
class Partner implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Consumer::class, mappedBy: 'partner', orphanRemoval: true)]
    private Collection $consumers;


    /**
     * Partner constructor.
     */
    public function __construct()
    {
        $this->consumers = new ArrayCollection();
    }


    /**
     * Get the ID of the partner.
     *
     * @return int|null The ID of the partner.
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Get the username of the partner.
     *
     * @return string|null The username of the partner.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }


    /**
     * Set the username of the partner.
     *
     * @param string $username The username of the partner.
     * 
     * @return self
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }


    /**
     * Get a visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string The visual identifier of the user.
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }


    /**
     * Get the roles of the partner.
     *
     * @see UserInterface
     * 
     * @return array The roles of the partner.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    /**
     * Set the roles of the partner.
     *
     * @param array $roles The roles of the partner.
     * 
     * @return self
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * Get the password of the partner.
     *
     * @see PasswordAuthenticatedUserInterface
     * 
     * @return string The password of the partner.
     */
    public function getPassword(): string
    {
        return $this->password;
    }


    /**
     * Set the password of the partner.
     *
     * @param string $password The password of the partner.
     * 
     * @return self
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Erase the partner's credentials.
     * 
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    /**
     * Get the consumers associated with the partner.
     * 
     * @return Collection<int, Consumer> The consumers associated with the partner.
     */
    public function getConsumers(): Collection
    {
        return $this->consumers;
    }


    /**
     * Add a consumer to the partner.
     *
     * @param Consumer $consumer The consumer to add.
     * 
     * @return self
     */
    public function addConsumer(Consumer $consumer): static
    {
        if (!$this->consumers->contains($consumer)) {
            $this->consumers->add($consumer);
            $consumer->setPartner($this);
        }

        return $this;
    }


    /**
     * Remove a consumer from the partner.
     *
     * @param Consumer $consumer The consumer to remove.
     * 
     * @return self
     */
    public function removeConsumer(Consumer $consumer): static
    {
        if ($this->consumers->removeElement($consumer)) {
            // set the owning side to null (unless already changed)
            if ($consumer->getPartner() === $this) {
                $consumer->setPartner(null);
            }
        }

        return $this;
    }
}
