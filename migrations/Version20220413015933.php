<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413015933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE envio (id INT AUTO_INCREMENT NOT NULL, codigo VARCHAR(255) NOT NULL, estado INT NOT NULL, numero_orden VARCHAR(255) NOT NULL, descripcion LONGTEXT NOT NULL, total_peso_estimado NUMERIC(5, 2) DEFAULT NULL, total_peso_real NUMERIC(5, 2) DEFAULT NULL, total_dimencional NUMERIC(5, 2) DEFAULT NULL, fecha_estimada_entrega DATE NOT NULL, piezas LONGTEXT NOT NULL, cantidad_piezas INT DEFAULT NULL, json_recibido LONGTEXT DEFAULT NULL, facturado TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE envio');
    }
}
