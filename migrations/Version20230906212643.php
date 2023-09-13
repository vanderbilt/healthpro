<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230906212643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create HeadCircumferenceForAge0To36Months Entity';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE head_circumference_for_age_0_to_36months (id INT AUTO_INCREMENT NOT NULL, sex TINYINT(1) NOT NULL, month FLOAT NOT NULL, L DOUBLE NOT NULL, M DOUBLE NOT NULL, S DOUBLE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql("INSERT INTO head_circumference_for_age_0_to_36months (sex, month, L, M, S) VALUES
                        ('1', '0', '4.42782504', '35.8136684', '0.05217254'),
                        ('1', '0.5', '4.31092746', '37.1936105', '0.04725915'),
                        ('1', '1.5', '3.8695768', '39.2074293', '0.0409479'),
                        ('1', '2.5', '3.30559304', '40.652332', '0.03702772'),
                        ('1', '3.5', '2.7205903', '41.7651696', '0.03436425'),
                        ('1', '4.5', '2.16804824', '42.6611615', '0.03246218'),
                        ('1', '5.5', '1.67546569', '43.4048873', '0.0310647'),
                        ('1', '6.5', '1.25516032', '44.0360992', '0.03002267'),
                        ('1', '7.5', '0.91054114', '44.5809691', '0.02924217'),
                        ('1', '8.5', '0.63951047', '45.0576122', '0.02866045'),
                        ('1', '9.5', '0.43697886', '45.4790756', '0.0282336'),
                        ('1', '10.5', '0.29627586', '45.8550571', '0.02792976'),
                        ('1', '11.5', '0.21010725', '46.1929543', '0.02772518'),
                        ('1', '12.5', '0.17114702', '46.4985344', '0.02760169'),
                        ('1', '13.5', '0.17239389', '46.7763768', '0.02754515'),
                        ('1', '14.5', '0.20737154', '47.030176', '0.02754438'),
                        ('1', '15.5', '0.27022613', '47.2629533', '0.02759042'),
                        ('1', '16.5', '0.35575727', '47.4772099', '0.02767598'),
                        ('1', '17.5', '0.45940763', '47.6750383', '0.02779512'),
                        ('1', '18.5', '0.57722762', '47.8582061', '0.0279429'),
                        ('1', '19.5', '0.70582678', '48.0282187', '0.02811524'),
                        ('1', '20.5', '0.84231906', '48.1863686', '0.02830871'),
                        ('1', '21.5', '0.98426683', '48.3337732', '0.02852041'),
                        ('1', '22.5', '1.1296267', '48.4714043', '0.0287479'),
                        ('1', '23.5', '1.27669122', '48.6001122', '0.02898909'),
                        ('1', '24.5', '1.42408485', '48.7206462', '0.02924221'),
                        ('1', '25.5', '1.57062129', '48.8336663', '0.02950572'),
                        ('1', '26.5', '1.715394', '48.9397609', '0.02977832'),
                        ('1', '27.5', '1.85765298', '49.0394538', '0.03005887'),
                        ('1', '28.5', '1.99681056', '49.1332143', '0.03034638'),
                        ('1', '29.5', '2.13241135', '49.2214641', '0.03064001'),
                        ('1', '30.5', '2.26411101', '49.3045835', '0.03093899'),
                        ('1', '31.5', '2.39165805', '49.3829166', '0.03124269'),
                        ('1', '32.5', '2.51487822', '49.4567757', '0.03155054'),
                        ('1', '33.5', '2.63366123', '49.526445', '0.03186203'),
                        ('1', '34.5', '2.74794945', '49.5921839', '0.03217672'),
                        ('1', '35.5', '2.85772838', '49.6542295', '0.03249423'),
                        ('1', '36', '2.9109321', '49.6839361', '0.03265393'),
                        ('2', '0', '-1.2987497', '34.7115617', '0.04690511'),
                        ('2', '0.5', '-1.4402715', '36.0345388', '0.0429996'),
                        ('2', '1.5', '-1.5810163', '37.9767199', '0.03806786'),
                        ('2', '2.5', '-1.5931364', '39.3801263', '0.03507961'),
                        ('2', '3.5', '-1.5214924', '40.4677373', '0.03309644'),
                        ('2', '4.5', '-1.3945659', '41.3484101', '0.03170963'),
                        ('2', '5.5', '-1.2317134', '42.0833507', '0.03070904'),
                        ('2', '6.5', '-1.0465826', '42.710336', '0.0299743'),
                        ('2', '7.5', '-0.8489327', '43.2542888', '0.02943099'),
                        ('2', '8.5', '-0.6457791', '43.7324965', '0.02903038'),
                        ('2', '9.5', '-0.4421654', '44.1574284', '0.02873911'),
                        ('2', '10.5', '-0.2416321', '44.5383679', '0.02853354'),
                        ('2', '11.5', '-0.0466738', '44.8824056', '0.02839638'),
                        ('2', '12.5', '0.14103109', '45.1950765', '0.02831472'),
                        ('2', '13.5', '0.32040317', '45.4807815', '0.02827868'),
                        ('2', '14.5', '0.49080713', '45.7430753', '0.02828059'),
                        ('2', '15.5', '0.65193505', '45.984869', '0.02831436'),
                        ('2', '16.5', '0.80371809', '46.2085756', '0.02837516'),
                        ('2', '17.5', '0.94625968', '46.4162164', '0.02845903'),
                        ('2', '18.5', '1.07978498', '46.6095008', '0.02856276'),
                        ('2', '19.5', '1.20460269', '46.7898872', '0.02868367'),
                        ('2', '20.5', '1.32107629', '46.9586288', '0.02881953'),
                        ('2', '21.5', '1.42960258', '47.1168104', '0.02896846'),
                        ('2', '22.5', '1.53059568', '47.2653768', '0.02912888'),
                        ('2', '23.5', '1.62447526', '47.4051559', '0.02929943'),
                        ('2', '24.5', '1.71165803', '47.5368765', '0.02947894'),
                        ('2', '25.5', '1.79255162', '47.661184', '0.02966641'),
                        ('2', '26.5', '1.86755038', '47.7786519', '0.02986096'),
                        ('2', '27.5', '1.93703258', '47.8897923', '0.03006184'),
                        ('2', '28.5', '2.00135867', '47.9950642', '0.03026838'),
                        ('2', '29.5', '2.0608703', '48.0948805', '0.03047999'),
                        ('2', '30.5', '2.11588998', '48.1896137', '0.03069615'),
                        ('2', '31.5', '2.16672113', '48.2796011', '0.03091641'),
                        ('2', '32.5', '2.21364844', '48.3651492', '0.03114037'),
                        ('2', '33.5', '2.25694322', '48.446537', '0.03136765'),
                        ('2', '34.5', '2.29684402', '48.5240189', '0.03159794'),
                        ('2', '35.5', '2.33358943', '48.5978283', '0.03183094'),
                        ('2', '36', '2.3508472', '48.6334233', '0.03194838')"
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE head_circumference_for_age_0_to_36months');
    }
}
