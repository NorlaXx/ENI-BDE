<?php

namespace App\Service;

use App\Entity\Campus;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\PasswordHasher\EventListener\PasswordHasherListener;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CsvImportService extends AbstractController
{


    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface      $entityManager,
        private UserRepository              $userRepository
    )
    {
    }

    public function importCsv(string $filePath): void
    {
        try {

            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception('le fichier n.\'est pas trouvé ou n.\'est pas lisible');
            }

            $handle = fopen($filePath, 'r');
            if ($handle === false) {
                throw new \Exception('Le fichier ne peut pas être ouvert');
            }

            fgetcsv($handle);
        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->redirectToRoute("app_profil_list");
        }
        while (($data = fgetcsv($handle )) !== false) {
            if($data[0] != "") {
                $this->processRow($data);
            }
        }

        fclose($handle);

    }

    private
    function processRow(array $data): void
    {
        $email = $data[0] ?? '';
        $phoneNumber = $data[1] ?? '';
        $pseudo = $data[2] ?? '';
        $campusName = $data[3] ?? '';
        $lastName = $data[4] ?? '';
        $firstName = $data[5] ?? '';

        $user = new User();
        if($this->entityManager->getRepository(User::class)->findOneBy(['email' => $email])){
            $this->addFlash('error', 'Tous les emails doivent être uniques!');
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