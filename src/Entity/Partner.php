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

    /**
     * The unique identifier of the partner.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * The username of the partner.
     *
     * @var string|null
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    /**
     * The roles assigned to the partner.
     *
     * @var array
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * The hashed password of the partner.
     *
     * @var string|null
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * The consumers associated with the partner.
     *
     * @var Collection
     */
    #[ORM\OneToMany(targetEntity: Consumer::class, mappedBy: 'partner', orphanRemoval: true)]
    private Collection $consumers;


    /**
     * Partner constructor.
     */
    public function __construct()
    {
        $this->consumers = new ArrayCollection();

    }//end __construct()


    /**
     * Get the ID of the partner.
     *
     * @return int|null The ID of the partner.
     */
    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    /**
     * Get the username of the partner.
     *
     * @return string|null The username of the partner.
     */
    public function getUsername(): ?string
    {
        return $this->username;

    }//end getUsername()


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

    }//end setUsername()


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

    }//end getUserIdentifier()


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
        // Guarantee every user at least has ROLE_USER.
        $roles[] = 'ROLE_USER';

        return array_unique($roles);

    }//end getRoles()


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

    }//end setRoles()


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

    }//end getPassword()


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

    }//end setPassword()

    /**
     * Erase the partner's credentials.
     * 
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here.
        // $this->plainPassword = null;.

    }//end eraseCredentials()


    /**
     * Get the consumers associated with the partner.
     * 
     * @return Collection<int, Consumer> The consumers associated with the partner.
     */
    public function getConsumers(): Collection
    {
        return $this->consumers;

    }//end getConsumers()


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

    }//end addConsumer()


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
            // Set the owning side to null (unless already changed).
            if ($consumer->getPartner() === $this) {
                $consumer->setPartner(null);
            }
        }

        return $this;

    }//end removeConsumer()


}
