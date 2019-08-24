<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180528084820 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product ADD compare_at_price NUMERIC(10, 2) DEFAULT NULL, ADD inventory_policy_status SMALLINT NOT NULL, ADD inventory_quantity INT DEFAULT NULL, ADD grams INT DEFAULT NULL, ADD weight INT DEFAULT NULL, ADD weight_unit VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE sku ADD compare_at_price NUMERIC(10, 2) DEFAULT NULL, ADD inventory_quantity INT DEFAULT NULL, ADD grams INT DEFAULT NULL, ADD weight INT DEFAULT NULL, ADD weight_unit VARCHAR(45) DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD default_option SMALLINT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product DROP compare_at_price, DROP inventory_policy_status, DROP inventory_quantity, DROP grams, DROP weight, DROP weight_unit');
        $this->addSql('ALTER TABLE sku DROP compare_at_price, DROP inventory_quantity, DROP grams, DROP weight, DROP weight_unit, DROP image, DROP default_option');
    }
}
