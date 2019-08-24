<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180706080342 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE banner_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, website TEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_841ECF1C2C2AC5D3 (translatable_id), INDEX search_idx (title), UNIQUE INDEX banner_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE banner_translation ADD CONSTRAINT FK_841ECF1C2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES banner (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE banner DROP title, DROP website, DROP sub_title');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE banner_translation');
        $this->addSql('ALTER TABLE banner ADD title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD website TEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD sub_title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
