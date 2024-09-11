<?php

namespace App\Tests\Activity;

use App\Entity\Activity;
use App\Entity\ActivityState;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ActivityControllerTest extends WebTestCase
{
    private kernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);
        $campusRepository = $em->getRepository(Campus::class);
        $lieuRepository = $em->getRepository(Lieu::class);
        $activityRepository = $em->getRepository(Activity::class);
        $activityStateRepository = $em->getRepository(ActivityState::class);

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
        foreach ($activityStateRepository->findAll() as $activityState) {
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

        $activityState3 = new ActivityState();
        $activityState3->setCode('ACT_INS');
        $activityState3->setWording('inscription');

        $em->persist($activityState);
        $em->persist($activityState2);
        $em->persist($activityState3);
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
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
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

    public function testGetActivities(): void
    {
        $this->client->request('GET', '/login');

        //Connexion d'un utilisateur
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        $this->client->request('GET', '/activity/list');
        $this->assertResponseIsSuccessful();
    }

    public function testPublyActivity(){
        $this->client->request('GET', '/login');

        //Connexion d'un utilisateur
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        $lieu = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Lieu::class)->findOneBy(['name' => 'picine de Bréquiny']);
        $campus = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Campus::class)->findOneBy(['name' => 'ENI - Rennes']);

        $this->client->request('GET', '/activity/create');
        $this->client->submitForm('Publier', [
            'activity[name]' => 'Activity 2',
            'activity[campus]' => $campus->getid(),
            'activity[lieu]' => $lieu->getid(),
            'activity[description]' => 'Description 2',
            'activity[startDate]' => '2024-10-01 14:00:00',
            'activity[registrationDateLimit]' => '2024-09-30 23:59:00',
            'activity[nbLimitParticipants]' => 10,
            'activity[duration]' => 40,
        ]);

        $this->assertResponseRedirects('/');
    }

    public function testRegisterActivity()
    {
        $this->client->request('GET', '/login');

        //Connexion d'un utilisateur
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        $lieu = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Lieu::class)->findOneBy(['name' => 'picine de Bréquiny']);
        $campus = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Campus::class)->findOneBy(['name' => 'ENI - Rennes']);

        $this->client->request('GET', '/activity/create');
        $this->client->submitForm('Enregistrer', [
            'activity[name]' => 'Activity 2',
            'activity[campus]' => $campus->getid(),
            'activity[lieu]' => $lieu->getid(),
            'activity[description]' => 'Description 2',
            'activity[startDate]' => '2024-10-01 14:00:00',
            'activity[registrationDateLimit]' => '2024-09-30 23:59:00',
            'activity[nbLimitParticipants]' => 10,
            'activity[duration]' => 40,
        ]);

        $this->assertResponseRedirects('/');
    }

    public function testUpdateActivity(){
        $this->client->request('GET', '/login');

        //Connexion d'un utilisateur
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        $activity = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Activity::class)->findOneBy(['name' => 'Activity 1']);

        $this->client->request('GET', '/activity/update/'.$activity->getId());
        $this->client->submitForm('Publier', [
            'activity[name]' => 'Activité modifiée',
            'activity[campus]' => $activity->getCampus()->getid(),
            'activity[lieu]' => $activity->getLieu()->getid(),
            'activity[description]' => 'une description de fou',
            'activity[startDate]' => '2024-10-01 14:00:00',
            'activity[registrationDateLimit]' => '2024-09-30 23:59:00',
            'activity[nbLimitParticipants]' => 10,
            'activity[duration]' => 50,
        ]);

        $this->assertResponseRedirects('/');
    }
}
