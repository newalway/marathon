<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181210031135 extends AbstractMigration
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
        $this->addSql('CREATE INDEX customer_order_order_number_1 ON customer_order (order_number)');
        $this->addSql('CREATE INDEX customer_order_order_paid_1 ON customer_order (paid)');
        $this->addSql('CREATE INDEX customer_order_order_fulfilled_1 ON customer_order (fulfilled)');
        $this->addSql('CREATE INDEX customer_order_order_cancelled_1 ON customer_order (cancelled)');
        $this->addSql('CREATE INDEX customer_order_order_refunded_1 ON customer_order (refunded)');
        $this->addSql('CREATE INDEX customer_order_order_deleted_1 ON customer_order (deleted)');
        $this->addSql('CREATE INDEX customer_order_order_is_read_1 ON customer_order (is_read)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX customer_order_order_number_1 ON customer_order');
        $this->addSql('DROP INDEX customer_order_order_paid_1 ON customer_order');
        $this->addSql('DROP INDEX customer_order_order_fulfilled_1 ON customer_order');
        $this->addSql('DROP INDEX customer_order_order_cancelled_1 ON customer_order');
        $this->addSql('DROP INDEX customer_order_order_refunded_1 ON customer_order');
        $this->addSql('DROP INDEX customer_order_order_deleted_1 ON customer_order');
        $this->addSql('DROP INDEX customer_order_order_is_read_1 ON customer_order');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
