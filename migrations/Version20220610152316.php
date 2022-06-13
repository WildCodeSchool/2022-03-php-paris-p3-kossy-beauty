<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220610152316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA53A8AA FOREIGN KEY (provider_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9A53A8AA FOREIGN KEY (provider_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FC8EAE55E');
        $this->addSql('DROP INDEX IDX_B6BD307FC8EAE55E ON message');
        $this->addSql('ALTER TABLE message ADD conversation_id INT NOT NULL, DROP provider_service_id, CHANGE message content LONGTEXT NOT NULL, CHANGE date created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
        $this->addSql('ALTER TABLE provider_planning ADD description LONGTEXT NOT NULL, DROP work_start, DROP work_end');
        $this->addSql('ALTER TABLE provider_planning ADD CONSTRAINT FK_9CDBD11FA53A8AA FOREIGN KEY (provider_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE provider_service ADD CONSTRAINT FK_11C53875A53A8AA FOREIGN KEY (provider_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD town VARCHAR(255) NOT NULL, ADD district VARCHAR(255) DEFAULT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD is_top TINYINT(1) NOT NULL, CHANGE address company_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA53A8AA');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9A53A8AA');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('DROP INDEX IDX_B6BD307F9AC0396 ON message');
        $this->addSql('ALTER TABLE message ADD provider_service_id INT DEFAULT NULL, DROP conversation_id, CHANGE content message LONGTEXT NOT NULL, CHANGE created_at date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FC8EAE55E FOREIGN KEY (provider_service_id) REFERENCES provider_service (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FC8EAE55E ON message (provider_service_id)');
        $this->addSql('ALTER TABLE provider_planning DROP FOREIGN KEY FK_9CDBD11FA53A8AA');
        $this->addSql('ALTER TABLE provider_planning ADD work_start DATETIME NOT NULL, ADD work_end DATETIME NOT NULL, DROP description');
        $this->addSql('ALTER TABLE provider_service DROP FOREIGN KEY FK_11C53875A53A8AA');
        $this->addSql('ALTER TABLE user ADD address VARCHAR(255) DEFAULT NULL, DROP company_name, DROP town, DROP district, DROP image, DROP is_top');
    }
}
