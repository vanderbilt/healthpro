<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230908190845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create WeightForAge0To23Months entity';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE weight_for_age_0_to_23_months (id INT AUTO_INCREMENT NOT NULL, sex TINYINT(1) NOT NULL, month FLOAT NOT NULL, L DOUBLE NOT NULL, M DOUBLE NOT NULL, S DOUBLE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql("INSERT INTO weight_for_age_0_to_23_months (sex, month, L, M, S) VALUES
                        ('1', '0', '0.3487', '3.3464', '0.14602'),
                        ('1', '1', '0.2297', '4.4709', '0.13395'),
                        ('1', '2', '0.197', '5.5675', '0.12385'),
                        ('1', '3', '0.1738', '6.3762', '0.11727'),
                        ('1', '4', '0.1553', '7.0023', '0.11316'),
                        ('1', '5', '0.1395', '7.5105', '0.1108'),
                        ('1', '6', '0.1257', '7.934', '0.10958'),
                        ('1', '7', '0.1134', '8.297', '0.10902'),
                        ('1', '8', '0.1021', '8.6151', '0.10882'),
                        ('1', '9', '0.0917', '8.9014', '0.10881'),
                        ('1', '10', '0.082', '9.1649', '0.10891'),
                        ('1', '11', '0.073', '9.4122', '0.10906'),
                        ('1', '12', '0.0644', '9.6479', '0.10925'),
                        ('1', '13', '0.0563', '9.8749', '0.10949'),
                        ('1', '14', '0.0487', '10.0953', '0.10976'),
                        ('1', '15', '0.0413', '10.3108', '0.11007'),
                        ('1', '16', '0.0343', '10.5228', '0.11041'),
                        ('1', '17', '0.0275', '10.7319', '0.11079'),
                        ('1', '18', '0.0211', '10.9385', '0.11119'),
                        ('1', '19', '0.0148', '11.143', '0.11164'),
                        ('1', '20', '0.0087', '11.3462', '0.11211'),
                        ('1', '21', '0.0029', '11.5486', '0.11261'),
                        ('1', '22', '-0.0028', '11.7504', '0.11314'),
                        ('1', '23', '-0.0083', '11.9514', '0.11369'),
                        ('1', '24', '-0.0137', '12.1515', '0.11426'),
                        ('2', '0', '0.3809', '3.2322', '0.14171'),
                        ('2', '1', '0.1714', '4.1873', '0.13724'),
                        ('2', '2', '0.0962', '5.1282', '0.13'),
                        ('2', '3', '0.0402', '5.8458', '0.12619'),
                        ('2', '4', '-0.005', '6.4237', '0.12402'),
                        ('2', '5', '-0.043', '6.8985', '0.12274'),
                        ('2', '6', '-0.0756', '7.297', '0.12204'),
                        ('2', '7', '-0.1039', '7.6422', '0.12178'),
                        ('2', '8', '-0.1288', '7.9487', '0.12181'),
                        ('2', '9', '-0.1507', '8.2254', '0.12199'),
                        ('2', '10', '-0.17', '8.48', '0.12223'),
                        ('2', '11', '-0.1872', '8.7192', '0.12247'),
                        ('2', '12', '-0.2024', '8.9481', '0.12268'),
                        ('2', '13', '-0.2158', '9.1699', '0.12283'),
                        ('2', '14', '-0.2278', '9.387', '0.12294'),
                        ('2', '15', '-0.2384', '9.6008', '0.12299'),
                        ('2', '16', '-0.2478', '9.8124', '0.12303'),
                        ('2', '17', '-0.2562', '10.0226', '0.12306'),
                        ('2', '18', '-0.2637', '10.2315', '0.12309'),
                        ('2', '19', '-0.2703', '10.4393', '0.12315'),
                        ('2', '20', '-0.2762', '10.6464', '0.12323'),
                        ('2', '21', '-0.2815', '10.8534', '0.12335'),
                        ('2', '22', '-0.2862', '11.0608', '0.1235'),
                        ('2', '23', '-0.2903', '11.2688', '0.12369'),
                        ('2', '24', '-0.2941', '11.4775', '0.1239')"
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE weight_for_age_0_to_23_months');
    }
}
