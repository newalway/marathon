<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180927111907 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order_delivery ADD address_type SMALLINT NOT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(45) DEFAULT NULL, ADD district VARCHAR(255) DEFAULT NULL, ADD province VARCHAR(255) DEFAULT NULL, ADD country VARCHAR(255) DEFAULT NULL, ADD postcode VARCHAR(45) DEFAULT NULL, ADD tax_payer_id VARCHAR(255) DEFAULT NULL, ADD company_name VARCHAR(255) DEFAULT NULL, ADD head_office VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE delivery_address ADD company_name VARCHAR(255) DEFAULT NULL, ADD head_office VARCHAR(255) DEFAULT NULL, CHANGE taxpayer_id tax_payer_id VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order_delivery DROP address_type, DROP first_name, DROP last_name, DROP address, DROP phone, DROP district, DROP province, DROP country, DROP postcode, DROP tax_payer_id, DROP company_name, DROP head_office');
        $this->addSql('ALTER TABLE delivery_address ADD taxpayer_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP tax_payer_id, DROP company_name, DROP head_office');
    }
}
