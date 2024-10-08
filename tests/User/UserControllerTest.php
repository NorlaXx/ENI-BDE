<?php

namespace App\Tests\User;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);
        $campusRepository = $em->getRepository(Campus::class);
        $activityRepository = $em->getRepository(Activity::class);

        foreach ($activityRepository->findAll() as $activity) {
            $em->remove($activity);
        }

        // Remove any existing users from the test database
        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        //Remove all campus
        foreach ($campusRepository->findAll() as $campus) {
            $em->remove($campus);
        }

        $em->flush();

        // Create a User fixture
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get('security.user_password_hasher');

        $campus = (new Campus())->setName('Campus');
        $campus->setCity('City');
        $campus->setAddress('Address');
        $campus->setNblimitPlaces(10);
        $campus->setLatitude(1.1);
        $campus->setLongitude(1.1);
        $campus->setFileName('fileName.jpg');
        $campus->setPostalCode('12345');

        $em->persist($campus);
        $em->flush();

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

    public function testShowProfile(){
        $this->client->request('GET', '/login');
        //Connexion d'un utilisateur
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        $this->client->request('GET', '/profil');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateUser()
    {
        $this->client->request('GET', '/login');
        //Connexion d'un utilisateur
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        $this->client->request('GET', '/profil/list');
        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/profil/create');
        $this->assertResponseIsSuccessful();

        $campus = $this->client->getContainer()->get('doctrine')->getRepository(Campus::class)->findOneBy(['name' => 'Campus']);
        $file = new UploadedFile(
            'public/images/bdEni.png',
            'bdEni.png',
            'image/png',
        );

        $this->client->submitForm('Enregistrer', [
            'user[phone_number]' => '1234567890',
            'user[pseudo]' => 'pseudo',
            'user[lastName]' => 'Doe',
            'user[firstName]' => 'John',
            'user[campus]' => $campus->getId(),
            'user[email]' => 'emailtest@email.com',
            'user[password]' => 'password',
            'user[passwordConfirm]' => 'password',
            'user[profilePicture]' => $file,
        ]);

        $this->assertResponseRedirects('/profil/list');
    }

    public function testUpdateUser(){
        $this->client->request('GET', '/login');
        //Connexion d'un utilisateur
        $this->client->submitForm('Se connecter', [
            '_username' => 'email@example.com',
            '_password' => 'password',
        ]);

        $this->client->request('GET', '/profileEdit');
        $this->assertResponseIsSuccessful();

        $campus = $this->client->getContainer()->get('doctrine')->getRepository(Campus::class)->findOneBy(['name' => 'Campus']);
        $file = new UploadedFile(
            'public/images/bdEni.png',
            'bdEni.png',
            'image/png',
        );

        $this->client->submitForm('Enregistrer', [
            'user_update[phone_number]' => '1234567890',
            'user_update[pseudo]' => 'pseudoTropCool',
            'user_update[lastName]' => 'Doe',
            'user_update[firstName]' => 'John',
            'user_update[campus]' => $campus->getId(),
            'user_update[email]' => 'email@example.com',
            'user_update[password]' => 'password',
            'user_update[passwordConfirm]' => 'password',
            'user_update[profilePicture]' => $file,
        ]);

        $this->assertResponseRedirects('/profil');
    }
}
