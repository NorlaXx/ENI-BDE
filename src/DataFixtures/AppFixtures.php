<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\ActivityState;
use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Array of campuses
        $campuses = [
            [
                'id' => 1,
                'name' => 'Eni - Rennes',
                'latitude' => 48.03914488370492,
                'longitude' => -1.6920911818491027,
                'nbLimitPlaces' => 20,
                'address' => '8 Rue Léo Lagrange',
                'postalCode' => 35000,
                'city' => 'Chartres de Bretagne'
            ],
            [
                'id' => 2,
                'name' => 'Eni - Nantes',
                'latitude' => 47.55166565445708,
                'longitude' => -1.4327962718513467,
                'nbLimitPlaces' => 18,
                'address' => '3 rue Michael Faraday',
                'postalCode' => 44000,
                'city' => 'Nantes'
            ],
            [
                'id' => 3,
                'name' => 'Eni - Quimper',
                'latitude' => 47.97754750308657,
                'longitude' => -4.083039206994787,
                'nbLimitPlaces' => 15,
                'address' => '2 Rue Georges Perros',
                'postalCode' => 29000,
                'city' => 'Quimper'
            ]
        ];

        // Array of lieux
        $lieux = [
            [
                'id' => 1,
                'name' => 'Piscine de bréquigny',
                'latitude' => 48.0890177959865,
                'longitude' => -1.690158649607449,
                'postalCode' => 35000,
                'city' => 'Rennes',
                'address' => '10 rue de la piscine'
            ],
            [
                'id' => 2,
                'name' => 'Parc de la Gaudinière',
                'latitude' => 47.24650184636519,
                'longitude' => -1.5845028822839156,
                'postalCode' => 44100,
                'city' => 'Nantes',
                'address' => '10 rue du parc'
            ],
            [
                'id' => 3,
                'name' => 'Stade de Penvillers',
                'latitude' => 47.99707956026434,
                'longitude' => -4.099162626373785,
                'postalCode' => 29000,
                'city' => 'Quimper',
                'address' => '10 rue du stade'
            ],
            [
                'id' => 4,
                'name' => 'Stade de la route de Lorient',
                'latitude' => 48.085013,
                'longitude' => -1.698211,
                'postalCode' => 35000,
                'city' => 'Rennes',
                'address' => '111 route de Lorient'
            ],
            [
                'id' => 5,
                'name' => 'Stade de la Beaujoire',
                'latitude' => 47.248611,
                'longitude' => -1.526389,
                'postalCode' => 44000,
                'city' => 'Nantes',
                'address' => '5 boulevard de la Beaujoire'
            ],
            [
                'id' => 6,
                'name' => 'Stade de la Rabine',
                'latitude' => 47.660833,
                'longitude' => -2.760556,
                'postalCode' => 56000,
                'city' => 'Vannes',
                'address' => '20 rue de la Rabine'
            ],
            [
                'id' => 7,
                'name' => 'Tour Eiffel',
                'latitude' => 48.858370,
                'longitude' => 2.294481,
                'postalCode' => 75007,
                'city' => 'Paris',
                'address' => '5 avenue Anatole France'
            ],
            [
                'id' => 8,
                'name' => 'Arc de Triomphe',
                'latitude' => 48.873792,
                'longitude' => 2.295028,
                'postalCode' => 75017,
                'city' => 'Paris',
                'address' => 'Place Charles de Gaulle'
            ],
            [
                'id' => 9,
                'name' => 'Château de Versailles',
                'latitude' => 48.804864,
                'longitude' => 2.120355,
                'postalCode' => 78000,
                'city' => 'Versailles',
                'address' => 'Place d\'Armes'
            ],
            [
                'id' => 10,
                'name' => 'Mont Saint-Michel',
                'latitude' => 48.636111,
                'longitude' => -1.511389,
                'postalCode' => 50170,
                'city' => 'Le Mont-Saint-Michel',
                'address' => '50170 Le Mont-Saint-Michel'
            ],
            [
                'id' => 11,
                'name' => 'Cathédrale Notre-Dame de Paris',
                'latitude' => 48.852968,
                'longitude' => 2.349902,
                'postalCode' => 75004,
                'city' => 'Paris',
                'address' => '6 Parvis Notre-Dame - Pl. Jean-Paul II'
            ],
            [
                'id' => 12,
                'name' => 'Basilique du Sacré-Cœur de Montmartre',
                'latitude' => 48.886667,
                'longitude' => 2.343056,
                'postalCode' => 75018,
                'city' => 'Paris',
                'address' => '35 Rue du Chevalier de la Barre'
            ],
            [
                'id' => 13,
                'name' => 'Château de Chambord',
                'latitude' => 47.616389,
                'longitude' => 1.515833,
                'postalCode' => 41250,
                'city' => 'Chambord',
                'address' => '41250 Chambord'
            ],
            [
                'id' => 14,
                'name' => 'Château de Chantilly',
                'latitude' => 49.194167,
                'longitude' => 2.481944,
                'postalCode' => 60500,
                'city' => 'Chantilly',
                'address' => '60500 Chantilly'
            ],
        ];

        // Création des campus
        foreach ($campuses as $campusData) {
            $campusVar = "campus" . $campusData['id'];
            $$campusVar = new Campus();
            $$campusVar->setName($campusData['name']);
            $$campusVar->setLatitude($campusData['latitude']);
            $$campusVar->setLongitude($campusData['longitude']);
            $$campusVar->setNblimitPlaces($campusData['nbLimitPlaces']);
            $$campusVar->setFileName('campus_ident.jpg');
            $$campusVar->setAddress($campusData['address']);
            $$campusVar->setPostalCode($campusData['postalCode']);
            $$campusVar->setCity($campusData['city']);
            $manager->persist($$campusVar);
        }

        //Création des lieux
        foreach ($lieux as $lieuData) {
            $lieuVar = "lieu" . $lieuData['id'];
            $$lieuVar = new Lieu();
            $$lieuVar->setName($lieuData['name']);
            $$lieuVar->setLatitude($lieuData['latitude']);
            $$lieuVar->setLongitude($lieuData['longitude']);
            $$lieuVar->setPostalCode($lieuData['postalCode']);
            $$lieuVar->setCity($lieuData['city']);
            $$lieuVar->setFileName('campus_ident.jpg');
            $$lieuVar->setAddress($lieuData['address']);
            $manager->persist($$lieuVar);
        }

        $allState = [
            'ACT_CR' => 'crées',
            'ACT_INS' => 'inscription',
            'ACT_INS_F' => 'inscription fermé',
            'ACT_EN_C' => 'en cours',
            'ACT_TER' => 'terminée',
            'ACT_ARC' => 'archivée',
            'ACT_ANN' => 'annulée'
        ];

        //Création des états des sorties
        foreach ($allState as $key => $value) {
            $activityState = new ActivityState();
            $activityState->setWording($value);
            $activityState->setCode($key);
            $manager->persist($activityState);
        }


        // Création des utilisateurs
        $user1 = new User();
        $user1->setPseudo('user1');
        $user1->setLastName('Marie');
        $user1->setFirstName('Jean');
        $user1->setActive(true);
        $user1->setPhoneNumber('0123456789');
        $user1->setEmail('email@email.com');
        $user1->setPassword('$2y$10$MkOCJMhXB7tecvn52C1BG.El9oVvZ4CuwCTlaaNnVSvZxJEQ0wdMC');
        $user1->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user1->setCampus($campus1);
        $user1->setFileName('default.jpg');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setPseudo('user2');
        $user2->setFirstName('Loic');
        $user2->setLastName('Dupont');
        $user2->setActive(true);
        $user2->setPhoneNumber('0123456789');
        $user2->setEmail('email2@email.com');
        $user2->setPassword('$2y$10$8ArdLO2Y0Li.HUR5R9fOWO4UMiihLOCdf1bARdT/yc/h1GgCbW9eK');
        $user2->setRoles(['ROLE_USER']);
        $user2->setCampus($campus2);
        $user2->setFileName('default.jpg');
        $manager->persist($user2);

        $user3 = new User();
        $user3->setPseudo('user3');
        $user3->setFirstName('Pierre');
        $user3->setLastName('Durand');
        $user3->setActive(true);
        $user3->setPhoneNumber('0123456789');
        $user3->setEmail('email3@email.com');
        $user3->setPassword('$2y$10$Dc.xI0OeyS5fGJEhV5TwUOASYMQQiR7wzA7OmPA3M12ckLjoJ8y4i');
        $user3->setRoles(['ROLE_USER']);
        $user3->setCampus($campus3);
        $user3->setFileName('default.jpg');
        $manager->persist($user3);


        // Array of activities
        $activities = [
            [
                'name' => 'Swimming Competition',
                'description' => 'A competitive swimming event.',
                'startDate' => new \DateTime('2024-10-01 10:00:00'),
                'registrationDateLimit' => new \DateTime('2024-10-01 08:00:00'),
                'duration' => 120,
                'nbLimitParticipants' => 50,
                'campus' => $campus1,
                'lieu' => $lieu1,
                'state' => $activityState,
                'organizer' => $user1
            ],
            [
                'name' => 'Park Cleanup',
                'description' => 'A community park cleanup event.',
                'startDate' => new \DateTime('2024-10-01 10:00:00'),
                'registrationDateLimit' => new \DateTime('2024-10-01 08:00:00'),
                'duration' => 180,
                'nbLimitParticipants' => 30,
                'campus' => $campus2,
                'lieu' => $lieu2,
                'state' => $activityState,
                'organizer' => $user2
            ],
            [
                'name' => 'Football Match',
                'description' => 'A friendly football match.',
                'startDate' => new \DateTime('2024-10-01 10:00:00'),
                'registrationDateLimit' => new \DateTime('2024-10-01 08:00:00'),
                'duration' => 90,
                'nbLimitParticipants' => 22,
                'campus' => $campus3,
                'lieu' => $lieu3,
                'state' => $activityState,
                'organizer' => $user3
            ]
        ];

        // Création des sorties
        foreach ($activities as $activityData) {
            $activity = new Activity();
            $activity->setName($activityData['name']);
            $activity->setDescription($activityData['description']);
            $activity->setStartDate($activityData['startDate']);
            $activity->setRegistrationDateLimit($activityData['registrationDateLimit']);
            $activity->setCreationDate(new \DateTime());
            $activity->setDuration($activityData['duration']);
            $activity->setNbLimitParticipants($activityData['nbLimitParticipants']);
            $activity->setCampus($activityData['campus']);
            $activity->setLieu($activityData['lieu']);
            $activity->setState($activityData['state']);
            $activity->setOrganizer($activityData['organizer']);
            $activity->setFileName('activity_ident.jpg');
            $manager->persist($activity);
        }

        $manager->flush();
    }
}
