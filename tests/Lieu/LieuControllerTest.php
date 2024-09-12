<?php

namespace App\Tests\Lieu;

use App\Entity\Activity;
use App\Entity\ActivityState;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LieuControllerTest extends WebTestCase
{
    private KernelBrowser $client;

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
    }

    public function testCreateLieu(): void
    {
        $this->client->request('GET', '/login');
        //Connexion d'un utilisateur
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        $file = new UploadedFile(
            'public/images/bdEni.png',
            'bdEni.png',
            'image/png',
        );

        $this->client->request('GET', '/lieu/create');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm('Enregistrer', [
            'lieu[name]' => 'picine de Bréquiny',
            'lieu[city]' => 'Rennes',
            'lieu[postalCode]' => '35000',
            'lieu[address]' => '12 Bd Albert 1er',
            'lieu[fileName]' => $file,
        ]);

        $this->assertResponseRedirects('/lieu/liste');

    }
}
