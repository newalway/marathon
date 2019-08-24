<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180925075059 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment_bank_transfer ADD order_id INT NOT NULL, ADD order_number VARCHAR(45) NOT NULL, ADD bank_account_id INT NOT NULL, ADD first_name VARCHAR(255) DEFAULT NULL, ADD last_name VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(45) DEFAULT NULL, ADD amount NUMERIC(10, 2) NOT NULL, ADD attach_file VARCHAR(255) DEFAULT NULL, ADD date_transfer DATE NOT NULL, ADD time_transfer TIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD created_at DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment_bank_transfer DROP order_id, DROP order_number, DROP bank_account_id, DROP first_name, DROP last_name, DROP phone, DROP amount, DROP attach_file, DROP date_transfer, DROP time_transfer, DROP updated_at, DROP created_at');
    }
}
