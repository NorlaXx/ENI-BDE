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
                'name' => 'Rennes',
                'lat' => 48.03914488370492,
                'longitude' => -1.6920911818491027,
                'nombrePlaceMax' => 20
            ],
            [
                'id' => 2,
                'name' => 'Nantes',
                'lat' => 47.55166565445708,
                'longitude' => -1.4327962718513467,
                'nombrePlaceMax' => 18
            ],
            [
                'id' => 3,
                'name' => 'Quimper',
                'lat' => 47.97754750308657,
                'longitude' => -4.083039206994787,
                'nombrePlaceMax' => 15
            ]
        ];

        // Array of lieux
        $lieux = [
            [
                'id' => 1,
                'name' => 'piscine de bréquigny',
                'lat' => 48.0890177959865,
                'longitude' => -1.690158649607449,
                'cp' => 35000,
                'ville' => 'Rennes',
                'adresse' => '10 rue de la piscine'
            ],
            [
                'id' => 2,
                'name' => 'Parc de la Gaudinière',
                'lat' => 47.24650184636519,
                'longitude' => -1.5845028822839156,
                'cp' => 44100,
                'ville' => 'Nantes',
                'adresse' => '10 rue du parc'
            ],
            [
                'id' => 3,
                'name' => 'Stade de Penvillers',
                'lat' => 47.99707956026434,
                'longitude' => -4.099162626373785,
                'cp' => 29000,
                'ville' => 'Quimper',
                'adresse' => '10 rue du stade'
            ]
        ];
        // Création des campus
        foreach ($campuses as $campusData) {
            $campusVar = "campus" . $campusData['id'];
            $$campusVar = new Campus();
            $$campusVar->setName($campusData['name']);
            $$campusVar->setLat($campusData['lat']);
            $$campusVar->setLongitude($campusData['longitude']);
            $$campusVar->setNombrePlaceMax($campusData['nombrePlaceMax']);
            $$campusVar->setPictureFileName('campus_ident.jpg');
            $manager->persist($$campusVar);
        }

        //Création des lieux
        foreach ($lieux as $lieuData) {
            $lieuVar = "lieu" . $lieuData['id'];
            $$lieuVar = new Lieu();
            $$lieuVar->setName($lieuData['name']);
            $$lieuVar->setLat($lieuData['lat']);
            $$lieuVar->setLongitude($lieuData['longitude']);
            $$lieuVar->setCp($lieuData['cp']);
            $$lieuVar->setVille($lieuData['ville']);
            $$lieuVar->setFileName('campus_ident.jpg');
            $$lieuVar->setAddresse($lieuData['adresse']);
            $manager->persist($$lieuVar);
        }

        //Création des états des sorties
        $activityState = new ActivityState();
        $activityState->setLibelle('crées');
        $activityState->setCode('ACT_CR');
        $manager->persist($activityState);
        $activityState2 = new ActivityState();
        $activityState2->setLibelle('inscription');
        $activityState2->setCode('ACT_INS');
        $manager->persist($activityState2);
        $activityState3 = new ActivityState();
        $activityState3->setLibelle('inscription fermé');
        $activityState3->setCode('ACT_INS_F');
        $manager->persist($activityState3);
        $activityState4 = new ActivityState();
        $activityState4->setLibelle('en cours');
        $activityState4->setCode('ACT_EN_C');
        $manager->persist($activityState4);
        $activityState5 = new ActivityState();
        $activityState5->setLibelle('terminée');
        $activityState5->setCode('ACT_TER');
        $manager->persist($activityState5);
        $activityState6 = new ActivityState();
        $activityState6->setLibelle('archivée');
        $activityState6->setCode('ACT_ARC');
        $manager->persist($activityState6);
        $activityState7 = new ActivityState();
        $activityState7->setLibelle('annulée');
        $activityState7->setCode('ACT_ANN');
        $manager->persist($activityState7);


        // Création des utilisateurs
        $user1 = new User();
        $user1->setPseudo('user1');
        $user1->setPhoneNumber('0123456789');
        $user1->setEmail('email@email.com');
        $user1->setPassword('$2y$10$MkOCJMhXB7tecvn52C1BG.El9oVvZ4CuwCTlaaNnVSvZxJEQ0wdMC');
        $user1->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user1->setCampus($campus1); // Assuming $campus1 is an instance of Campus
        $user1->setPictureFileName('default.jpg');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setPseudo('user2');
        $user2->setPhoneNumber('0123456789');
        $user2->setEmail('email2@email.com');
        $user2->setPassword('$2y$10$8ArdLO2Y0Li.HUR5R9fOWO4UMiihLOCdf1bARdT/yc/h1GgCbW9eK');
        $user2->setRoles(['ROLE_USER']);
        $user2->setCampus($campus2); // Assuming $campus2 is an instance of Campus
        $user2->setPictureFileName('default.jpg');
        $manager->persist($user2);

        $user3 = new User();
        $user3->setPseudo('user3');
        $user3->setPhoneNumber('0123456789');
        $user3->setEmail('email3@email.com');
        $user3->setPassword('$2y$10$Dc.xI0OeyS5fGJEhV5TwUOASYMQQiR7wzA7OmPA3M12ckLjoJ8y4i');
        $user3->setRoles(['ROLE_USER']);
        $user3->setCampus($campus3); // Assuming $campus3 is an instance of Campus
        $user3->setPictureFileName('default.jpg');
        $manager->persist($user3);


        // Array of activities
        $activities = [
            [
                'name' => 'Swimming Competition',
                'description' => 'A competitive swimming event.',
                'dateDebut' => new \DateTime('2024-10-01 10:00:00'),
                'dateFinalInscription' => new \DateTime('2024-10-01 08:00:00'),
                'duree' => 120,
                'nbLimitParticipants' => 50,
                'campus' => $campus1, // Assuming $campus1 is an instance of Campus
                'lieu' => $lieu1, // Assuming $lieu1 is an instance of Lieu
                'state' => $activityState, // Assuming $activityState2 is an instance of ActivityState
                'organisateur' => $user1 // Assuming $user1 is an instance of User
            ],
            [
                'name' => 'Park Cleanup',
                'description' => 'A community park cleanup event.',
                'dateDebut' => new \DateTime('2024-10-01 10:00:00'),
                'dateFinalInscription' => new \DateTime('2024-10-01 08:00:00'),
                'duree' => 180,
                'nbLimitParticipants' => 30,
                'campus' => $campus2, // Assuming $campus2 is an instance of Campus
                'lieu' => $lieu2, // Assuming $lieu2 is an instance of Lieu
                'state' => $activityState, // Assuming $activityState2 is an instance of ActivityState
                'organisateur' => $user2 // Assuming $user2 is an instance of User
            ],
            [
                'name' => 'Football Match',
                'description' => 'A friendly football match.',
                'dateDebut' => new \DateTime('2024-10-01 10:00:00'),
                'dateFinalInscription' => new \DateTime('2024-10-01 08:00:00'),
                'duree' => 90,
                'nbLimitParticipants' => 22,
                'campus' => $campus3, // Assuming $campus3 is an instance of Campus
                'lieu' => $lieu3, // Assuming $lieu3 is an instance of Lieu
                'state' => $activityState, // Assuming $activityState2 is an instance of ActivityState
                'organisateur' => $user3 // Assuming $user3 is an instance of User
            ]
        ];

        // Création des sorties
        foreach ($activities as $activityData) {
            $activity = new Activity();
            $activity->setName($activityData['name']);
            $activity->setDescription($activityData['description']);
            $activity->setDateDebut($activityData['dateDebut']);
            $activity->setDateFinalInscription($activityData['dateFinalInscription']);
            $activity->setDateCreation(new \DateTime());
            $activity->setDuree($activityData['duree']);
            $activity->setNbLimitParticipants($activityData['nbLimitParticipants']);
            $activity->setCampus($activityData['campus']);
            $activity->setLieu($activityData['lieu']);
            $activity->setState($activityData['state']);
            $activity->setOrganisateur($activityData['organisateur']);
            $activity->setPictureFileName('activity_ident.jpg');
            $manager->persist($activity);
        }

        $manager->flush();
    }
}
