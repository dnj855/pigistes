<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210123173329 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE montants (id INT AUTO_INCREMENT NOT NULL, tarifs_id INT NOT NULL, montant_variable DOUBLE PRECISION NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_2E129412F5F3287F (tarifs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE montants ADD CONSTRAINT FK_2E129412F5F3287F FOREIGN KEY (tarifs_id) REFERENCES tarifs (id)');
        $this->addSql('ALTER TABLE tarifs ADD montant_invariable DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE montants');
        $this->addSql('ALTER TABLE tarifs DROP montant_invariable');
    }
}
