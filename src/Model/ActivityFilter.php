<?php

namespace App\Model;

use App\Entity\Campus;

class ActivityFilter
{
    private ?string $name = null;
    private ?Campus $campus = null;
    private ?\DateTimeInterface $dateMin = null;
    private ?\DateTimeInterface $dateMax = null;
    private ?bool $organisateur = null;
    private ?bool $inscrit = null;
    private ?bool $finis = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getDateMin(): ?\DateTimeInterface
    {
        return $this->dateMin;
    }

    public function setDateMin(?\DateTimeInterface $dateMin): void
    {
        $this->dateMin = $dateMin;
    }

    public function getDateMax(): ?\DateTimeInterface
    {
        return $this->dateMax;
    }

    public function setDateMax(?\DateTimeInterface $dateMax): void
    {
        $this->dateMax = $dateMax;
    }

    public function getOrganisateur(): ?bool
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?bool $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    public function getInscrit(): ?bool
    {
        return $this->inscrit;
    }

    public function setInscrit(?bool $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    public function getFinis(): ?bool
    {
        return $this->finis;
    }

    public function setFinis(?bool $finis): void
    {
        $this->finis = $finis;
    }


}