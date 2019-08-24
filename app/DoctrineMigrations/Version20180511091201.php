<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180511091201 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE variant (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_F143BFAD4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE variant ADD CONSTRAINT FK_F143BFAD4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sku ADD variant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sku ADD CONSTRAINT FK_F9038C43B69A9AF FOREIGN KEY (variant_id) REFERENCES variant (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_F9038C43B69A9AF ON sku (variant_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sku DROP FOREIGN KEY FK_F9038C43B69A9AF');
        $this->addSql('DROP TABLE variant');
        $this->addSql('DROP INDEX IDX_F9038C43B69A9AF ON sku');
        $this->addSql('ALTER TABLE sku DROP variant_id');
    }
}
