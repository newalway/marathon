<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180920070547 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery_address ADD fos_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE delivery_address ADD CONSTRAINT FK_750D05F8C20A0FB FOREIGN KEY (fos_user_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_750D05F8C20A0FB ON delivery_address (fos_user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery_address DROP FOREIGN KEY FK_750D05F8C20A0FB');
        $this->addSql('DROP INDEX IDX_750D05F8C20A0FB ON delivery_address');
        $this->addSql('ALTER TABLE delivery_address DROP fos_user_id');
    }
}
