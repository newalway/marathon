<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181003081244 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE country_code (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(255) NOT NULL, iso_code VARCHAR(5) NOT NULL, dial_code VARCHAR(45) DEFAULT NULL, nationality VARCHAR(255) DEFAULT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE delivery_address ADD country_code_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE delivery_address ADD CONSTRAINT FK_750D05FEE96A67A FOREIGN KEY (country_code_id) REFERENCES country_code (id)');
        $this->addSql('CREATE INDEX IDX_750D05FEE96A67A ON delivery_address (country_code_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery_address DROP FOREIGN KEY FK_750D05FEE96A67A');
        $this->addSql('DROP TABLE country_code');
        $this->addSql('DROP INDEX IDX_750D05FEE96A67A ON delivery_address');
        $this->addSql('ALTER TABLE delivery_address DROP country_code_id');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
