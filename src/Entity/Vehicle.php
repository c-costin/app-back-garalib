<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=VehicleRepository::class)
 * @OA\Schema()
 */
class Vehicle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(type="interger", example="1")
     * @Groups({"read:Vehicle:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank
     * @OA\Property(type="string", example="voiture")
     * @Groups({"read:Vehicle:item"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @OA\Property(type="string", nullable=true, example="peugeot")
     * @Groups({"read:Vehicle:item"})
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @OA\Property(type="string", nullable=true, example="style 308")
     * @Groups({"read:Vehicle:item"})
     */
    private $model;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank
     * OA\Property(type="string", example="AA-123-AA")
     * @Groups({"read:Vehicle:item"})
     */
    private $numberPlate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * OA\Property(type="date", nullable=true, example="2016-09-01")
     * @Groups({"read:Vehicle:item"})
     */
    private $releaseDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * OA\Property(type="interger", nullable=true, example="240000")
     * @Groups({"read:Vehicle:item"})
     */
    private $mileage;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="vehicles")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getNumberPlate(): ?string
    {
        return $this->numberPlate;
    }

    public function setNumberPlate(string $numberPlate): self
    {
        $this->numberPlate = $numberPlate;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(?int $mileage): self
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
