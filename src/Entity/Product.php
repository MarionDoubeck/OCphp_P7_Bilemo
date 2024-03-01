<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * 
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_detailProduct",
 *          parameters = { "id" = "expr(object.getId())" }
 *      )
 * )
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;


    /**
     * Get the ID of the product.
     *
     * @return int|null The ID of the product.
     */
    public function getId(): ?int
    {
        return $this->id;

    }//end getId()


    /**
     * Get the model of the product.
     *
     * @return string|null The model of the product.
     */
    public function getModel(): ?string
    {
        return $this->model;

    }//end getModel()

    /**
     * Set the model of the product.
     *
     * @param string $model The model of the product.
     * 
     * @return self
     */
    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;

    }//end setModel()

    /**
     * Get the brand of the product.
     *
     * @return string|null The brand of the product.
     */
    public function getBrand(): ?string
    {
        return $this->brand;

    }//end getBrand()

    /**
     * Set the brand of the product.
     *
     * @param string $brand The brand of the product.
     * 
     * @return self
     */
    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;

    }//end setBrand()

    /**
     * Get the price of the product.
     *
     * @return float|null The price of the product.
     */
    public function getPrice(): ?float
    {
        return $this->price;

    }//end getPrice()

    /**
     * Set the price of the product.
     *
     * @param float $price The price of the product.
     * 
     * @return self
     */
    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;

    }//end setPrice()

    /**
     * Get the description of the product.
     *
     * @return string|null The description of the product.
     */
    public function getDescription(): ?string
    {
        return $this->description;

    }//end getDescription()

    /**
     * Set the description of the product.
     *
     * @param string|null $description The description of the product.
     * 
     * @return self
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;

    }//end setDescription()

    /**
     * Get the creation date of the product.
     *
     * @return \DateTimeImmutable|null The creation date of the product.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;

    }//end getCreatedAt()

    /**
     * Set the creation date of the product.
     *
     * @param \DateTimeImmutable $created_at The creation date of the product.
     * 
     * @return self
     */
    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;

    }//end setCreatedAt()


}
