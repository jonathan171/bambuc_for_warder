<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241114012145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        $this->addSql('ALTER TABLE recibo_caja ADD sub_total NUMERIC(20, 2) NOT NULL, ADD total NUMERIC(20, 2) NOT NULL');
        
    }

    public function down(Schema $schema): void
    {
       
    }
}
