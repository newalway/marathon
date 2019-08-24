<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180927100709 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_payment_bank_transfer ADD customer_order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_payment_bank_transfer ADD CONSTRAINT FK_DA1C2826A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('CREATE INDEX IDX_DA1C2826A15A2E17 ON customer_payment_bank_transfer (customer_order_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_payment_bank_transfer DROP FOREIGN KEY FK_DA1C2826A15A2E17');
        $this->addSql('DROP INDEX IDX_DA1C2826A15A2E17 ON customer_payment_bank_transfer');
        $this->addSql('ALTER TABLE customer_payment_bank_transfer DROP customer_order_id');
    }
}
