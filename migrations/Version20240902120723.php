<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902120723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, campus_id INT NOT NULL, lieu_id INT DEFAULT NULL, state_id INT NOT NULL, organisateur_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_timeate_debut DATE NOT NULL, date_final_inscription DATE NOT NULL, duree INT NOT NULL, picture_file_name VARCHAR(255) DEFAULT NULL, date_creation DATE NOT NULL, INDEX IDX_AC74095AAF5D55E1 (campus_id), INDEX IDX_AC74095A6AB213CC (lieu_id), INDEX IDX_AC74095A5D83CC1 (state_id), INDEX IDX_AC74095AD936B2FA (organisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_user (activity_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8E570DDB81C06096 (activity_id), INDEX IDX_8E570DDBA76ED395 (user_id), PRIMARY KEY(activity_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_state (id INT AUTO_INCREMENT NOT NULL, state INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campus (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lat VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, nombre_place_max INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieu (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lat VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, cp VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AAF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A6AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A5D83CC1 FOREIGN KEY (state_id) REFERENCES activity_state (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AD936B2FA FOREIGN KEY (organisateur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE activity_user ADD CONSTRAINT FK_8E570DDB81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_user ADD CONSTRAINT FK_8E570DDBA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD campus_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649AF5D55E1 ON user (campus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649AF5D55E1');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AAF5D55E1');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A6AB213CC');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A5D83CC1');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AD936B2FA');
        $this->addSql('ALTER TABLE activity_user DROP FOREIGN KEY FK_8E570DDB81C06096');
        $this->addSql('ALTER TABLE activity_user DROP FOREIGN KEY FK_8E570DDBA76ED395');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_user');
        $this->addSql('DROP TABLE activity_state');
        $this->addSql('DROP TABLE campus');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP INDEX IDX_8D93D649AF5D55E1 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP campus_id');
    }
}
