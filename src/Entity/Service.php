<?php

namespace App\Entity;

use App\Entity\ServiceCategory;
use App\Repository\ServiceRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 * @UniqueEntity("name", message="Ce nom est déjà utilisé !")
 */
class Service
{
    public const STATUS_AVAILABLE = 'Disponible';
    public const STATUS_UNAVAILABLE = 'Indisponible';

    public function __toString()
    {
        return "service";
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom du service est obligatoire !")
     * @Assert\Length(min=3, max=255, minMessage="Le nom du service doit avoir au moins {{ limit }} caractères", maxMessage="Le nom du service doit avoir au plus {{ limit }} caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Le prix du service est obligatoire !")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="La description du service est obligatoire !")
     * @Assert\Length(min=10, minMessage="La description du service doit avoir au moins {{ limit }} caractères")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="La date est obligatoire !")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $main_picture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceCategory::class, inversedBy="services")
     * @Assert\NotBlank(message="La catégorie est obligatoire !")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseItem::class, mappedBy="service")
     */
    private $purchaseItems;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->purchaseItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->main_picture;
    }

    public function setMainPicture(?string $main_picture): self
    {
        $this->main_picture = $main_picture;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?ServiceCategory
    {
        return $this->category;
    }

    public function setCategory(?ServiceCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEntity(): ?string
    {
        return "service";
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): self
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems[] = $purchaseItem;
            $purchaseItem->setService($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): self
    {
        if ($this->purchaseItems->removeElement($purchaseItem)) {
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getService() === $this) {
                $purchaseItem->setService(null);
            }
        }

        return $this;
    }
}
