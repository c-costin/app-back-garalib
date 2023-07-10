<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @OA\Schema()
 * @UniqueEntity("email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:User:item", "read:Garage:item", "read:Appointment:item", "read:Review:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank
     * @Assert\Email
     * @OA\Property(type="string", example="nom@domain.com")
     * @Groups({"read:User:item"})
     */
    private $email;


    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank
     * @OA\Property(type="string", example="toto/*5625#dada")
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotBlank
     * @OA\Property(type="array", @OA\Items(example="['ROLE_EXAMPLE']"))
     * @Groups({"read:User:item"})
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     * @OA\Property(type="string", example="toto")
     * @Groups({"read:User:item"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank
     * @OA\Property(type="string", example="tutu")
     * @Groups({"read:User:item"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank
     * @OA\Property(type="string", example="0695483215")
     * @Groups({"read:User:item"})
     */
    private $phone;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     * @OA\Property(type="date", example="1984-05-04")
     * @Groups({"read:User:item"})
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity=Address::class, orphanRemoval=true)
     */
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity=Garage::class, inversedBy="users")
     * @Groups({"read:User:item"})
     */
    private $garage;

    /**
     * @ORM\OneToMany(targetEntity=Vehicle::class, mappedBy="user", orphanRemoval=true)
     */
    private $vehicles;

    /**
     * @ORM\OneToMany(targetEntity=Appointment::class, mappedBy="user", orphanRemoval=true)
     */
    private $appointments;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="user", orphanRemoval=true)
     */
    private $reviews;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->garage = new ArrayCollection();
        $this->vehicles = new ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

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

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Garage>
     */
    public function getGarage(): Collection
    {
        return $this->garage;
    }

    public function addGarage(Garage $garage): self
    {
        if (!$this->garage->contains($garage)) {
            $this->garage[] = $garage;
        }

        return $this;
    }

    public function removeGarage(Garage $garage): self
    {
        $this->garage->removeElement($garage);

        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicle $vehicle): self
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles[] = $vehicle;
            $vehicle->setUser($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): self
    {
        if ($this->vehicles->removeElement($vehicle)) {
            // set the owning side to null (unless already changed)
            if ($vehicle->getUser() === $this) {
                $vehicle->setUser(null);
            }
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
            $appointment->setUser($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getUser() === $this) {
                $appointment->setUser(null);
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
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }
}
