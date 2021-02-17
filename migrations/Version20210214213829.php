<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210214213829 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonne (id INT AUTO_INCREMENT NOT NULL, compte_id INT DEFAULT NULL, numero INT NOT NULL, INDEX IDX_76328BF0F2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE action (id INT AUTO_INCREMENT NOT NULL, abonne_id INT DEFAULT NULL, date DATE NOT NULL, heure TIME NOT NULL, duree_volume_reel_en_heure TIME DEFAULT NULL, duree_volume_facture_en_heure TIME DEFAULT NULL, duree_volume_reel_data INT DEFAULT NULL, duree_volume_facture_data INT DEFAULT NULL, typeAction_id INT DEFAULT NULL, INDEX IDX_47CC8C92FCFC100A (typeAction_id), INDEX IDX_47CC8C92C325A696 (abonne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte_facture (id INT AUTO_INCREMENT NOT NULL, numero INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, numero INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture_abonne (id INT AUTO_INCREMENT NOT NULL, abonne_id INT DEFAULT NULL, facture_id INT DEFAULT NULL, INDEX IDX_23A59957C325A696 (abonne_id), INDEX IDX_23A599577F2DEE08 (facture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_action (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE abonne ADD CONSTRAINT FK_76328BF0F2C56620 FOREIGN KEY (compte_id) REFERENCES compte_facture (id)');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92FCFC100A FOREIGN KEY (typeAction_id) REFERENCES type_action (id)');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92C325A696 FOREIGN KEY (abonne_id) REFERENCES abonne (id)');
        $this->addSql('ALTER TABLE facture_abonne ADD CONSTRAINT FK_23A59957C325A696 FOREIGN KEY (abonne_id) REFERENCES abonne (id)');
        $this->addSql('ALTER TABLE facture_abonne ADD CONSTRAINT FK_23A599577F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C92C325A696');
        $this->addSql('ALTER TABLE facture_abonne DROP FOREIGN KEY FK_23A59957C325A696');
        $this->addSql('ALTER TABLE abonne DROP FOREIGN KEY FK_76328BF0F2C56620');
        $this->addSql('ALTER TABLE facture_abonne DROP FOREIGN KEY FK_23A599577F2DEE08');
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C92FCFC100A');
        $this->addSql('DROP TABLE abonne');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP TABLE compte_facture');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE facture_abonne');
        $this->addSql('DROP TABLE type_action');
    }
}
