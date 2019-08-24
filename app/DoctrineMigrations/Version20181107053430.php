<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181107053430 extends AbstractMigration
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
        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8B1777D41C');
        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8B4584665A');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8B1777D41C FOREIGN KEY (sku_id) REFERENCES sku (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8B4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8B4584665A');
        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8B1777D41C');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8B1777D41C FOREIGN KEY (sku_id) REFERENCES sku (id)');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
