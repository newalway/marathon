<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181001030413 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer_orders_discounts (customer_order_id INT NOT NULL, discount_id INT NOT NULL, INDEX IDX_9E3611FCA15A2E17 (customer_order_id), INDEX IDX_9E3611FC4C7C611F (discount_id), PRIMARY KEY(customer_order_id, discount_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_orders_discounts ADD CONSTRAINT FK_9E3611FCA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_orders_discounts ADD CONSTRAINT FK_9E3611FC4C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE customer_orders_discounts');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
