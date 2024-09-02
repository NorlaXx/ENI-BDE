<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActivityState $state = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateTimeateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFinalInscription = null;

    #[ORM\ManyToOne(inversedBy: 'ActivitiesOwner')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organisateur = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pictureFileName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

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

    public function removeInscrit(user $inscrit): static
    {
        $this->inscrits->removeElement($inscrit);

        return $this;
    }

    public function getCampus(): ?campus
    {
        return $this->campus;
    }

    public function setCampus(?campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getLieu(): ?lieu
    {
        return $this->lieu;
    }

    public function setLieu(?lieu $lieu): static
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
        return $this->DateDebut;
    }

    public function setDateDebut(\DateTimeInterface $DateDebut): static
    {
        $this->DateDebut = $DateDebut;

        return $this;
    }

    public function getDateFinalInscription(): ?\DateTimeInterface
    {
        return $this->dateFinalInscription;
    }

    public function setDateFinalInscription(\DateTimeInterface $dateFinalInscription): static
    {
        $this->dateFinalInscription = $dateFinalInscription;

        return $this;
    }

    public function getOrganisateur(): ?user
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?user $organisateur): static
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
}
