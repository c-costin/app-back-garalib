<?php

namespace App\Entity;

use App\Repository\GarageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GarageRepository::class)
 * @OA\Schema()
 */
class Garage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:Garage:collection", "read:Garage:item", "read:User:item", "read:Type:item", "read:Schedule:item","read:Appointment:item", "read:Review:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     * @OA\Property(type="string", example="AutoGarage2000")
     * @Groups({"read:Garage:collection", "read:Garage:item"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=17)
     * @Assert\NotBlank
     * @Assert\Length(17)
     * @OA\Property(type="string", example="231 190 987 12315")
     * @Groups({"read:Garage:item"})
     */
    private $registerNumber;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\NotBlank
     * @OA\Property(type="string", example="0412258462")
     * @Groups({"read:Garage:collection", "read:Garage:item"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @OA\Property(type="string", example="john.doe@mail.com")
     * @Groups({"read:Garage:collection", "read:Garage:item"})
     */
    private $email;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1, nullable=true)
     * @OA\Property(type="integer", example="3.9")
     * @Groups({"read:Garage:collection", "read:Garage:item"})
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="garage")
     * @Groups({"read:Garage:item"})
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Appointment::class, mappedBy="garage", cascade={"remove"})
     */
    private $appointments;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="garage", cascade={"remove"})
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity=Schedule::class, mappedBy="garage", orphanRemoval=true)
     * 
     */
    private $schedules;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity=Type::class, mappedBy="garage", orphanRemoval=true)
     */
    private $types;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->users = new ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        $this->types = new ArrayCollection();
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

    public function getRegisterNumber(): ?string
    {
        return $this->registerNumber;
    }

    public function setRegisterNumber(string $registerNumber): self
    {
        $this->registerNumber = $registerNumber;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): self
    {
        $this->rating = $rating;

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addGarage($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeGarage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments[] = $appointment;
            $appointment->setGarage($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getGarage() === $this) {
                $appointment->setGarage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setGarage($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getGarage() === $this) {
                $review->setGarage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Schedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules[] = $schedule;
            $schedule->setGarage($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->removeElement($schedule)) {
            // set the owning side to null (unless already changed)
            if ($schedule->getGarage() === $this) {
                $schedule->setGarage(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Type>
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(Type $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
            $type->setGarage($this);
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        if ($this->types->removeElement($type)) {
            // set the owning side to null (unless already changed)
            if ($type->getGarage() === $this) {
                $type->setGarage(null);
            }
        }

        return $this;
    }
}
