<?php

namespace App\Tests\Activity;

use App\Entity\Activity;
use App\Entity\ActivityState;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\User;
use App\Repository\ActivityRepository;
use PHPUnit\Framework\TestCase;

class ActivityTest extends TestCase
{
    public function testFindAllWithEmpty(): void
    {
        $activityRepository = $this->createMock(ActivityRepository::class);
        $activityRepository->method('findAll')
            ->willReturn([]);

        $this->assertCount(0, $activityRepository->findAll());
    }

    public function testFindAll(): void
    {
        $campus = new Campus();
        $campus->setName('ENI - Rennes');
        $campus->setPostalCode('35131');
        $campus->setLongitude(1.234567);
        $campus->setLatitude(1.234567);
        $campus->setAddress('8 rue Léo Lagrange');
        $campus->setFileName('file.jpg');
        $campus->setNblimitPlaces(10);
        $campus->setCity('Chartres de bretagne');

        $lieu = new Lieu();
        $lieu->setName('picine de Bréquiny');
        $lieu->setLongitude(1.234567);
        $lieu->setLatitude(1.234567);
        $lieu->setAddress('12 Bd Albert 1er');
        $lieu->setPostalCode('35000');
        $lieu->setCity('Rennes');
        $lieu->setFileName('file.jpg');

        $user = new User();
        $user->setActive(true);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('password');
        $user->setEmail('email@email.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setPhoneNumber('0606060606');
        $user->setPseudo('johndoe');
        $user->setCampus($campus);
        $user->setFileName('file.jpg');

        $activityState = new ActivityState();
        $activityState->setCode('ACT_CR');
        $activityState->setWording('Créée');

        $activity1 = new Activity();
        $activity1->setName('Activity 1');
        $activity1->setDescription('Description 1');
        $activity1->setDuration(40);
        $activity1->setCampus($campus);
        $activity1->setLieu($lieu);
        $activity1->setOrganizer($user);
        $activity1->setFileName('file.jpg');
        $activity1->setNbLimitParticipants(10);
        $activity1->setRegistrationDateLimit(new \DateTime('2024-09-30 23:59:00'));
        $activity1->setStartDate(new \DateTime('2024-10-01 14:00:00'));
        $activity1->setCreationDate(new \DateTime());
        $activity1->setState($activityState);

        $activityRepository = $this->createMock(ActivityRepository::class);
        $activityRepository->method('findAll')
            ->willReturn([$activity1]);

        $activities = $activityRepository->findAll();

        $this->assertEquals('Activity 1', $activities[0]->getName());
        $this->assertEquals('Description 1', $activities[0]->getDescription());
    }
}