<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180928033811 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order_delivery CHANGE address_type address_type SMALLINT UNSIGNED DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(10, 2) DEFAULT \'0\', CHANGE status status SMALLINT UNSIGNED DEFAULT 1 NOT NULL, CHANGE position position INT DEFAULT 0 NOT NULL, CHANGE compare_at_price compare_at_price NUMERIC(10, 2) DEFAULT \'0\', CHANGE inventory_policy_status inventory_policy_status SMALLINT DEFAULT 0 NOT NULL, CHANGE grams grams NUMERIC(10, 2) DEFAULT \'0\', CHANGE weight weight NUMERIC(10, 2) DEFAULT \'0\'');
        $this->addSql('ALTER TABLE sku CHANGE status status SMALLINT UNSIGNED DEFAULT 1 NOT NULL, CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE inventory_policy_status inventory_policy_status SMALLINT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE customer_order CHANGE paid paid SMALLINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE fulfilled fulfilled SMALLINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE cancelled cancelled SMALLINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE deleted deleted SMALLINT UNSIGNED DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE customer_order_item ADD inventory_policy_status SMALLINT UNSIGNED DEFAULT 0 NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order CHANGE paid paid SMALLINT NOT NULL, CHANGE fulfilled fulfilled SMALLINT NOT NULL, CHANGE cancelled cancelled SMALLINT NOT NULL, CHANGE deleted deleted SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE customer_order_delivery CHANGE address_type address_type SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE customer_order_item DROP inventory_policy_status');
        $this->addSql('ALTER TABLE product CHANGE price price NUMERIC(10, 2) DEFAULT NULL, CHANGE status status SMALLINT NOT NULL, CHANGE position position INT NOT NULL, CHANGE compare_at_price compare_at_price NUMERIC(10, 2) DEFAULT NULL, CHANGE inventory_policy_status inventory_policy_status SMALLINT NOT NULL, CHANGE grams grams NUMERIC(10, 2) DEFAULT NULL, CHANGE weight weight NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE sku CHANGE status status SMALLINT NOT NULL, CHANGE inventory_policy_status inventory_policy_status SMALLINT NOT NULL, CHANGE default_option default_option TINYINT(1) NOT NULL');
    }
}
