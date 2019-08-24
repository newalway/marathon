<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181009122805 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_payment_epayment DROP FOREIGN KEY FK_5D0A3AB1A15A2E17');
        $this->addSql('ALTER TABLE customer_payment_epayment ADD CONSTRAINT FK_5D0A3AB1A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_order_delivery DROP FOREIGN KEY FK_4F143A6BA15A2E17');
        $this->addSql('ALTER TABLE customer_order_delivery ADD CONSTRAINT FK_4F143A6BA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8BA15A2E17');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8BA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_payment_bank_transfer DROP FOREIGN KEY FK_DA1C2826A15A2E17');
        $this->addSql('ALTER TABLE customer_payment_bank_transfer ADD CONSTRAINT FK_DA1C2826A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order_delivery DROP FOREIGN KEY FK_4F143A6BA15A2E17');
        $this->addSql('ALTER TABLE customer_order_delivery ADD CONSTRAINT FK_4F143A6BA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8BA15A2E17');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8BA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('ALTER TABLE customer_payment_bank_transfer DROP FOREIGN KEY FK_DA1C2826A15A2E17');
        $this->addSql('ALTER TABLE customer_payment_bank_transfer ADD CONSTRAINT FK_DA1C2826A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('ALTER TABLE customer_payment_epayment DROP FOREIGN KEY FK_5D0A3AB1A15A2E17');
        $this->addSql('ALTER TABLE customer_payment_epayment ADD CONSTRAINT FK_5D0A3AB1A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
