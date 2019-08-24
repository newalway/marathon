<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190405052739 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE template_customer_groups_product_categories (product_category_id INT NOT NULL, template_customer_group_id INT NOT NULL, INDEX IDX_C746AD9CBE6903FD (product_category_id), INDEX IDX_C746AD9C23AF50AA (template_customer_group_id), PRIMARY KEY(product_category_id, template_customer_group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE template_customer_groups_product_categories ADD CONSTRAINT FK_C746AD9CBE6903FD FOREIGN KEY (product_category_id) REFERENCES product_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE template_customer_groups_product_categories ADD CONSTRAINT FK_C746AD9C23AF50AA FOREIGN KEY (template_customer_group_id) REFERENCES template_customer_group (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE template_customer_groups_product_categories');
    }
}
