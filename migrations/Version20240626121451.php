<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240626121451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal ADD nourriture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F98BD5834 FOREIGN KEY (nourriture_id) REFERENCES nourriture (id)');
        $this->addSql('CREATE INDEX IDX_6AAB231F98BD5834 ON animal (nourriture_id)');
        $this->addSql('ALTER TABLE nourriture DROP FOREIGN KEY FK_7447E6133256915B');
        $this->addSql('DROP INDEX IDX_7447E6133256915B ON nourriture');
        $this->addSql('ALTER TABLE nourriture DROP relation_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F98BD5834');
        $this->addSql('DROP INDEX IDX_6AAB231F98BD5834 ON animal');
        $this->addSql('ALTER TABLE animal DROP nourriture_id');
        $this->addSql('ALTER TABLE nourriture ADD relation_id INT NOT NULL');
        $this->addSql('ALTER TABLE nourriture ADD CONSTRAINT FK_7447E6133256915B FOREIGN KEY (relation_id) REFERENCES animal (id)');
        $this->addSql('CREATE INDEX IDX_7447E6133256915B ON nourriture (relation_id)');
    }
}
