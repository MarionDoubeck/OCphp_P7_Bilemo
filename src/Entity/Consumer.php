<?php

namespace App\Entity;

use App\Repository\ConsumerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_detailConsumer",
 *          parameters = { 
 *              "partner_id" = "expr(object.getPartner().getId())",
 *              "id" = "expr(object.getId())" 
 *          }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getPartner")
 * )
 *
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "api_deleteConsumer",
 *          parameters = { 
 *              "partner_id" = "expr(object.getPartner().getId())",
 *              "id" = "expr(object.getId())" 
 *          },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getPartner")
 * )
 */
#[ORM\Entity(repositoryClass: ConsumerRepository::class)]
class Consumer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getPartner"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getPartner"])]
    #[Assert\NotBlank(message: "Le prénom du client est obligatoire")]
    #[Assert\Length(min:1, max: 255, minMessage:"Le prénom du client doit faire au moins {{limit}} caractère(s)", maxMessage:"Le prénom du client doit faire au plus {{limit}} caractères")]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getPartner"])]
    #[Assert\NotBlank(message: "Le nom du client est obligatoire")]
    #[Assert\Length(min:1, max: 255, minMessage:"Le nom du client doit faire au moins {{limit}} caractère(s)", maxMessage:"Le nom du client doit faire au plus {{limit}} caractères")]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getPartner"])]
    #[Assert\NotBlank(message: "L'adresse email du client est obligatoire")]
    #[Assert\Email(message: "L'adresse email du client n'est pas valide")]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["getPartner"])]
    private ?string $adress = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(["getPartner"])]
    private ?string $postCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["getPartner"])]
    private ?string $city = null;

    #[ORM\ManyToOne(inversedBy: 'consumers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partner $partner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): static
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): static
    {
        $this->partner = $partner;

        return $this;
    }
}
