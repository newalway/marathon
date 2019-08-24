<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180509042542 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer_group_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_85C0A7B32C2AC5D3 (translatable_id), UNIQUE INDEX customer_group_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_group_translation ADD CONSTRAINT FK_85C0A7B32C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES customer_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_group DROP title');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE customer_group_translation');
        $this->addSql('ALTER TABLE customer_group ADD title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
