<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180927074145 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer_payment_epayment (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_order_delivery (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_order (id INT AUTO_INCREMENT NOT NULL, fos_user_id INT DEFAULT NULL, order_number VARCHAR(45) NOT NULL, order_date DATETIME NOT NULL, ship_date DATE DEFAULT NULL, item_count INT DEFAULT NULL, shipping_cost NUMERIC(10, 2) DEFAULT NULL, sub_total NUMERIC(10, 2) DEFAULT NULL, discount_code NUMERIC(10, 2) DEFAULT NULL, discount_amount NUMERIC(10, 2) DEFAULT NULL, total_price NUMERIC(10, 2) DEFAULT NULL, payment_option VARCHAR(255) NOT NULL, payment_option_title VARCHAR(255) NOT NULL, paid SMALLINT NOT NULL, fulfilled SMALLINT NOT NULL, cancelled SMALLINT NOT NULL, deleted SMALLINT NOT NULL, note TEXT DEFAULT NULL, INDEX IDX_3B1CE6A38C20A0FB (fos_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_order_item (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A38C20A0FB FOREIGN KEY (fos_user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE payment_bank_transfer CHANGE date_transfer date_transfer DATE DEFAULT NULL, CHANGE time_transfer time_transfer TIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE customer_payment_epayment');
        $this->addSql('DROP TABLE customer_order_delivery');
        $this->addSql('DROP TABLE customer_order');
        $this->addSql('DROP TABLE customer_order_item');
        $this->addSql('ALTER TABLE payment_bank_transfer CHANGE date_transfer date_transfer DATE NOT NULL, CHANGE time_transfer time_transfer TIME NOT NULL');
    }
}
