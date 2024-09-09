<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(name: 'email', type: 'string', length: 255, unique: true)]
    #[Assert\Email]
    private ?string $email = null;

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

    #[ORM\Column(length: 255)]
    private ?string $phone_number = null;
    #[Assert\NotBlank]
    #[Assert\Unique]
    #[Assert\Length(min: 3)]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    /**
     * @var Collection<int, Activity>
     */
    #[ORM\ManyToMany(targetEntity: Activity::class, mappedBy: 'registered')]
    private Collection $activities;
    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $Campus = null;
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): static
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->addInscrit($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            $activity->removeInscrit($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivitiesOwner(): Collection
    {
        return $this->ActivitiesOwner;
    }

    public function addActivitiesOwner(Activity $activitiesOwner): static
    {
        if (!$this->ActivitiesOwner->contains($activitiesOwner)) {
            $this->ActivitiesOwner->add($activitiesOwner);
            $activitiesOwner->setOrganizer($this);
        }

        return $this;
    }

    public function removeActivitiesOwner(Activity $activitiesOwner): static
    {
        if ($this->ActivitiesOwner->removeElement($activitiesOwner)) {
            // set the owning side to null (unless already changed)
            if ($activitiesOwner->getOrganizer() === $this) {
                $activitiesOwner->setOrganizer(null);
            }
        }

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->Campus;
    }

    public function setCampus(?Campus $Campus): static
    {
        $this->Campus = $Campus;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function convertToJson()
    {
        return json_encode([
            'id' => str_replace(" ", "&@^", $this->getId()),
            'email' => str_replace(" ", "&@^", $this->getEmail()),
            'roles' => str_replace(" ", "&@^", $this->getRoles()),
            'phone_number' => str_replace(" ", "&@^", $this->getPhoneNumber()),
            'pseudo' => str_replace(" ", "&@^", $this->getPseudo()),
            'fileName' => str_replace(" ", "&@^", $this->getFileName()),
            'isActive' => str_replace(" ", "&@^", $this->isActive()),
            'lastName' => str_replace(" ", "&@^", $this->getLastName()),
            'firstName' => str_replace(" ", "&@^", $this->getFirstName()),
        ]);
    }
}
