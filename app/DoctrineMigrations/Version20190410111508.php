<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190410111508 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) DEFAULT NULL, position SMALLINT NOT NULL, status SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_category (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, position SMALLINT NOT NULL, status SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_category_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_836038B82C2AC5D3 (translatable_id), INDEX search_idx (title), UNIQUE INDEX event_category_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_1FE096EF2C2AC5D3 (translatable_id), INDEX search_idx (title), UNIQUE INDEX event_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_category_translation ADD CONSTRAINT FK_836038B82C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES event_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_translation ADD CONSTRAINT FK_1FE096EF2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES event (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE event_translation DROP FOREIGN KEY FK_1FE096EF2C2AC5D3');
        $this->addSql('ALTER TABLE event_category_translation DROP FOREIGN KEY FK_836038B82C2AC5D3');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_category');
        $this->addSql('DROP TABLE event_category_translation');
        $this->addSql('DROP TABLE event_translation');
    }
}
