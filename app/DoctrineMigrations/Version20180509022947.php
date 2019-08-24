<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180509022947 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE brand_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_D538659A2C2AC5D3 (translatable_id), UNIQUE INDEX brand_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE brand_translation ADD CONSTRAINT FK_D538659A2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES brand (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE brand DROP title, DROP description');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE brand_translation');
        $this->addSql('ALTER TABLE brand ADD title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD description TEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
