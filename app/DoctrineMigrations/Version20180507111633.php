<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180507111633 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE muscle (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_muscles (product_id INT NOT NULL, muscle_id INT NOT NULL, INDEX IDX_F075DBA64584665A (product_id), INDEX IDX_F075DBA6354FDBB4 (muscle_id), PRIMARY KEY(product_id, muscle_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE products_muscles ADD CONSTRAINT FK_F075DBA64584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_muscles ADD CONSTRAINT FK_F075DBA6354FDBB4 FOREIGN KEY (muscle_id) REFERENCES muscle (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE products_muscles DROP FOREIGN KEY FK_F075DBA6354FDBB4');
        $this->addSql('DROP TABLE muscle');
        $this->addSql('DROP TABLE products_muscles');
    }
}
