<?php

namespace App\Model;

use App\Entity\Campus;

class ActivityFilter
{
    private ?string $name = null;
    private ?Campus $campus = null;
    private ?\DateTimeInterface $minDate = null;
    private ?\DateTimeInterface $maxDate = null;
    private ?bool $organizer = null;
    private ?bool $registered = null;
    private ?bool $notRegistered = null;
    private ?bool $finished = null;

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

    public function getMinDate(): ?\DateTimeInterface
    {
        return $this->minDate;
    }

    public function setMinDate(?\DateTimeInterface $minDate): void
    {
        $this->minDate = $minDate;
    }

    public function getMaxDate(): ?\DateTimeInterface
    {
        return $this->maxDate;
    }

    public function setMaxDate(?\DateTimeInterface $maxDate): void
    {
        $this->maxDate = $maxDate;
    }

    public function getOrganizer(): ?bool
    {
        return $this->organizer;
    }

    public function setOrganizer(?bool $organizer): void
    {
        $this->organizer = $organizer;
    }

    public function getRegistered(): ?bool
    {
        return $this->registered;
    }

    public function setRegistered(?bool $registered): void
    {
        $this->registered = $registered;
    }

    public function getNotRegistered(): ?bool
    {
        return $this->notRegistered;
    }

    public function setNotRegistered(?bool $notRegistered): void
    {
        $this->notRegistered = $notRegistered;
    }

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(?bool $finished): void
    {
        $this->finished = $finished;
    }


}