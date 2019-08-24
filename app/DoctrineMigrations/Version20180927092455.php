<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180927092455 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order_item ADD customer_order_id INT DEFAULT NULL, ADD product_id INT DEFAULT NULL, ADD sku_id INT DEFAULT NULL, ADD product_title VARCHAR(255) NOT NULL, ADD price NUMERIC(10, 2) DEFAULT NULL, ADD compare_at_price NUMERIC(10, 2) DEFAULT NULL, ADD discount NUMERIC(10, 2) DEFAULT NULL, ADD quantity NUMERIC(10, 2) DEFAULT NULL, ADD amount NUMERIC(10, 2) DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8BA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8B1777D41C FOREIGN KEY (sku_id) REFERENCES sku (id)');
        $this->addSql('CREATE INDEX IDX_AF231B8BA15A2E17 ON customer_order_item (customer_order_id)');
        $this->addSql('CREATE INDEX IDX_AF231B8B4584665A ON customer_order_item (product_id)');
        $this->addSql('CREATE INDEX IDX_AF231B8B1777D41C ON customer_order_item (sku_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8BA15A2E17');
        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8B4584665A');
        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8B1777D41C');
        $this->addSql('DROP INDEX IDX_AF231B8BA15A2E17 ON customer_order_item');
        $this->addSql('DROP INDEX IDX_AF231B8B4584665A ON customer_order_item');
        $this->addSql('DROP INDEX IDX_AF231B8B1777D41C ON customer_order_item');
        $this->addSql('ALTER TABLE customer_order_item DROP customer_order_id, DROP product_id, DROP sku_id, DROP product_title, DROP price, DROP compare_at_price, DROP discount, DROP quantity, DROP amount, DROP image');
    }
}
