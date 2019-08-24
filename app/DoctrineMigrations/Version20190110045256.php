<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190110045256 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE layout_shop (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) NOT NULL, link_url VARCHAR(255) DEFAULT NULL, status SMALLINT NOT NULL, position SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE layout_shop_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, content TEXT DEFAULT NULL, link_title VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_63592E382C2AC5D3 (translatable_id), UNIQUE INDEX layout_shop_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE layout_shop_translation ADD CONSTRAINT FK_63592E382C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES layout_shop (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE layout_shop_translation DROP FOREIGN KEY FK_63592E382C2AC5D3');
        $this->addSql('DROP TABLE layout_shop');
        $this->addSql('DROP TABLE layout_shop_translation');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
