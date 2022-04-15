<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220414023430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE envio CHANGE total_peso_estimado total_peso_estimado NUMERIC(20, 2) DEFAULT NULL, CHANGE total_peso_real total_peso_real NUMERIC(20, 2) DEFAULT NULL, CHANGE total_dimencional total_dimencional NUMERIC(20, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE envio CHANGE total_peso_estimado total_peso_estimado NUMERIC(5, 2) DEFAULT NULL, CHANGE total_peso_real total_peso_real NUMERIC(5, 2) DEFAULT NULL, CHANGE total_dimencional total_dimencional NUMERIC(5, 2) DEFAULT NULL');
    }
}
