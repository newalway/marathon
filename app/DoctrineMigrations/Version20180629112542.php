<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180629112542 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, discount_code VARCHAR(255) NOT NULL, discount_type SMALLINT NOT NULL, discount_value INT NOT NULL, applies_to SMALLINT NOT NULL, only_applies_once_product_per_order TINYINT(1) NOT NULL, minimum_requirement SMALLINT NOT NULL, minimum_requirement_value INT DEFAULT NULL, usage_limit_discount_total TINYINT(1) NOT NULL, usage_limit_discount_total_value INT DEFAULT NULL, usage_limit_discount_one_per_customer TINYINT(1) NOT NULL, start_date DATETIME NOT NULL, is_end_date TINYINT(1) NOT NULL, end_date DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE discount');
    }
}
