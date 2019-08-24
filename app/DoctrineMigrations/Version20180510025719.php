<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180510025719 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX search_idx ON muscle_translation (title)');
        $this->addSql('CREATE INDEX search_idx ON power_translation (title)');
        $this->addSql('CREATE INDEX search_idx ON product_translation (title)');
        $this->addSql('CREATE INDEX search_idx ON age_group_translation (title)');
        $this->addSql('CREATE INDEX search_idx ON brand_translation (title)');
        $this->addSql('CREATE INDEX search_idx ON customer_group_translation (title)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX search_idx ON age_group_translation');
        $this->addSql('DROP INDEX search_idx ON brand_translation');
        $this->addSql('DROP INDEX search_idx ON customer_group_translation');
        $this->addSql('DROP INDEX search_idx ON muscle_translation');
        $this->addSql('DROP INDEX search_idx ON power_translation');
        $this->addSql('DROP INDEX search_idx ON product_translation');
    }
}
