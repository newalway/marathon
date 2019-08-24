<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181227050926 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_category (id INT AUTO_INCREMENT NOT NULL, tree_root INT DEFAULT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, lft INT NOT NULL, lvl INT NOT NULL, rgt INT NOT NULL, INDEX IDX_CDFC7356A977936C (tree_root), INDEX IDX_CDFC7356727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC7356A977936C FOREIGN KEY (tree_root) REFERENCES product_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC7356727ACA70 FOREIGN KEY (parent_id) REFERENCES product_category (id) ON DELETE CASCADE');
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

        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC7356A977936C');
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC7356727ACA70');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('ALTER TABLE product CHANGE is_new is_new TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE sku CHANGE default_option default_option TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
