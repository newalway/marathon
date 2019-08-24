<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190327073825 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE promotion_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, short_desc TEXT DEFAULT NULL, description TEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_65978CB52C2AC5D3 (translatable_id), UNIQUE INDEX promotion_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promotion_translation ADD CONSTRAINT FK_65978CB52C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES promotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promotion DROP title, DROP image, DROP short_desc, DROP description');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE promotion_translation');
        $this->addSql('ALTER TABLE promotion ADD title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD image VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD short_desc TEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD description TEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
