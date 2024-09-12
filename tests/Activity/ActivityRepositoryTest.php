<?php

namespace App\Tests\Activity;

use App\Entity\Activity;
use App\Entity\ActivityState;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ActivityRepositoryTest extends KernelTestCase
{
    public function setUp(): void
    {
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);
        $campusRepository = $em->getRepository(Campus::class);
        $lieuRepository = $em->getRepository(Lieu::class);
        $activityRepository = $em->getRepository(Activity::class);
        $activistyStateRepository = $em->getRepository(ActivityState::class);

        //Remove all campus
        foreach ($campusRepository->findAll() as $campus) {
            $em->remove($campus);
        }
        //Remove all lieu
        foreach ($lieuRepository->findAll() as $lieu) {
            $em->remove($lieu);
        }
        // Remove all users
        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }
        //Remove all activityState
        foreach ($activistyStateRepository->findAll() as $activityState) {
            $em->remove($activityState);
        }
        //Remove all activity
        foreach ($activityRepository->findAll() as $activity) {
            $em->remove($activity);
        }

        $em->flush();

        //Create a Campus fixture
        $campus = new Campus();
        $campus->setName('ENI - Rennes');
        $campus->setPostalCode('35131');
        $campus->setLongitude(1.234567);
        $campus->setLatitude(1.234567);
        $campus->setAddress('8 rue Léo Lagrange');
        $campus->setFileName('file.jpg');
        $campus->setNblimitPlaces(10);
        $campus->setCity('Chartres de bretagne');

        $em->persist($campus);
        $em->flush();

        //Create a Lieu fixture
        $lieu = new Lieu();
        $lieu->setName('picine de Bréquiny');
        $lieu->setLongitude(1.234567);
        $lieu->setLatitude(1.234567);
        $lieu->setAddress('12 Bd Albert 1er');
        $lieu->setPostalCode('35000');
        $lieu->setCity('Rennes');
        $lieu->setFileName('file.jpg');

        $em->persist($lieu);
        $em->flush();

        //create a ActivityState fixture
        $activityState = new ActivityState();
        $activityState->setCode('ACT_CR');
        $activityState->setWording('créée');

        $activityState2 = new ActivityState();
        $activityState2->setCode('ACT_ANN');
        $activityState2->setWording('annulée');

        $em->persist($activityState);
        $em->persist($activityState2);
        $em->flush();

        // Create a User fixture
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get('security.user_password_hasher');
        $user = (new User())->setEmail('email@example.com');
        $user->setPhoneNumber('1234567890');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));
        $user->setPseudo('pseudo');
        $user->setActive(true);
        $user->setLastName('Doe');
        $user->setFirstName('John');
        $user->setCampus($campus);
        $em->persist($user);
        $em->flush();

        //Create a Activity fixture
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
        $activity1->addInscrit($user);

        $em->persist($activity1);
        $em->flush();
    }

    public function testRemoveOrganizer(){
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $activityRepository = $em->getRepository(Activity::class);
        $activityService = $container->get('App\Service\ActivityService');

        $activity = $activityRepository->findOneBy(['name' => 'Activity 1']);
        $activityService->removeOrganizer($activity);

        $this->assertNull($activity->getOrganizer());
        $this->assertEquals('ACT_ANN', $activity->getState()->getCode());
    }
}
