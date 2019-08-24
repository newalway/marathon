<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181219103302 extends AbstractMigration
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
        $this->addSql('ALTER TABLE customer_order ADD direction_distance INT DEFAULT NULL, ADD direction_distance_text VARCHAR(255) DEFAULT NULL, ADD direction_origin_showroom_id INT DEFAULT NULL, ADD direction_origin_lat_lng VARCHAR(255) DEFAULT NULL, ADD direction_origin_showroom_name VARCHAR(255) DEFAULT NULL, ADD direction_destination_delivery_address_id INT DEFAULT NULL, ADD direction_destination_lat_lng VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order DROP direction_distance, DROP direction_distance_text, DROP direction_origin_showroom_id, DROP direction_origin_lat_lng, DROP direction_origin_showroom_name, DROP direction_destination_delivery_address_id, DROP direction_destination_lat_lng');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
