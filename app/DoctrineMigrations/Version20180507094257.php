<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180507094257 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE about_us (id INT AUTO_INCREMENT NOT NULL, status SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C74F21955F37A13B (token), INDEX IDX_C74F219519EB6921 (client_id), INDEX IDX_C74F2195A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE authentication (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, input_type VARCHAR(255) DEFAULT NULL, group_type VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX authentication_l_1 (name), INDEX authentication_l_2 (group_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, equipment_id INT DEFAULT NULL, brand_id INT DEFAULT NULL, power_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, price NUMERIC(10, 2) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, short_desc TEXT DEFAULT NULL, description TEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, status SMALLINT NOT NULL, position SMALLINT NOT NULL, INDEX IDX_D34A04AD517FE9FE (equipment_id), INDEX IDX_D34A04AD44F5D008 (brand_id), INDEX IDX_D34A04ADAB4FC384 (power_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_customer_groups (product_id INT NOT NULL, customer_group_id INT NOT NULL, INDEX IDX_9443C9A4584665A (product_id), INDEX IDX_9443C9AD2919A68 (customer_group_id), PRIMARY KEY(product_id, customer_group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products_age_groups (product_id INT NOT NULL, age_group_id INT NOT NULL, INDEX IDX_3BCBBC194584665A (product_id), INDEX IDX_3BCBBC19B09E220E (age_group_id), PRIMARY KEY(product_id, age_group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banner (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, image_mobile VARCHAR(255) DEFAULT NULL, website TEXT DEFAULT NULL, public_date DATE DEFAULT NULL, status SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_group (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE power (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, short_desc TEXT DEFAULT NULL, description TEXT DEFAULT NULL, filepath VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, filesize NUMERIC(10, 1) DEFAULT NULL, download_count INT NOT NULL, public_date DATE DEFAULT NULL, status SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(45) DEFAULT NULL, birth_date DATE DEFAULT NULL, company_name VARCHAR(255) DEFAULT NULL, oauth SMALLINT NOT NULL, facebook_id VARCHAR(255) DEFAULT NULL, facebook_access_token VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, google_access_token VARCHAR(255) DEFAULT NULL, service_name VARCHAR(255) DEFAULT NULL, service_email VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B6A2DD685F37A13B (token), INDEX IDX_B6A2DD6819EB6921 (client_id), INDEX IDX_B6A2DD68A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, telephone_number VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, message TEXT DEFAULT NULL, status SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_image (id INT AUTO_INCREMENT NOT NULL, blog_id INT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, position SMALLINT NOT NULL, INDEX IDX_35D24797DAE07E97 (blog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion_download_counter (id INT AUTO_INCREMENT NOT NULL, promotion_id INT DEFAULT NULL, ip_address VARCHAR(255) DEFAULT NULL, browser_name VARCHAR(255) DEFAULT NULL, platform VARCHAR(255) DEFAULT NULL, browser VARCHAR(255) DEFAULT NULL, version VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, country_name VARCHAR(255) DEFAULT NULL, city_name VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, location_latitude VARCHAR(255) DEFAULT NULL, location_longitude VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_8225F94D139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_code (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_5933D02C5F37A13B (token), INDEX IDX_5933D02C19EB6921 (client_id), INDEX IDX_5933D02CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, short_desc TEXT DEFAULT NULL, description TEXT DEFAULT NULL, public_date DATE DEFAULT NULL, status SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting_option (id INT AUTO_INCREMENT NOT NULL, option_name VARCHAR(255) NOT NULL, option_value LONGTEXT DEFAULT NULL, option_title VARCHAR(255) DEFAULT NULL, option_type VARCHAR(255) DEFAULT NULL, group_type VARCHAR(255) DEFAULT NULL, cat_type VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX setting_option_l_1 (cat_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE about_us_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content MEDIUMTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_F295A6712C2AC5D3 (translatable_id), UNIQUE INDEX about_us_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE age_group (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hashtag (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, position SMALLINT NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', secret VARCHAR(255) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F219519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADAB4FC384 FOREIGN KEY (power_id) REFERENCES power (id)');
        $this->addSql('ALTER TABLE products_customer_groups ADD CONSTRAINT FK_9443C9A4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_customer_groups ADD CONSTRAINT FK_9443C9AD2919A68 FOREIGN KEY (customer_group_id) REFERENCES customer_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_age_groups ADD CONSTRAINT FK_3BCBBC194584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_age_groups ADD CONSTRAINT FK_3BCBBC19B09E220E FOREIGN KEY (age_group_id) REFERENCES age_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE blog_image ADD CONSTRAINT FK_35D24797DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promotion_download_counter ADD CONSTRAINT FK_8225F94D139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE about_us_translation ADD CONSTRAINT FK_F295A6712C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES about_us (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE about_us_translation DROP FOREIGN KEY FK_F295A6712C2AC5D3');
        $this->addSql('ALTER TABLE products_customer_groups DROP FOREIGN KEY FK_9443C9A4584665A');
        $this->addSql('ALTER TABLE products_age_groups DROP FOREIGN KEY FK_3BCBBC194584665A');
        $this->addSql('ALTER TABLE products_customer_groups DROP FOREIGN KEY FK_9443C9AD2919A68');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADAB4FC384');
        $this->addSql('ALTER TABLE promotion_download_counter DROP FOREIGN KEY FK_8225F94D139DF194');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F2195A76ED395');
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD68A76ED395');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02CA76ED395');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD517FE9FE');
        $this->addSql('ALTER TABLE blog_image DROP FOREIGN KEY FK_35D24797DAE07E97');
        $this->addSql('ALTER TABLE products_age_groups DROP FOREIGN KEY FK_3BCBBC19B09E220E');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F219519EB6921');
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD6819EB6921');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02C19EB6921');
        $this->addSql('DROP TABLE about_us');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE authentication');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE products_customer_groups');
        $this->addSql('DROP TABLE products_age_groups');
        $this->addSql('DROP TABLE banner');
        $this->addSql('DROP TABLE customer_group');
        $this->addSql('DROP TABLE power');
        $this->addSql('DROP TABLE promotion');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE blog_image');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE promotion_download_counter');
        $this->addSql('DROP TABLE auth_code');
        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP TABLE setting_option');
        $this->addSql('DROP TABLE about_us_translation');
        $this->addSql('DROP TABLE age_group');
        $this->addSql('DROP TABLE hashtag');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE client');
    }
}
