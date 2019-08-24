<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180522104556 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sku_value (id INT AUTO_INCREMENT NOT NULL, variant_id INT DEFAULT NULL, variant_option_id INT DEFAULT NULL, INDEX IDX_3A7BBDD43B69A9AF (variant_id), INDEX IDX_3A7BBDD44438C63C (variant_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sku_value ADD CONSTRAINT FK_3A7BBDD43B69A9AF FOREIGN KEY (variant_id) REFERENCES variant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sku_value ADD CONSTRAINT FK_3A7BBDD44438C63C FOREIGN KEY (variant_option_id) REFERENCES variant_option (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sku');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sku (id INT AUTO_INCREMENT NOT NULL, variant_id INT DEFAULT NULL, variant_option_id INT DEFAULT NULL, INDEX IDX_F9038C43B69A9AF (variant_id), INDEX IDX_F9038C44438C63C (variant_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sku ADD CONSTRAINT FK_F9038C43B69A9AF FOREIGN KEY (variant_id) REFERENCES variant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sku ADD CONSTRAINT FK_F9038C44438C63C FOREIGN KEY (variant_option_id) REFERENCES variant_option (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sku_value');
    }
}
