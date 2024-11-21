<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121021148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clientes CHANGE municipio_id municipio_id BIGINT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_50FE07D75E5F5AF3 ON clientes (nit)');
        $this->addSql('ALTER TABLE empresa CHANGE documento documento VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE envio DROP FOREIGN KEY envio_ibfk_4');
        $this->addSql('ALTER TABLE envio ADD municipio_id BIGINT DEFAULT NULL, CHANGE pais_origen pais_origen INT DEFAULT NULL, CHANGE pais_destino pais_destino INT DEFAULT NULL, CHANGE verificado verificado TINYINT(1) NOT NULL, CHANGE referencia referencia LONGTEXT DEFAULT NULL, CHANGE facturado_transportadora facturado_transportadora TINYINT(1) NOT NULL, CHANGE factura_transportadora factura_transportadora VARCHAR(255) NOT NULL, CHANGE facturado_recibo facturado_recibo TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE envio ADD CONSTRAINT FK_754737D558BC1BE0 FOREIGN KEY (municipio_id) REFERENCES municipio (id)');
        $this->addSql('CREATE INDEX IDX_754737D558BC1BE0 ON envio (municipio_id)');
        $this->addSql('DROP INDEX recibo_caja_item_id ON envio');
        $this->addSql('CREATE INDEX IDX_754737D54F43FF31 ON envio (recibo_caja_item_id)');
        $this->addSql('ALTER TABLE envio ADD CONSTRAINT envio_ibfk_4 FOREIGN KEY (recibo_caja_item_id) REFERENCES recibo_caja_item (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE envios_nacionales DROP FOREIGN KEY envios_nacionales_ibfk_4');
        $this->addSql('ALTER TABLE envios_nacionales DROP FOREIGN KEY envios_nacionales_ibfk_6');
        $this->addSql('ALTER TABLE envios_nacionales DROP FOREIGN KEY envios_nacionales_ibfk_5');
        $this->addSql('ALTER TABLE envios_nacionales CHANGE cliente_id cliente_id INT DEFAULT NULL, CHANGE municipio_origen municipio_origen BIGINT DEFAULT NULL, CHANGE municipio_destino municipio_destino BIGINT DEFAULT NULL, CHANGE peso peso NUMERIC(20, 2) NOT NULL, CHANGE valor_total valor_total NUMERIC(20, 2) NOT NULL, CHANGE contra_entrega contra_entrega TINYINT(1) NOT NULL, CHANGE facturado facturado TINYINT(1) DEFAULT NULL, CHANGE facturado_recibo facturado_recibo TINYINT(1) DEFAULT NULL');
        $this->addSql('DROP INDEX factura_items_id ON envios_nacionales');
        $this->addSql('CREATE INDEX IDX_C8565C0670A4000F ON envios_nacionales (factura_items_id)');
        $this->addSql('DROP INDEX creador_id ON envios_nacionales');
        $this->addSql('CREATE INDEX IDX_C8565C0662F40C3D ON envios_nacionales (creador_id)');
        $this->addSql('DROP INDEX recibo_items_id ON envios_nacionales');
        $this->addSql('CREATE INDEX IDX_C8565C06FC98E6D9 ON envios_nacionales (recibo_items_id)');
        $this->addSql('ALTER TABLE envios_nacionales ADD CONSTRAINT envios_nacionales_ibfk_4 FOREIGN KEY (factura_items_id) REFERENCES factura_items (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE envios_nacionales ADD CONSTRAINT envios_nacionales_ibfk_6 FOREIGN KEY (recibo_items_id) REFERENCES recibo_caja_item (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE envios_nacionales ADD CONSTRAINT envios_nacionales_ibfk_5 FOREIGN KEY (creador_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE envios_nacionales_unidades DROP FOREIGN KEY envios_nacionales_unidades_ibfk_1');
        $this->addSql('ALTER TABLE envios_nacionales_unidades CHANGE envio_nacional_id envio_nacional_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE envios_nacionales_unidades ADD CONSTRAINT FK_E67D2EF3A34909F1 FOREIGN KEY (envio_nacional_id) REFERENCES envios_nacionales (id)');
        $this->addSql('ALTER TABLE factura CHANGE cond_de_pago cond_de_pago INT DEFAULT NULL, CHANGE factura_resolucion_id factura_resolucion_id INT DEFAULT NULL, CHANGE estado estado VARCHAR(20) NOT NULL, CHANGE total total NUMERIC(20, 2) DEFAULT \'0.00\' NOT NULL, CHANGE soporte_pago soporte_pago LONGTEXT DEFAULT NULL, CHANGE facturado facturado TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE factura_items DROP FOREIGN KEY factura_items_ibfk_2');
        $this->addSql('ALTER TABLE factura_items CHANGE factura_clientes_id factura_clientes_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE factura_items ADD CONSTRAINT FK_876545E93CFD1E69 FOREIGN KEY (factura_clientes_id) REFERENCES factura (id)');
        $this->addSql('ALTER TABLE factura_resolucion DROP FOREIGN KEY factura_resolucion_ibfk_1');
        $this->addSql('ALTER TABLE factura_resolucion CHANGE empresa_id empresa_id INT DEFAULT NULL, CHANGE prefijo prefijo VARCHAR(125) DEFAULT NULL');
        $this->addSql('DROP INDEX empresa_id ON factura_resolucion');
        $this->addSql('CREATE INDEX IDX_D904B4D7521E1991 ON factura_resolucion (empresa_id)');
        $this->addSql('ALTER TABLE factura_resolucion ADD CONSTRAINT factura_resolucion_ibfk_1 FOREIGN KEY (empresa_id) REFERENCES empresa (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE municipio CHANGE departamento_id departamento_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE nota_credito CHANGE factura_cliente_id factura_cliente_id BIGINT DEFAULT NULL, CHANGE subtotal subtotal DOUBLE PRECISION NOT NULL, CHANGE total total DOUBLE PRECISION NOT NULL, CHANGE total_iva total_iva DOUBLE PRECISION NOT NULL, CHANGE rete_fuente rete_fuente DOUBLE PRECISION NOT NULL, CHANGE total_rete_fuente_g total_rete_fuente_g DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE nota_credito_items CHANGE nota_credito_id nota_credito_id INT DEFAULT NULL, CHANGE valor_unitario valor_unitario DOUBLE PRECISION NOT NULL, CHANGE subtotal subtotal DOUBLE PRECISION NOT NULL, CHANGE iva iva DOUBLE PRECISION NOT NULL, CHANGE valor_iva valor_iva DOUBLE PRECISION NOT NULL, CHANGE total total DOUBLE PRECISION NOT NULL, CHANGE retencion_fuente retencion_fuente DOUBLE PRECISION NOT NULL, CHANGE valor_retencion_fuente valor_retencion_fuente DOUBLE PRECISION NOT NULL, CHANGE tasa_descuento tasa_descuento DOUBLE PRECISION NOT NULL, CHANGE valor_descuento valor_descuento DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE pais CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE pais_zona CHANGE pais_id pais_id INT DEFAULT NULL, CHANGE zona_id zona_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recibo_caja CHANGE firma firma VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tarifas CHANGE zona_id zona_id INT DEFAULT NULL, CHANGE tarifas_configuracion_id tarifas_configuracion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional DROP FOREIGN KEY trazabilidad_envio_nacional_ibfk_1');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional DROP FOREIGN KEY trazabilidad_envio_nacional_ibfk_1');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional CHANGE envio_nacional_id envio_nacional_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional ADD CONSTRAINT FK_F8232DC8A34909F1 FOREIGN KEY (envio_nacional_id) REFERENCES envios_nacionales (id)');
        $this->addSql('DROP INDEX envio_nacional_id ON trazabilidad_envio_nacional');
        $this->addSql('CREATE INDEX IDX_F8232DC8A34909F1 ON trazabilidad_envio_nacional (envio_nacional_id)');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional ADD CONSTRAINT trazabilidad_envio_nacional_ibfk_1 FOREIGN KEY (envio_nacional_id) REFERENCES envios_nacionales (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP INDEX UNIQ_50FE07D75E5F5AF3 ON clientes');
        $this->addSql('ALTER TABLE clientes CHANGE municipio_id municipio_id BIGINT DEFAULT 28 NOT NULL');
        $this->addSql('ALTER TABLE empresa CHANGE documento documento VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE envio DROP FOREIGN KEY FK_754737D558BC1BE0');
        $this->addSql('DROP INDEX IDX_754737D558BC1BE0 ON envio');
        $this->addSql('ALTER TABLE envio DROP FOREIGN KEY FK_754737D54F43FF31');
        $this->addSql('ALTER TABLE envio DROP municipio_id, CHANGE pais_destino pais_destino INT NOT NULL, CHANGE pais_origen pais_origen INT NOT NULL, CHANGE verificado verificado TINYINT(1) DEFAULT 0 NOT NULL, CHANGE referencia referencia TEXT DEFAULT NULL, CHANGE facturado_transportadora facturado_transportadora TINYINT(1) DEFAULT 0 NOT NULL, CHANGE factura_transportadora factura_transportadora VARCHAR(255) DEFAULT NULL, CHANGE facturado_recibo facturado_recibo TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX idx_754737d54f43ff31 ON envio');
        $this->addSql('CREATE INDEX recibo_caja_item_id ON envio (recibo_caja_item_id)');
        $this->addSql('ALTER TABLE envio ADD CONSTRAINT FK_754737D54F43FF31 FOREIGN KEY (recibo_caja_item_id) REFERENCES recibo_caja_item (id)');
        $this->addSql('ALTER TABLE envios_nacionales DROP FOREIGN KEY FK_C8565C0670A4000F');
        $this->addSql('ALTER TABLE envios_nacionales DROP FOREIGN KEY FK_C8565C0662F40C3D');
        $this->addSql('ALTER TABLE envios_nacionales DROP FOREIGN KEY FK_C8565C06FC98E6D9');
        $this->addSql('ALTER TABLE envios_nacionales CHANGE cliente_id cliente_id INT NOT NULL, CHANGE municipio_origen municipio_origen BIGINT NOT NULL, CHANGE municipio_destino municipio_destino BIGINT NOT NULL, CHANGE peso peso NUMERIC(20, 2) DEFAULT \'0.00\' NOT NULL, CHANGE valor_total valor_total NUMERIC(20, 2) DEFAULT \'0.00\' NOT NULL, CHANGE contra_entrega contra_entrega TINYINT(1) DEFAULT 0 NOT NULL, CHANGE facturado facturado TINYINT(1) DEFAULT 0 NOT NULL, CHANGE facturado_recibo facturado_recibo TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX idx_c8565c0662f40c3d ON envios_nacionales');
        $this->addSql('CREATE INDEX creador_id ON envios_nacionales (creador_id)');
        $this->addSql('DROP INDEX idx_c8565c06fc98e6d9 ON envios_nacionales');
        $this->addSql('CREATE INDEX recibo_items_id ON envios_nacionales (recibo_items_id)');
        $this->addSql('DROP INDEX idx_c8565c0670a4000f ON envios_nacionales');
        $this->addSql('CREATE INDEX factura_items_id ON envios_nacionales (factura_items_id)');
        $this->addSql('ALTER TABLE envios_nacionales ADD CONSTRAINT FK_C8565C0670A4000F FOREIGN KEY (factura_items_id) REFERENCES factura_items (id)');
        $this->addSql('ALTER TABLE envios_nacionales ADD CONSTRAINT FK_C8565C0662F40C3D FOREIGN KEY (creador_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE envios_nacionales ADD CONSTRAINT FK_C8565C06FC98E6D9 FOREIGN KEY (recibo_items_id) REFERENCES recibo_caja_item (id)');
        $this->addSql('ALTER TABLE envios_nacionales_unidades DROP FOREIGN KEY FK_E67D2EF3A34909F1');
        $this->addSql('ALTER TABLE envios_nacionales_unidades CHANGE envio_nacional_id envio_nacional_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE envios_nacionales_unidades ADD CONSTRAINT envios_nacionales_unidades_ibfk_1 FOREIGN KEY (envio_nacional_id) REFERENCES envios_nacionales (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE factura CHANGE cond_de_pago cond_de_pago INT DEFAULT 77 NOT NULL, CHANGE factura_resolucion_id factura_resolucion_id INT NOT NULL, CHANGE estado estado VARCHAR(20) DEFAULT \'0\' NOT NULL, CHANGE total total NUMERIC(20, 2) DEFAULT NULL, CHANGE soporte_pago soporte_pago VARCHAR(255) DEFAULT NULL, CHANGE facturado facturado TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE factura_items DROP FOREIGN KEY FK_876545E93CFD1E69');
        $this->addSql('ALTER TABLE factura_items CHANGE factura_clientes_id factura_clientes_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE factura_items ADD CONSTRAINT factura_items_ibfk_2 FOREIGN KEY (factura_clientes_id) REFERENCES factura (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE factura_resolucion DROP FOREIGN KEY FK_D904B4D7521E1991');
        $this->addSql('ALTER TABLE factura_resolucion CHANGE empresa_id empresa_id INT NOT NULL, CHANGE prefijo prefijo VARCHAR(125) CHARACTER SET latin1 DEFAULT NULL COLLATE `latin1_swedish_ci`');
        $this->addSql('DROP INDEX idx_d904b4d7521e1991 ON factura_resolucion');
        $this->addSql('CREATE INDEX empresa_id ON factura_resolucion (empresa_id)');
        $this->addSql('ALTER TABLE factura_resolucion ADD CONSTRAINT FK_D904B4D7521E1991 FOREIGN KEY (empresa_id) REFERENCES empresa (id)');
        $this->addSql('ALTER TABLE municipio CHANGE departamento_id departamento_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE nota_credito CHANGE factura_cliente_id factura_cliente_id BIGINT NOT NULL, CHANGE subtotal subtotal DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE total total DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE total_iva total_iva DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE rete_fuente rete_fuente DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE total_rete_fuente_g total_rete_fuente_g DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE nota_credito_items CHANGE nota_credito_id nota_credito_id INT NOT NULL, CHANGE valor_unitario valor_unitario DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE subtotal subtotal DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE iva iva DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE valor_iva valor_iva DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE total total DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE retencion_fuente retencion_fuente DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE valor_retencion_fuente valor_retencion_fuente DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE tasa_descuento tasa_descuento DOUBLE PRECISION DEFAULT \'0\' NOT NULL, CHANGE valor_descuento valor_descuento DOUBLE PRECISION DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE pais CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE pais_zona CHANGE pais_id pais_id INT NOT NULL, CHANGE zona_id zona_id INT NOT NULL');
        $this->addSql('ALTER TABLE recibo_caja CHANGE firma firma VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tarifas CHANGE tarifas_configuracion_id tarifas_configuracion_id INT NOT NULL, CHANGE zona_id zona_id INT NOT NULL');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional DROP FOREIGN KEY FK_F8232DC8A34909F1');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional DROP FOREIGN KEY FK_F8232DC8A34909F1');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional CHANGE envio_nacional_id envio_nacional_id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional ADD CONSTRAINT trazabilidad_envio_nacional_ibfk_1 FOREIGN KEY (envio_nacional_id) REFERENCES envios_nacionales (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP INDEX idx_f8232dc8a34909f1 ON trazabilidad_envio_nacional');
        $this->addSql('CREATE INDEX envio_nacional_id ON trazabilidad_envio_nacional (envio_nacional_id)');
        $this->addSql('ALTER TABLE trazabilidad_envio_nacional ADD CONSTRAINT FK_F8232DC8A34909F1 FOREIGN KEY (envio_nacional_id) REFERENCES envios_nacionales (id)');
    }
}
