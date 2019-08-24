<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180925080128 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment_bank_transfer CHANGE bank_account_id bank_account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment_bank_transfer ADD CONSTRAINT FK_8C70B08A12CB990C FOREIGN KEY (bank_account_id) REFERENCES bank_account (id)');
        $this->addSql('CREATE INDEX IDX_8C70B08A12CB990C ON payment_bank_transfer (bank_account_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment_bank_transfer DROP FOREIGN KEY FK_8C70B08A12CB990C');
        $this->addSql('DROP INDEX IDX_8C70B08A12CB990C ON payment_bank_transfer');
        $this->addSql('ALTER TABLE payment_bank_transfer CHANGE bank_account_id bank_account_id INT NOT NULL');
    }
}
