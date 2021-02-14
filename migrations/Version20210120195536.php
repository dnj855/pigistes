<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210120195536 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrats ADD pigiste_id INT NOT NULL, ADD tarif_id INT NOT NULL');
        $this->addSql('ALTER TABLE contrats ADD CONSTRAINT FK_7268396C351F58F6 FOREIGN KEY (pigiste_id) REFERENCES pigistes (id)');
        $this->addSql('ALTER TABLE contrats ADD CONSTRAINT FK_7268396C357C0A59 FOREIGN KEY (tarif_id) REFERENCES tarifs (id)');
        $this->addSql('CREATE INDEX IDX_7268396C351F58F6 ON contrats (pigiste_id)');
        $this->addSql('CREATE INDEX IDX_7268396C357C0A59 ON contrats (tarif_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrats DROP FOREIGN KEY FK_7268396C351F58F6');
        $this->addSql('ALTER TABLE contrats DROP FOREIGN KEY FK_7268396C357C0A59');
        $this->addSql('DROP INDEX IDX_7268396C351F58F6 ON contrats');
        $this->addSql('DROP INDEX IDX_7268396C357C0A59 ON contrats');
        $this->addSql('ALTER TABLE contrats DROP pigiste_id, DROP tarif_id');
    }
}
