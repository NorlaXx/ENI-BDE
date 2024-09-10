<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\PasswordHasher\EventListener\PasswordHasherListener;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CsvImportService extends AbstractController
{


    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface      $entityManager)
    {
    }

    public function importCsv(string $filePath): array
    {
        $results = ['success' => 0, 'errors' => []];

        try {

            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception('CSV file not found or not readable.');
            }

            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                throw new \Exception('Could not open the CSV file.');
            }

            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {
                try {
                    $this->processRow($data);
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Row " . ($results['success'] + count($results['errors']) + 1) . ": " . $e->getMessage();
                }
            }

            fclose($handle);
            $this->entityManager->flush();
            } catch (\Exception $e) {
                $results['errors'][] = "File error: " . $e->getMessage();
            }
            return $results;
        }

    private function processRow(array $data): void
    {
        [$email, $phoneNumber, $pseudo, $campusName, $lastName, $firstName] = $data;

        $user = new User();
        if($this->entityManager->getRepository(User::class)->findOneBy(['email' => $email])){
            $this->addFlash('error', 'Tous les emails doivent être uniques !');
            $this->redirectToRoute("app_profil_liste");
        } else {
            $user->setEmail($email);
        }


        $user->setRoles(["ROLE_USER"]);
//        Set "password" to default password for new User
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            "password"
        );
        $user->setPassword($hashedPassword);
        $user->setPhoneNumber($phoneNumber);
        $user->setPseudo($pseudo);

        $campus = $this->entityManager->getRepository(Campus::class)->findOneBy(['name' => $campusName]);

//        Si le nom du campus n'est pas connue, valuer set a Rennes par defaut
        if (!$campus) {
             $campus = $this->entityManager->getRepository(Campus::class)->findOneBy(['name' => "Rennes"]);
        }
        $user->setCampus($campus);

        $user->setFileName("default.jpg");
        $user->setActive(true);
        $user->setLastName($lastName);
        $user->setFirstName($firstName);

        $this->entityManager->persist($user);
    }

}