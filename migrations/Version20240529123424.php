<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240529123424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal ADD rapport_veterinaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F82A908C2 FOREIGN KEY (rapport_veterinaire_id) REFERENCES rapport_veterinaire (id)');
        $this->addSql('CREATE INDEX IDX_6AAB231F82A908C2 ON animal (rapport_veterinaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F82A908C2');
        $this->addSql('DROP INDEX IDX_6AAB231F82A908C2 ON animal');
        $this->addSql('ALTER TABLE animal DROP rapport_veterinaire_id');
    }
}
