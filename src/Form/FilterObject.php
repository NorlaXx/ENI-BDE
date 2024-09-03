<?php

namespace App\Form;

use App\Entity\Campus;
use Doctrine\DBAL\Types\DateType;

class FilterObject
{
    private String $name;
    private Campus $campus;
    private DateType $datemin;
    private DateType $dateMax;
    private bool $organisateur;
    private bool $inscrit;
    private bool $finis;


}