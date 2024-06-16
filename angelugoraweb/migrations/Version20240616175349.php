<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240616175349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedido CHANGE total_precio total_precio NUMERIC(10, 2) NOT NULL, CHANGE usuarioId usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE pedido ADD CONSTRAINT FK_C4EC16CEDB38439E FOREIGN KEY (usuario_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_C4EC16CEDB38439E ON pedido (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedido DROP FOREIGN KEY FK_C4EC16CEDB38439E');
        $this->addSql('DROP INDEX IDX_C4EC16CEDB38439E ON pedido');
        $this->addSql('ALTER TABLE pedido CHANGE total_precio total_precio NUMERIC(10, 2) DEFAULT NULL, CHANGE usuario_id usuarioId INT NOT NULL');
    }
}
