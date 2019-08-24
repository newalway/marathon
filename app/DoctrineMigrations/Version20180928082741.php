<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180928082741 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order_delivery ADD customer_order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_order_delivery ADD CONSTRAINT FK_4F143A6BA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('CREATE INDEX IDX_4F143A6BA15A2E17 ON customer_order_delivery (customer_order_id)');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order_delivery DROP FOREIGN KEY FK_4F143A6BA15A2E17');
        $this->addSql('DROP INDEX IDX_4F143A6BA15A2E17 ON customer_order_delivery');
        $this->addSql('ALTER TABLE customer_order_delivery DROP customer_order_id');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
