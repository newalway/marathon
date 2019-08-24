<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180511095128 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE variant_option (id INT AUTO_INCREMENT NOT NULL, variant_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_4FDCA7663B69A9AF (variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE variant_option ADD CONSTRAINT FK_4FDCA7663B69A9AF FOREIGN KEY (variant_id) REFERENCES variant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sku ADD variant_option_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sku ADD CONSTRAINT FK_F9038C44438C63C FOREIGN KEY (variant_option_id) REFERENCES variant_option (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_F9038C44438C63C ON sku (variant_option_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sku DROP FOREIGN KEY FK_F9038C44438C63C');
        $this->addSql('DROP TABLE variant_option');
        $this->addSql('DROP INDEX IDX_F9038C44438C63C ON sku');
        $this->addSql('ALTER TABLE sku DROP variant_option_id');
    }
}
