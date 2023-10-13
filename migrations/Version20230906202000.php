<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230906202000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create HeightForAge0To23Months Entity';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE height_for_age_0_to_23_months (id INT AUTO_INCREMENT NOT NULL, sex TINYINT(1) NOT NULL, month FLOAT NOT NULL, L DOUBLE NOT NULL, M DOUBLE NOT NULL, S DOUBLE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql("INSERT INTO height_for_age_0_to_23_months (sex, month, L, M, S) VALUES
                        ('1', '0', '1', '49.8842', '0.03795'),
                        ('1', '1', '1', '54.7244', '0.03557'),
                        ('1', '2', '1', '58.4249', '0.03424'),
                        ('1', '3', '1', '61.4292', '0.03328'),
                        ('1', '4', '1', '63.886', '0.03257'),
                        ('1', '5', '1', '65.9026', '0.03204'),
                        ('1', '6', '1', '67.6236', '0.03165'),
                        ('1', '7', '1', '69.1645', '0.03139'),
                        ('1', '8', '1', '70.5994', '0.03124'),
                        ('1', '9', '1', '71.9687', '0.03117'),
                        ('1', '10', '1', '73.2812', '0.03118'),
                        ('1', '11', '1', '74.5388', '0.03125'),
                        ('1', '12', '1', '75.7488', '0.03137'),
                        ('1', '13', '1', '76.9186', '0.03154'),
                        ('1', '14', '1', '78.0497', '0.03174'),
                        ('1', '15', '1', '79.1458', '0.03197'),
                        ('1', '16', '1', '80.2113', '0.03222'),
                        ('1', '17', '1', '81.2487', '0.0325'),
                        ('1', '18', '1', '82.2587', '0.03279'),
                        ('1', '19', '1', '83.2418', '0.0331'),
                        ('1', '20', '1', '84.1996', '0.03342'),
                        ('1', '21', '1', '85.1348', '0.03376'),
                        ('1', '22', '1', '86.0477', '0.0341'),
                        ('1', '23', '1', '86.941', '0.03445'),
                        ('1', '24', '1', '87.8161', '0.03479'),
                        ('2', '0', '1', '49.1477', '0.0379'),
                        ('2', '1', '1', '53.6872', '0.0364'),
                        ('2', '2', '1', '57.0673', '0.03568'),
                        ('2', '3', '1', '59.8029', '0.0352'),
                        ('2', '4', '1', '62.0899', '0.03486'),
                        ('2', '5', '1', '64.0301', '0.03463'),
                        ('2', '6', '1', '65.7311', '0.03448'),
                        ('2', '7', '1', '67.2873', '0.03441'),
                        ('2', '8', '1', '68.7498', '0.0344'),
                        ('2', '9', '1', '70.1435', '0.03444'),
                        ('2', '10', '1', '71.4818', '0.03452'),
                        ('2', '11', '1', '72.771', '0.03464'),
                        ('2', '12', '1', '74.015', '0.03479'),
                        ('2', '13', '1', '75.2176', '0.03496'),
                        ('2', '14', '1', '76.3817', '0.03514'),
                        ('2', '15', '1', '77.5099', '0.03534'),
                        ('2', '16', '1', '78.6055', '0.03555'),
                        ('2', '17', '1', '79.671', '0.03576'),
                        ('2', '18', '1', '80.7079', '0.03598'),
                        ('2', '19', '1', '81.7182', '0.0362'),
                        ('2', '20', '1', '82.7036', '0.03643'),
                        ('2', '21', '1', '83.6654', '0.03666'),
                        ('2', '22', '1', '84.604', '0.03688'),
                        ('2', '23', '1', '85.5202', '0.03711'),
                        ('2', '24', '1', '86.4153', '0.03734')"
        );
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE height_for_age_0_to_23_months');
    }
}