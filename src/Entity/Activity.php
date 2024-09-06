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
    private Collection $inscrits;

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

    #[Assert\GreaterThan(propertyPath: 'dateFinalInscription')]
    #[Assert\GreaterThan('today')]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[Assert\NotBlank]
    #[Assert\GreaterThan('today')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFinalInscription = null;

    #[ORM\ManyToOne(inversedBy: 'ActivitiesOwner')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organisateur = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pictureFileName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column]
    private ?int $nbLimitParticipants = null;

    public function __construct()
    {
        $this->inscrits = new ArrayCollection();
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
    public function getInscrits(): Collection
    {
        return $this->inscrits;
    }

    public function addInscrit(user $inscrit): static
    {
        if (!$this->inscrits->contains($inscrit)) {
            $this->inscrits->add($inscrit);
        }

        return $this;
    }

    public function removeInscrit(User $inscrit): static
    {
        $this->inscrits->removeElement($inscrit);

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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFinalInscription(): ?\DateTimeInterface
    {
        return $this->dateFinalInscription;
    }

    public function setDateFinalInscription(?\DateTimeInterface $dateFinalInscription): static
    {
        $this->dateFinalInscription = $dateFinalInscription;

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getPictureFileName(): ?string
    {
        return $this->pictureFileName;
    }

    public function setPictureFileName(?string $pictureFileName): static
    {
        $this->pictureFileName = $pictureFileName;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

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
            "ville" => str_replace(" ", "&@^",$this->getLieu()->getVille()),
            "lieu" => str_replace(" ", "&@^",$this->getLieu()->getAddresse()),
            "description" => str_replace(" ", "&@^",$this->getDescription()),
            "state" => str_replace(" ", "&@^",$this->getState()->getCode()),
            "dateDebut" => str_replace(" ", "&@^",$this->getDateDebut()->format('Y/m/d H:i')), // Assurez-vous que c'est une chaîne
            "dateFinalInscription" => str_replace(" ", "&@^",$this->getDateFinalInscription()->format('Y/m/d H:i')),
            "duree" => str_replace(" ", "&@^",$this->getDuree()),
            "pictureFileName" => str_replace(" ", "&@^",$this->getPictureFileName()),
            "dateCreation" => str_replace(" ", "&@^",$this->getDateCreation()->format('Y/m/d H:i')),
            "nbLimitParticipants" => str_replace(" ", "&@^",$this->getNbLimitParticipants()),
            "nbParticipants" => str_replace(" ", "&@^",$this->getInscrits()->count())
        ];

        return json_encode($array);
    }

}
