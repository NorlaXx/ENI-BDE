<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, user>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'activities')]
    private Collection $registered;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    private ?Lieu $lieu = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActivityState $state = null;

    #[Assert\GreaterThan(propertyPath: 'registrationDateLimit')]
    #[Assert\GreaterThan('today')]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan('today')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationDateLimit = null;

    #[ORM\ManyToOne(inversedBy: 'ActivitiesOwner')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizer = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column]
    private ?int $nbLimitParticipants = null;

    public function __construct()
    {
        $this->registered = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getRegistered(): Collection
    {
        return $this->registered;
    }

    public function addInscrit(user $inscrit): static
    {
        if (!$this->registered->contains($inscrit)) {
            $this->registered->add($inscrit);
        }

        return $this;
    }

    public function removeInscrit(User $inscrit): static
    {
        $this->registered->removeElement($inscrit);

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getState(): ?ActivityState
    {
        return $this->state;
    }

    public function setState(?ActivityState $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getRegistrationDateLimit(): ?\DateTimeInterface
    {
        return $this->registrationDateLimit;
    }

    public function setRegistrationDateLimit(?\DateTimeInterface $registrationDateLimit): static
    {
        $this->registrationDateLimit = $registrationDateLimit;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getNbLimitParticipants(): ?int
    {
        return $this->nbLimitParticipants;
    }

    public function setNbLimitParticipants(int $nbLimitParticipants): static
    {
        $this->nbLimitParticipants = $nbLimitParticipants;

        return $this;
    }

    public function convertToJson(): false|string
    {

        //On
        $array = [
            "id" => str_replace(" ", "&@^", $this->getId()),
            "name" => str_replace(" ", "&@^",$this->getName()),
            "city" => str_replace(" ", "&@^",$this->getLieu()->getCity()),
            "lieu" => str_replace(" ", "&@^",$this->getLieu()->getAddress()),
            "description" => str_replace(" ", "&@^",$this->getDescription()),
            "state" => str_replace(" ", "&@^",$this->getState()->getCode()),
            "startDate" => str_replace(" ", "&@^",$this->getStartDate()->format('Y/m/d H:i')), // Assurez-vous que c'est une chaîne
            "registrationDateLimit" => str_replace(" ", "&@^",$this->getRegistrationDateLimit()->format('Y/m/d H:i')),
            "duration" => str_replace(" ", "&@^",$this->getDuration()),
            "fileName" => str_replace(" ", "&@^",$this->getFileName()),
            "creationDate" => str_replace(" ", "&@^",$this->getCreationDate()->format('Y/m/d H:i')),
            "nbLimitParticipants" => str_replace(" ", "&@^",$this->getNbLimitParticipants()),
            "nbParticipants" => str_replace(" ", "&@^",$this->getRegistered()->count())
        ];

        return json_encode($array);
    }

}
