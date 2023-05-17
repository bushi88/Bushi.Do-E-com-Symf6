<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $moreInformation = null;

    #[ORM\Column]
    private ?float $Price = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isBestSeller = false;

    #[ORM\Column(nullable: true)]
    private ?bool $isNewArrival = false;

    #[ORM\Column(nullable: true)]
    private ?bool $isFeatured = false;

    #[ORM\Column(nullable: true)]
    private ?bool $isSpecialOffer = false;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: Categories::class, inversedBy: 'products')]
    private Collection $category;

    #[ORM\ManyToMany(targetEntity: TagsProduct::class, mappedBy: 'product')]
    private Collection $tagsProducts;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: RelatedProduct::class)]
    private Collection $relatedProducts;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ReviewsProduct::class)]
    private Collection $reviewsProducts;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->tagsProducts = new ArrayCollection();
        $this->relatedProducts = new ArrayCollection();
        $this->reviewsProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMoreInformation(): ?string
    {
        return $this->moreInformation;
    }

    public function setMoreInformation(string $moreInformation): self
    {
        $this->moreInformation = $moreInformation;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->Price;
    }

    public function setPrice(float $Price): self
    {
        $this->Price = $Price;

        return $this;
    }

    public function isIsBestSeller(): ?bool
    {
        return $this->isBestSeller;
    }

    public function setIsBestSeller(bool $isBestSeller): self
    {
        $this->isBestSeller = $isBestSeller;

        return $this;
    }

    public function isIsNewArrival(): ?bool
    {
        return $this->isNewArrival;
    }

    public function setIsNewArrival(?bool $isNewArrival): self
    {
        $this->isNewArrival = $isNewArrival;

        return $this;
    }

    public function isIsFeatured(): ?bool
    {
        return $this->isFeatured;
    }

    public function setIsFeatured(?bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;

        return $this;
    }

    public function isIsSpecialOffer(): ?bool
    {
        return $this->isSpecialOffer;
    }

    public function setIsSpecialOffer(?bool $isSpecialOffer): self
    {
        $this->isSpecialOffer = $isSpecialOffer;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Categories>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Categories $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Categories $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, TagsProduct>
     */
    public function getTagsProducts(): Collection
    {
        return $this->tagsProducts;
    }

    public function addTagsProduct(TagsProduct $tagsProduct): self
    {
        if (!$this->tagsProducts->contains($tagsProduct)) {
            $this->tagsProducts->add($tagsProduct);
            $tagsProduct->addProduct($this);
        }

        return $this;
    }

    public function removeTagsProduct(TagsProduct $tagsProduct): self
    {
        if ($this->tagsProducts->removeElement($tagsProduct)) {
            $tagsProduct->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, RelatedProduct>
     */
    public function getRelatedProducts(): Collection
    {
        return $this->relatedProducts;
    }

    public function addRelatedProduct(RelatedProduct $relatedProduct): self
    {
        if (!$this->relatedProducts->contains($relatedProduct)) {
            $this->relatedProducts->add($relatedProduct);
            $relatedProduct->setProduct($this);
        }

        return $this;
    }

    public function removeRelatedProduct(RelatedProduct $relatedProduct): self
    {
        if ($this->relatedProducts->removeElement($relatedProduct)) {
            // set the owning side to null (unless already changed)
            if ($relatedProduct->getProduct() === $this) {
                $relatedProduct->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReviewsProduct>
     */
    public function getReviewsProducts(): Collection
    {
        return $this->reviewsProducts;
    }

    public function addReviewsProduct(ReviewsProduct $reviewsProduct): self
    {
        if (!$this->reviewsProducts->contains($reviewsProduct)) {
            $this->reviewsProducts->add($reviewsProduct);
            $reviewsProduct->setProduct($this);
        }

        return $this;
    }

    public function removeReviewsProduct(ReviewsProduct $reviewsProduct): self
    {
        if ($this->reviewsProducts->removeElement($reviewsProduct)) {
            // set the owning side to null (unless already changed)
            if ($reviewsProduct->getProduct() === $this) {
                $reviewsProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}