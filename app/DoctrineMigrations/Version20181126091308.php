<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181126091308 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE tracking_number ADD customer_order_id INT DEFAULT NULL, ADD shipping_carrier_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE tracking_number ADD CONSTRAINT FK_3E1C9C18A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tracking_number ADD CONSTRAINT FK_3E1C9C18992497C9 FOREIGN KEY (shipping_carrier_id) REFERENCES shipping_carrier (id) ON DELETE RESTRICT');
        $this->addSql('CREATE INDEX IDX_3E1C9C18A15A2E17 ON tracking_number (customer_order_id)');
        $this->addSql('CREATE INDEX IDX_3E1C9C18992497C9 ON tracking_number (shipping_carrier_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE tracking_number DROP FOREIGN KEY FK_3E1C9C18A15A2E17');
        $this->addSql('ALTER TABLE tracking_number DROP FOREIGN KEY FK_3E1C9C18992497C9');
        $this->addSql('DROP INDEX IDX_3E1C9C18A15A2E17 ON tracking_number');
        $this->addSql('DROP INDEX IDX_3E1C9C18992497C9 ON tracking_number');
        $this->addSql('ALTER TABLE tracking_number DROP customer_order_id, DROP shipping_carrier_id, DROP updated_at, DROP created_at');
    }
}
