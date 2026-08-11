<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260811000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Agrega descuento informativo a los envios internacionales';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE envio ADD descuento NUMERIC(20, 2) DEFAULT '0.00' NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE envio DROP descuento');
    }
}
