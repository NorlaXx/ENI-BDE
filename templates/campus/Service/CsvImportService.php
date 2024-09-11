<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\PasswordHasher\EventListener\PasswordHasherListener;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CsvImportService
{


    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface      $entityManager
    )
    {}

    public function importCsv(string $filePath): array
    {
        $results = ['success' => 0, 'errors' => []];

        try {

            // Debug the absolute path
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception('CSV file not found or not readable.');
            }

            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                throw new \Exception('Could not open the CSV file.');
            }

            // Skip the header row
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
        [$email, $phoneNumber, $pseudo, $campusName, $pictureFileName, $isActive, $nom, $prenom] = $data;

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(["ROLE_USER"]);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            "password"
        );
        $user->setPassword($hashedPassword);
        $user->setPhoneNumber($phoneNumber);
        $user->setPseudo($pseudo);

        $campus = $this->entityManager->getRepository(Campus::class)->findOneBy(['name' => $campusName]);
        if (!$campus) {
            throw new \Exception("Campus not found: $campusName");
        }
        $user->setCampus($campus);

        $user->setFileName("default.jpg");
        $user->setActive(true);
        $user->setLastName($nom);
        $user->setFirstName($prenom);

        $this->entityManager->persist($user);
    }

}