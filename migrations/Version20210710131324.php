<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210710131324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_delivery_address DROP FOREIGN KEY FK_D694507D558FBEB9');
        $this->addSql('DROP INDEX UNIQ_D694507D558FBEB9 ON purchase_delivery_address');
        $this->addSql('ALTER TABLE purchase_delivery_address DROP purchase_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_delivery_address ADD purchase_id INT NOT NULL');
        $this->addSql('ALTER TABLE purchase_delivery_address ADD CONSTRAINT FK_D694507D558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D694507D558FBEB9 ON purchase_delivery_address (purchase_id)');
    }
}
