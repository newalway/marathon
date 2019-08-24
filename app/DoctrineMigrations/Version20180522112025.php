<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180522112025 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sku (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, sku VARCHAR(255) DEFAULT NULL, price NUMERIC(10, 2) DEFAULT NULL, qty INT DEFAULT NULL, INDEX IDX_F9038C44584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sku ADD CONSTRAINT FK_F9038C44584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sku_value ADD sku_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sku_value ADD CONSTRAINT FK_3A7BBDD41777D41C FOREIGN KEY (sku_id) REFERENCES sku (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_3A7BBDD41777D41C ON sku_value (sku_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sku_value DROP FOREIGN KEY FK_3A7BBDD41777D41C');
        $this->addSql('DROP TABLE sku');
        $this->addSql('DROP INDEX IDX_3A7BBDD41777D41C ON sku_value');
        $this->addSql('ALTER TABLE sku_value DROP sku_id');
    }
}
