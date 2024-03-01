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

    /**
     * The unique identifier of the consumer.
     *
     * @var integer|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getPartner"])]
    private ?int $id = null;

    /**
     * The first name of the consumer.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Groups(["getPartner"])]
    #[Assert\NotBlank(message: "Le prénom du client est obligatoire")]
    #[Assert\Length(min:1, max: 255, minMessage:"Le prénom du client doit faire au moins {{limit}} caractère(s)", maxMessage:"Le prénom du client doit faire au plus {{limit}} caractères")]
    private ?string $firstName = null;

    /**
     * The last name of the consumer.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Groups(["getPartner"])]
    #[Assert\NotBlank(message: "Le nom du client est obligatoire")]
    #[Assert\Length(min:1, max: 255, minMessage:"Le nom du client doit faire au moins {{limit}} caractère(s)", maxMessage:"Le nom du client doit faire au plus {{limit}} caractères")]
    private ?string $lastName = null;

    /**
     * The email address of the consumer.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    #[Groups(["getPartner"])]
    #[Assert\NotBlank(message: "L'adresse email du client est obligatoire")]
    #[Assert\Email(message: "L'adresse email du client n'est pas valide")]
    private ?string $email = null;

    /**
     * The address of the consumer.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["getPartner"])]
    private ?string $adress = null;

    /**
     * The postal code of the consumer.
     *
     * @var string|null
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(["getPartner"])]
    private ?string $postCode = null;

    /**
     * The city of the consumer.
     *
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["getPartner"])]
    private ?string $city = null;

    /**
     * The partner associated with the consumer.
     *
     * @var Partner|null
     */
    #[ORM\ManyToOne(inversedBy: 'consumers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partner $partner = null;


    /**
     * Get the ID of the consumer.
     *
     * @return integer|null The ID of the consumer.
     */
    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    /**
     * Get the first name of the consumer.
     *
     * @return string|null The first name of the consumer.
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;

    }//end getFirstName()


    /**
     * Set the first name of the consumer.
     *
     * @param string $firstName The first name of the consumer.
     *
     * @return self
     */
    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;

    }//end setFirstName()


    /**
     * Get the last name of the consumer.
     *
     * @return string|null The last name of the consumer.
     */
    public function getLastName(): ?string
    {
        return $this->lastName;

    }//end getLastName()


    /**
     * Set the last name of the consumer.
     *
     * @param string $lastName The last name of the consumer.
     *
     * @return self
     */
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;

    }//end setLastName()


    /**
     * Get the email of the consumer.
     *
     * @return string|null The email of the consumer.
     */
    public function getEmail(): ?string
    {
        return $this->email;

    }//end getEmail()


    /**
     * Set the email of the consumer.
     *
     * @param string $email The email of the consumer.
     * 
     * @return self
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;

    }//end setEmail()


    /**
     * Get the address of the consumer.
     *
     * @return string|null The address of the consumer.
     */
    public function getAdress(): ?string
    {
        return $this->adress;

    }//end getAdress()


    /**
     * Set the address of the consumer.
     *
     * @param string|null $adress The address of the consumer.
     * 
     * @return self
     */
    public function setAdress(?string $adress): static
    {
        $this->adress = $adress;

        return $this;

    }//end setAdress()


    /**
     * Get the postal code of the consumer.
     *
     * @return string|null The postal code of the consumer.
     */
    public function getPostCode(): ?string
    {
        return $this->postCode;

    }//end getPostCode()


    /**
     * Set the postal code of the consumer.
     *
     * @param string|null $postCode The postal code of the consumer.
     * 
     * @return self
     */
    public function setPostCode(?string $postCode): static
    {
        $this->postCode = $postCode;

        return $this;

    }//end setPostCode()


    /**
     * Get the city of the consumer.
     *
     * @return string|null The city of the consumer.
     */
    public function getCity(): ?string
    {
        return $this->city;

    }//end getCity()


    /**
     * Set the city of the consumer.
     *
     * @param string|null $city The city of the consumer.
     * 
     * @return self
     */
    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;

    }//end setCity()


    /**
     * Get the partner in whose client portfolio the consumer belongs.
     *
     * @return Partner|null The partner corresponding to the consumer.
     */
    public function getPartner(): ?Partner
    {
        return $this->partner;

    }//end getPartner()


    /**
     * Set the partner in whose client portfolio the consumer belongs.
     *
     * @param Partner|null $partner The partner corresponding to the consumer.
     * 
     * @return self
     */
    public function setPartner(?Partner $partner): static
    {
        $this->partner = $partner;

        return $this;

    }//end setPartner()


}//end class
