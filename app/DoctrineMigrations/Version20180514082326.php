<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180514082326 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE about_us_translation DROP FOREIGN KEY FK_F295A6712C2AC5D3');
        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content MEDIUMTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_7D0CA9D12C2AC5D3 (translatable_id), UNIQUE INDEX pages_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pages_translation ADD CONSTRAINT FK_7D0CA9D12C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES pages (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE about_us');
        $this->addSql('DROP TABLE about_us_translation');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pages_translation DROP FOREIGN KEY FK_7D0CA9D12C2AC5D3');
        $this->addSql('CREATE TABLE about_us (id INT AUTO_INCREMENT NOT NULL, status SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE about_us_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, content MEDIUMTEXT DEFAULT NULL COLLATE utf8_unicode_ci, locale VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX about_us_translation_unique_translation (translatable_id, locale), INDEX IDX_F295A6712C2AC5D3 (translatable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE about_us_translation ADD CONSTRAINT FK_F295A6712C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES about_us (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE pages_translation');
    }
}
