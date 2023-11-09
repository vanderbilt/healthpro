<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230908161215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create WeightForAge24MonthsAndUp Entity';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE weight_for_age_24_months_and_up (id INT AUTO_INCREMENT NOT NULL, sex TINYINT(1) NOT NULL, month FLOAT NOT NULL, L DOUBLE NOT NULL, M DOUBLE NOT NULL, S DOUBLE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql("INSERT INTO weight_for_age_24_months_and_up (sex, month, L, M, S) VALUES
                        ('1', '24', '-0.20615', '12.67076', '0.108126'),
                        ('1', '24.5', '-0.2165', '12.74154', '0.108166'),
                        ('1', '25.5', '-0.23979', '12.88102', '0.108275'),
                        ('1', '26.5', '-0.26632', '13.01842', '0.108421'),
                        ('1', '27.5', '-0.29575', '13.1545', '0.108605'),
                        ('1', '28.5', '-0.32773', '13.2899', '0.108826'),
                        ('1', '29.5', '-0.36182', '13.42519', '0.109083'),
                        ('1', '30.5', '-0.39757', '13.56088', '0.109378'),
                        ('1', '31.5', '-0.43452', '13.69738', '0.109708'),
                        ('1', '32.5', '-0.47219', '13.83505', '0.110073'),
                        ('1', '33.5', '-0.51012', '13.97418', '0.110473'),
                        ('1', '34.5', '-0.54789', '14.11503', '0.110907'),
                        ('1', '35.5', '-0.58507', '14.2578', '0.111375'),
                        ('1', '36.5', '-0.62132', '14.40263', '0.111875'),
                        ('1', '37.5', '-0.6563', '14.54965', '0.112406'),
                        ('1', '38.5', '-0.68974', '14.69893', '0.112967'),
                        ('1', '39.5', '-0.72141', '14.85054', '0.113558'),
                        ('1', '40.5', '-0.75118', '15.00449', '0.114177'),
                        ('1', '41.5', '-0.7789', '15.16078', '0.114822'),
                        ('1', '42.5', '-0.80452', '15.3194', '0.115493'),
                        ('1', '43.5', '-0.828', '15.4803', '0.116188'),
                        ('1', '44.5', '-0.84938', '15.64343', '0.116904'),
                        ('1', '45.5', '-0.8687', '15.80873', '0.117641'),
                        ('1', '46.5', '-0.88603', '15.9761', '0.118397'),
                        ('1', '47.5', '-0.90151', '16.14548', '0.119169'),
                        ('1', '48.5', '-0.91524', '16.31677', '0.119955'),
                        ('1', '49.5', '-0.92738', '16.48986', '0.120755'),
                        ('1', '50.5', '-0.93807', '16.66468', '0.121565'),
                        ('1', '51.5', '-0.94748', '16.8411', '0.122385'),
                        ('1', '52.5', '-0.95577', '17.01904', '0.123212'),
                        ('1', '53.5', '-0.9631', '17.19839', '0.124044'),
                        ('1', '54.5', '-0.96963', '17.37906', '0.124879'),
                        ('1', '55.5', '-0.97553', '17.56096', '0.125716'),
                        ('1', '56.5', '-0.98094', '17.744', '0.126554'),
                        ('1', '57.5', '-0.98601', '17.92809', '0.12739'),
                        ('1', '58.5', '-0.99087', '18.11316', '0.128224'),
                        ('1', '59.5', '-0.99564', '18.29912', '0.129054'),
                        ('1', '60.5', '-1.00045', '18.48592', '0.129879'),
                        ('1', '61.5', '-1.0054', '18.6735', '0.130698'),
                        ('1', '62.5', '-1.01058', '18.8618', '0.13151'),
                        ('1', '63.5', '-1.01606', '19.05077', '0.132315'),
                        ('1', '64.5', '-1.02193', '19.24037', '0.133111'),
                        ('1', '65.5', '-1.02824', '19.43058', '0.133898'),
                        ('1', '66.5', '-1.03504', '19.62136', '0.134676'),
                        ('1', '67.5', '-1.04237', '19.8127', '0.135444'),
                        ('1', '68.5', '-1.05025', '20.00459', '0.136203'),
                        ('1', '69.5', '-1.05871', '20.19703', '0.136952'),
                        ('1', '70.5', '-1.06773', '20.39002', '0.137691'),
                        ('1', '71.5', '-1.07732', '20.58357', '0.138422'),
                        ('1', '72.5', '-1.08747', '20.7777', '0.139143'),
                        ('1', '73.5', '-1.09815', '20.97243', '0.139855'),
                        ('1', '74.5', '-1.10933', '21.16779', '0.14056'),
                        ('1', '75.5', '-1.12097', '21.36383', '0.141256'),
                        ('1', '76.5', '-1.13302', '21.56058', '0.141947'),
                        ('1', '77.5', '-1.14543', '21.75811', '0.142631'),
                        ('1', '78.5', '-1.15813', '21.95645', '0.14331'),
                        ('1', '79.5', '-1.17106', '22.15567', '0.143985'),
                        ('1', '80.5', '-1.18414', '22.35584', '0.144657'),
                        ('1', '81.5', '-1.19731', '22.55702', '0.145327'),
                        ('1', '82.5', '-1.21048', '22.7593', '0.145996'),
                        ('1', '83.5', '-1.22357', '22.96273', '0.146666'),
                        ('1', '84.5', '-1.2365', '23.16742', '0.147337'),
                        ('1', '85.5', '-1.24919', '23.37343', '0.148012'),
                        ('1', '86.5', '-1.26156', '23.58086', '0.14869'),
                        ('1', '87.5', '-1.27352', '23.78979', '0.149374'),
                        ('1', '88.5', '-1.28501', '24.00031', '0.150065'),
                        ('1', '89.5', '-1.29595', '24.21251', '0.150764'),
                        ('1', '90.5', '-1.30627', '24.42648', '0.151472'),
                        ('1', '91.5', '-1.3159', '24.64231', '0.15219'),
                        ('1', '92.5', '-1.32478', '24.8601', '0.15292'),
                        ('1', '93.5', '-1.33286', '25.07992', '0.153663'),
                        ('1', '94.5', '-1.34008', '25.30189', '0.154419'),
                        ('1', '95.5', '-1.34641', '25.52607', '0.155189'),
                        ('1', '96.5', '-1.35181', '25.75257', '0.155974'),
                        ('1', '97.5', '-1.35625', '25.98146', '0.156775'),
                        ('1', '98.5', '-1.35971', '26.21284', '0.157592'),
                        ('1', '99.5', '-1.36217', '26.44679', '0.158425'),
                        ('1', '100.5', '-1.36361', '26.68339', '0.159275'),
                        ('1', '101.5', '-1.36404', '26.92273', '0.160142'),
                        ('1', '102.5', '-1.36346', '27.16489', '0.161026'),
                        ('1', '103.5', '-1.36187', '27.40995', '0.161926'),
                        ('1', '104.5', '-1.35928', '27.65797', '0.162842'),
                        ('1', '105.5', '-1.35572', '27.90904', '0.163775'),
                        ('1', '106.5', '-1.3512', '28.16324', '0.164722'),
                        ('1', '107.5', '-1.34575', '28.42064', '0.165684'),
                        ('1', '108.5', '-1.33941', '28.6813', '0.166659'),
                        ('1', '109.5', '-1.33219', '28.9453', '0.167647'),
                        ('1', '110.5', '-1.32414', '29.21271', '0.168646'),
                        ('1', '111.5', '-1.31529', '29.48359', '0.169655'),
                        ('1', '112.5', '-1.30569', '29.758', '0.170673'),
                        ('1', '113.5', '-1.29537', '30.03602', '0.171698'),
                        ('1', '114.5', '-1.28437', '30.3177', '0.172729'),
                        ('1', '115.5', '-1.27275', '30.60311', '0.173763'),
                        ('1', '116.5', '-1.26054', '30.8923', '0.174799'),
                        ('1', '117.5', '-1.24778', '31.18533', '0.175836'),
                        ('1', '118.5', '-1.23453', '31.48225', '0.176871'),
                        ('1', '119.5', '-1.22082', '31.78312', '0.177903'),
                        ('1', '120.5', '-1.20669', '32.08799', '0.178929'),
                        ('1', '121.5', '-1.19219', '32.3969', '0.179947'),
                        ('1', '122.5', '-1.17736', '32.70991', '0.180955'),
                        ('1', '123.5', '-1.16224', '33.02704', '0.181951'),
                        ('1', '124.5', '-1.14688', '33.34835', '0.182934'),
                        ('1', '125.5', '-1.1313', '33.67387', '0.183899'),
                        ('1', '126.5', '-1.11554', '34.00363', '0.184847'),
                        ('1', '127.5', '-1.09965', '34.33766', '0.185774'),
                        ('1', '128.5', '-1.08365', '34.67599', '0.186678'),
                        ('1', '129.5', '-1.06759', '35.01864', '0.187558'),
                        ('1', '130.5', '-1.05148', '35.36562', '0.188411'),
                        ('1', '131.5', '-1.03537', '35.71695', '0.189236'),
                        ('1', '132.5', '-1.01928', '36.07263', '0.19003'),
                        ('1', '133.5', '-1.00324', '36.43266', '0.190791'),
                        ('1', '134.5', '-0.98727', '36.79704', '0.191518'),
                        ('1', '135.5', '-0.97141', '37.16577', '0.19221'),
                        ('1', '136.5', '-0.95567', '37.53881', '0.192864'),
                        ('1', '137.5', '-0.94008', '37.91616', '0.193479'),
                        ('1', '138.5', '-0.92467', '38.29777', '0.194053'),
                        ('1', '139.5', '-0.90945', '38.68361', '0.194586'),
                        ('1', '140.5', '-0.89445', '39.07364', '0.195077'),
                        ('1', '141.5', '-0.87968', '39.46781', '0.195523'),
                        ('1', '142.5', '-0.86516', '39.86604', '0.195925'),
                        ('1', '143.5', '-0.85092', '40.26828', '0.196281'),
                        ('1', '144.5', '-0.83696', '40.67444', '0.196592'),
                        ('1', '145.5', '-0.82332', '41.08443', '0.196855'),
                        ('1', '146.5', '-0.80999', '41.49817', '0.197072'),
                        ('1', '147.5', '-0.79701', '41.91555', '0.197241'),
                        ('1', '148.5', '-0.78439', '42.33644', '0.197363'),
                        ('1', '149.5', '-0.77214', '42.76073', '0.197437'),
                        ('1', '150.5', '-0.76027', '43.18828', '0.197464'),
                        ('1', '151.5', '-0.74882', '43.61896', '0.197445'),
                        ('1', '152.5', '-0.73778', '44.05259', '0.197378'),
                        ('1', '153.5', '-0.72718', '44.48903', '0.197266'),
                        ('1', '154.5', '-0.71704', '44.92809', '0.197109'),
                        ('1', '155.5', '-0.70736', '45.3696', '0.196907'),
                        ('1', '156.5', '-0.69817', '45.81336', '0.196662'),
                        ('1', '157.5', '-0.68948', '46.25917', '0.196375'),
                        ('1', '158.5', '-0.6813', '46.70681', '0.196046'),
                        ('1', '159.5', '-0.67367', '47.15606', '0.195677'),
                        ('1', '160.5', '-0.66659', '47.60669', '0.195269'),
                        ('1', '161.5', '-0.66007', '48.05847', '0.194825'),
                        ('1', '162.5', '-0.65414', '48.51113', '0.194344'),
                        ('1', '163.5', '-0.64882', '48.96443', '0.19383'),
                        ('1', '164.5', '-0.64412', '49.4181', '0.193283'),
                        ('1', '165.5', '-0.64006', '49.87187', '0.192706'),
                        ('1', '166.5', '-0.63665', '50.32546', '0.1921'),
                        ('1', '167.5', '-0.63392', '50.77859', '0.191467'),
                        ('1', '168.5', '-0.63188', '51.23096', '0.190808'),
                        ('1', '169.5', '-0.63054', '51.68229', '0.190127'),
                        ('1', '170.5', '-0.62992', '52.13226', '0.189425'),
                        ('1', '171.5', '-0.63004', '52.58059', '0.188703'),
                        ('1', '172.5', '-0.63091', '53.02696', '0.187964'),
                        ('1', '173.5', '-0.63253', '53.47107', '0.187209'),
                        ('1', '174.5', '-0.63492', '53.91261', '0.186442'),
                        ('1', '175.5', '-0.63808', '54.35128', '0.185663'),
                        ('1', '176.5', '-0.64203', '54.78677', '0.184874'),
                        ('1', '177.5', '-0.64676', '55.21878', '0.184079'),
                        ('1', '178.5', '-0.65226', '55.64701', '0.183277'),
                        ('1', '179.5', '-0.65855', '56.07116', '0.182472'),
                        ('1', '180.5', '-0.66561', '56.49096', '0.181666'),
                        ('1', '181.5', '-0.67343', '56.90611', '0.180859'),
                        ('1', '182.5', '-0.68199', '57.31634', '0.180054'),
                        ('1', '183.5', '-0.69127', '57.72139', '0.179253'),
                        ('1', '184.5', '-0.70126', '58.121', '0.178457'),
                        ('1', '185.5', '-0.71192', '58.51492', '0.177668'),
                        ('1', '186.5', '-0.72322', '58.90293', '0.176887'),
                        ('1', '187.5', '-0.73512', '59.2848', '0.176116'),
                        ('1', '188.5', '-0.74758', '59.66033', '0.175357'),
                        ('1', '189.5', '-0.76055', '60.02932', '0.17461'),
                        ('1', '190.5', '-0.77398', '60.39159', '0.173877'),
                        ('1', '191.5', '-0.78782', '60.74699', '0.17316'),
                        ('1', '192.5', '-0.80199', '61.09537', '0.172459'),
                        ('1', '193.5', '-0.81645', '61.4366', '0.171776'),
                        ('1', '194.5', '-0.83111', '61.77057', '0.171111'),
                        ('1', '195.5', '-0.84591', '62.09719', '0.170466'),
                        ('1', '196.5', '-0.86079', '62.41639', '0.169841'),
                        ('1', '197.5', '-0.87565', '62.72809', '0.169237'),
                        ('1', '198.5', '-0.89044', '63.03228', '0.168655'),
                        ('1', '199.5', '-0.90506', '63.32892', '0.168095'),
                        ('1', '200.5', '-0.91946', '63.61802', '0.167558'),
                        ('1', '201.5', '-0.93354', '63.89959', '0.167044'),
                        ('1', '202.5', '-0.94725', '64.17367', '0.166553'),
                        ('1', '203.5', '-0.96051', '64.44032', '0.166085'),
                        ('1', '204.5', '-0.97324', '64.69961', '0.16564'),
                        ('1', '205.5', '-0.9854', '64.95165', '0.165218'),
                        ('1', '206.5', '-0.9969', '65.19653', '0.164819'),
                        ('1', '207.5', '-1.00771', '65.4344', '0.164442'),
                        ('1', '208.5', '-1.01776', '65.6654', '0.164087'),
                        ('1', '209.5', '-1.027', '65.8897', '0.163753'),
                        ('1', '210.5', '-1.0354', '66.10749', '0.163439'),
                        ('1', '211.5', '-1.04292', '66.31897', '0.163144'),
                        ('1', '212.5', '-1.04951', '66.52437', '0.162867'),
                        ('1', '213.5', '-1.05516', '66.7239', '0.162608'),
                        ('1', '214.5', '-1.05984', '66.91784', '0.162365'),
                        ('1', '215.5', '-1.06353', '67.10642', '0.162137'),
                        ('1', '216.5', '-1.06622', '67.28993', '0.161923'),
                        ('1', '217.5', '-1.06791', '67.46863', '0.161721'),
                        ('1', '218.5', '-1.06859', '67.64281', '0.161532'),
                        ('1', '219.5', '-1.06826', '67.81277', '0.161352'),
                        ('1', '220.5', '-1.06693', '67.97877', '0.161183'),
                        ('1', '221.5', '-1.06462', '68.14111', '0.161022'),
                        ('1', '222.5', '-1.06134', '68.30005', '0.16087'),
                        ('1', '223.5', '-1.05712', '68.45585', '0.160726'),
                        ('1', '224.5', '-1.05199', '68.60872', '0.16059'),
                        ('1', '225.5', '-1.04599', '68.75889', '0.160462'),
                        ('1', '226.5', '-1.03917', '68.90653', '0.160343'),
                        ('1', '227.5', '-1.03158', '69.05176', '0.160234'),
                        ('1', '228.5', '-1.02329', '69.19467', '0.160138'),
                        ('1', '229.5', '-1.01439', '69.33527', '0.160056'),
                        ('1', '230.5', '-1.00495', '69.47351', '0.159992'),
                        ('1', '231.5', '-0.9951', '69.60926', '0.15995'),
                        ('1', '232.5', '-0.98496', '69.74228', '0.159934'),
                        ('1', '233.5', '-0.97466', '69.87224', '0.159951'),
                        ('1', '234.5', '-0.96438', '69.99869', '0.160007'),
                        ('1', '235.5', '-0.95427', '70.12104', '0.160112'),
                        ('1', '236.5', '-0.94455', '70.23857', '0.160274'),
                        ('1', '237.5', '-0.93541', '70.3504', '0.160505'),
                        ('1', '238.5', '-0.92706', '70.45546', '0.160819'),
                        ('1', '239.5', '-0.91972', '70.55252', '0.16123'),
                        ('1', '240', '-0.91649', '70.59761', '0.161477'),
                        ('2', '24', '-0.73534', '12.05504', '0.107399'),
                        ('2', '24.5', '-0.75221', '12.13456', '0.10774'),
                        ('2', '25.5', '-0.78423', '12.29102', '0.108477'),
                        ('2', '26.5', '-0.8141', '12.44469', '0.109281'),
                        ('2', '27.5', '-0.84194', '12.59622', '0.110144'),
                        ('2', '28.5', '-0.86789', '12.74621', '0.111061'),
                        ('2', '29.5', '-0.8921', '12.89517', '0.112023'),
                        ('2', '30.5', '-0.91472', '13.04357', '0.113023'),
                        ('2', '31.5', '-0.93588', '13.19181', '0.114056'),
                        ('2', '32.5', '-0.95572', '13.34023', '0.115115'),
                        ('2', '33.5', '-0.97438', '13.48913', '0.116193'),
                        ('2', '34.5', '-0.99198', '13.63877', '0.117286'),
                        ('2', '35.5', '-1.00864', '13.78937', '0.118387'),
                        ('2', '36.5', '-1.02447', '13.94108', '0.119492'),
                        ('2', '37.5', '-1.03957', '14.09407', '0.120596'),
                        ('2', '38.5', '-1.05404', '14.24844', '0.121695'),
                        ('2', '39.5', '-1.06795', '14.40429', '0.122785'),
                        ('2', '40.5', '-1.08137', '14.56168', '0.123863'),
                        ('2', '41.5', '-1.09438', '14.72064', '0.124927'),
                        ('2', '42.5', '-1.10702', '14.88121', '0.125973'),
                        ('2', '43.5', '-1.11934', '15.04341', '0.127'),
                        ('2', '44.5', '-1.13137', '15.20721', '0.128006'),
                        ('2', '45.5', '-1.14314', '15.37263', '0.12899'),
                        ('2', '46.5', '-1.15466', '15.53962', '0.129951'),
                        ('2', '47.5', '-1.16596', '15.70817', '0.130889'),
                        ('2', '48.5', '-1.17703', '15.87824', '0.131802'),
                        ('2', '49.5', '-1.18787', '16.04978', '0.132692'),
                        ('2', '50.5', '-1.19848', '16.22277', '0.133559'),
                        ('2', '51.5', '-1.20885', '16.39715', '0.134403'),
                        ('2', '52.5', '-1.21897', '16.57289', '0.135226'),
                        ('2', '53.5', '-1.2288', '16.74994', '0.136028'),
                        ('2', '54.5', '-1.23833', '16.92827', '0.136811'),
                        ('2', '55.5', '-1.24754', '17.10783', '0.137576'),
                        ('2', '56.5', '-1.25639', '17.28859', '0.138324'),
                        ('2', '57.5', '-1.26486', '17.47052', '0.139058'),
                        ('2', '58.5', '-1.27293', '17.65361', '0.139779'),
                        ('2', '59.5', '-1.28055', '17.83782', '0.14049'),
                        ('2', '60.5', '-1.28769', '18.02314', '0.141191'),
                        ('2', '61.5', '-1.29433', '18.20956', '0.141885'),
                        ('2', '62.5', '-1.30044', '18.39709', '0.142574'),
                        ('2', '63.5', '-1.30599', '18.58571', '0.14326'),
                        ('2', '64.5', '-1.31095', '18.77545', '0.143944'),
                        ('2', '65.5', '-1.31529', '18.96631', '0.144629'),
                        ('2', '66.5', '-1.31899', '19.15831', '0.145317'),
                        ('2', '67.5', '-1.32204', '19.35149', '0.146009'),
                        ('2', '68.5', '-1.3244', '19.54588', '0.146707'),
                        ('2', '69.5', '-1.32606', '19.74151', '0.147412'),
                        ('2', '70.5', '-1.32702', '19.93843', '0.148127'),
                        ('2', '71.5', '-1.32726', '20.1367', '0.148852'),
                        ('2', '72.5', '-1.32676', '20.33636', '0.14959'),
                        ('2', '73.5', '-1.32554', '20.53748', '0.15034'),
                        ('2', '74.5', '-1.32358', '20.74013', '0.151105'),
                        ('2', '75.5', '-1.32089', '20.94438', '0.151885'),
                        ('2', '76.5', '-1.31747', '21.1503', '0.152682'),
                        ('2', '77.5', '-1.31333', '21.35797', '0.153495'),
                        ('2', '78.5', '-1.30849', '21.56748', '0.154326'),
                        ('2', '79.5', '-1.30295', '21.77891', '0.155174'),
                        ('2', '80.5', '-1.29673', '21.99235', '0.156041'),
                        ('2', '81.5', '-1.28986', '22.20789', '0.156927'),
                        ('2', '82.5', '-1.28236', '22.42562', '0.157831'),
                        ('2', '83.5', '-1.27424', '22.64564', '0.158753'),
                        ('2', '84.5', '-1.26555', '22.86804', '0.159693'),
                        ('2', '85.5', '-1.2563', '23.09293', '0.160651'),
                        ('2', '86.5', '-1.24653', '23.32039', '0.161627'),
                        ('2', '87.5', '-1.23627', '23.55052', '0.162619'),
                        ('2', '88.5', '-1.22555', '23.78342', '0.163628'),
                        ('2', '89.5', '-1.21441', '24.01918', '0.164651'),
                        ('2', '90.5', '-1.20288', '24.25789', '0.165689'),
                        ('2', '91.5', '-1.19101', '24.49965', '0.16674'),
                        ('2', '92.5', '-1.17882', '24.74454', '0.167802'),
                        ('2', '93.5', '-1.16635', '24.99264', '0.168876'),
                        ('2', '94.5', '-1.15365', '25.24403', '0.169959'),
                        ('2', '95.5', '-1.14075', '25.4988', '0.17105'),
                        ('2', '96.5', '-1.12768', '25.75702', '0.172147'),
                        ('2', '97.5', '-1.11449', '26.01874', '0.173249'),
                        ('2', '98.5', '-1.1012', '26.28404', '0.174355'),
                        ('2', '99.5', '-1.08786', '26.55298', '0.175462'),
                        ('2', '100.5', '-1.0745', '26.82559', '0.176568'),
                        ('2', '101.5', '-1.06115', '27.10193', '0.177673'),
                        ('2', '102.5', '-1.04785', '27.38203', '0.178774'),
                        ('2', '103.5', '-1.03462', '27.66593', '0.17987'),
                        ('2', '104.5', '-1.0215', '27.95365', '0.180958'),
                        ('2', '105.5', '-1.00852', '28.24521', '0.182037'),
                        ('2', '106.5', '-0.99571', '28.5406', '0.183105'),
                        ('2', '107.5', '-0.98309', '28.83984', '0.18416'),
                        ('2', '108.5', '-0.97069', '29.14291', '0.185201'),
                        ('2', '109.5', '-0.95853', '29.4498', '0.186225'),
                        ('2', '110.5', '-0.94664', '29.76048', '0.187231'),
                        ('2', '111.5', '-0.93504', '30.07493', '0.188218'),
                        ('2', '112.5', '-0.92376', '30.39308', '0.189183'),
                        ('2', '113.5', '-0.9128', '30.7149', '0.190124'),
                        ('2', '114.5', '-0.9022', '31.04032', '0.191041'),
                        ('2', '115.5', '-0.89196', '31.36928', '0.191932'),
                        ('2', '116.5', '-0.88211', '31.70168', '0.192796'),
                        ('2', '117.5', '-0.87266', '32.03745', '0.19363'),
                        ('2', '118.5', '-0.86363', '32.37649', '0.194434'),
                        ('2', '119.5', '-0.85503', '32.71868', '0.195207'),
                        ('2', '120.5', '-0.84687', '33.06392', '0.195947'),
                        ('2', '121.5', '-0.83917', '33.41208', '0.196653'),
                        ('2', '122.5', '-0.83193', '33.76303', '0.197325'),
                        ('2', '123.5', '-0.82518', '34.11663', '0.197961'),
                        ('2', '124.5', '-0.81891', '34.47272', '0.198561'),
                        ('2', '125.5', '-0.81314', '34.83116', '0.199123'),
                        ('2', '126.5', '-0.80787', '35.19176', '0.199648'),
                        ('2', '127.5', '-0.80312', '35.55437', '0.200134'),
                        ('2', '128.5', '-0.7989', '35.9188', '0.200581'),
                        ('2', '129.5', '-0.7952', '36.28486', '0.200988'),
                        ('2', '130.5', '-0.79205', '36.65236', '0.201356'),
                        ('2', '131.5', '-0.78944', '37.02111', '0.201684'),
                        ('2', '132.5', '-0.78737', '37.39089', '0.201971'),
                        ('2', '133.5', '-0.78587', '37.76149', '0.202218'),
                        ('2', '134.5', '-0.78493', '38.1327', '0.202425'),
                        ('2', '135.5', '-0.78456', '38.5043', '0.202591'),
                        ('2', '136.5', '-0.78476', '38.87605', '0.202717'),
                        ('2', '137.5', '-0.78554', '39.24775', '0.202803'),
                        ('2', '138.5', '-0.7869', '39.61914', '0.202848'),
                        ('2', '139.5', '-0.78886', '39.99', '0.202854'),
                        ('2', '140.5', '-0.7914', '40.36009', '0.20282'),
                        ('2', '141.5', '-0.79455', '40.72918', '0.202747'),
                        ('2', '142.5', '-0.79829', '41.09701', '0.202636'),
                        ('2', '143.5', '-0.80264', '41.46336', '0.202486'),
                        ('2', '144.5', '-0.8076', '41.82798', '0.202299'),
                        ('2', '145.5', '-0.81317', '42.19063', '0.202074'),
                        ('2', '146.5', '-0.81936', '42.55108', '0.201814'),
                        ('2', '147.5', '-0.82616', '42.90909', '0.201517'),
                        ('2', '148.5', '-0.83359', '43.26442', '0.201185'),
                        ('2', '149.5', '-0.84163', '43.61683', '0.200819'),
                        ('2', '150.5', '-0.85031', '43.96612', '0.200419'),
                        ('2', '151.5', '-0.85961', '44.31204', '0.199987'),
                        ('2', '152.5', '-0.86953', '44.65437', '0.199522'),
                        ('2', '153.5', '-0.88009', '44.99291', '0.199027'),
                        ('2', '154.5', '-0.89127', '45.32745', '0.198501'),
                        ('2', '155.5', '-0.90308', '45.65777', '0.197946'),
                        ('2', '156.5', '-0.91551', '45.98369', '0.197363'),
                        ('2', '157.5', '-0.92857', '46.30501', '0.196753'),
                        ('2', '158.5', '-0.94225', '46.62155', '0.196116'),
                        ('2', '159.5', '-0.95654', '46.93314', '0.195455'),
                        ('2', '160.5', '-0.97144', '47.23962', '0.194769'),
                        ('2', '161.5', '-0.98695', '47.54083', '0.194061'),
                        ('2', '162.5', '-1.00305', '47.83661', '0.19333'),
                        ('2', '163.5', '-1.01974', '48.12685', '0.19258'),
                        ('2', '164.5', '-1.03701', '48.41141', '0.191809'),
                        ('2', '165.5', '-1.05485', '48.69018', '0.191021'),
                        ('2', '166.5', '-1.07323', '48.96305', '0.190216'),
                        ('2', '167.5', '-1.09216', '49.22993', '0.189395'),
                        ('2', '168.5', '-1.11161', '49.49075', '0.18856'),
                        ('2', '169.5', '-1.13155', '49.74544', '0.187712'),
                        ('2', '170.5', '-1.15198', '49.99394', '0.186852'),
                        ('2', '171.5', '-1.17287', '50.23621', '0.185983'),
                        ('2', '172.5', '-1.19418', '50.47222', '0.185104'),
                        ('2', '173.5', '-1.21591', '50.70196', '0.184219'),
                        ('2', '174.5', '-1.23801', '50.92541', '0.183328'),
                        ('2', '175.5', '-1.26045', '51.14259', '0.182432'),
                        ('2', '176.5', '-1.28319', '51.35353', '0.181534'),
                        ('2', '177.5', '-1.30621', '51.55825', '0.180635'),
                        ('2', '178.5', '-1.32946', '51.75681', '0.179736'),
                        ('2', '179.5', '-1.3529', '51.94926', '0.17884'),
                        ('2', '180.5', '-1.37648', '52.13568', '0.177947'),
                        ('2', '181.5', '-1.40015', '52.31616', '0.177059'),
                        ('2', '182.5', '-1.42388', '52.4908', '0.176179'),
                        ('2', '183.5', '-1.44759', '52.6597', '0.175307'),
                        ('2', '184.5', '-1.47125', '52.82299', '0.174446'),
                        ('2', '185.5', '-1.49479', '52.98079', '0.173597'),
                        ('2', '186.5', '-1.51816', '53.13327', '0.172761'),
                        ('2', '187.5', '-1.54129', '53.28056', '0.171941'),
                        ('2', '188.5', '-1.56412', '53.42284', '0.171137'),
                        ('2', '189.5', '-1.5866', '53.56028', '0.170352'),
                        ('2', '190.5', '-1.60866', '53.69307', '0.169588'),
                        ('2', '191.5', '-1.63023', '53.82138', '0.168844'),
                        ('2', '192.5', '-1.65125', '53.94544', '0.168125'),
                        ('2', '193.5', '-1.67165', '54.06543', '0.167429'),
                        ('2', '194.5', '-1.69138', '54.18158', '0.16676'),
                        ('2', '195.5', '-1.71036', '54.29411', '0.166118'),
                        ('2', '196.5', '-1.72854', '54.40324', '0.165504'),
                        ('2', '197.5', '-1.74586', '54.50921', '0.164921'),
                        ('2', '198.5', '-1.76224', '54.61224', '0.164368'),
                        ('2', '199.5', '-1.77764', '54.71257', '0.163847'),
                        ('2', '200.5', '-1.79201', '54.81044', '0.163359'),
                        ('2', '201.5', '-1.80528', '54.9061', '0.162905'),
                        ('2', '202.5', '-1.81742', '54.99978', '0.162486'),
                        ('2', '203.5', '-1.82837', '55.09172', '0.162101'),
                        ('2', '204.5', '-1.83809', '55.18217', '0.161753'),
                        ('2', '205.5', '-1.84655', '55.27135', '0.16144'),
                        ('2', '206.5', '-1.85372', '55.35951', '0.161164'),
                        ('2', '207.5', '-1.85957', '55.44686', '0.160924'),
                        ('2', '208.5', '-1.86407', '55.53362', '0.160721'),
                        ('2', '209.5', '-1.86721', '55.62001', '0.160554'),
                        ('2', '210.5', '-1.86898', '55.70624', '0.160423'),
                        ('2', '211.5', '-1.86937', '55.79248', '0.160329'),
                        ('2', '212.5', '-1.86839', '55.87892', '0.160269'),
                        ('2', '213.5', '-1.86603', '55.96573', '0.160245'),
                        ('2', '214.5', '-1.86233', '56.05305', '0.160254'),
                        ('2', '215.5', '-1.85729', '56.141', '0.160296'),
                        ('2', '216.5', '-1.85095', '56.2297', '0.16037'),
                        ('2', '217.5', '-1.84333', '56.31922', '0.160474'),
                        ('2', '218.5', '-1.8345', '56.40963', '0.160607'),
                        ('2', '219.5', '-1.82448', '56.50096', '0.160768'),
                        ('2', '220.5', '-1.81334', '56.5932', '0.160955'),
                        ('2', '221.5', '-1.80115', '56.68633', '0.161166'),
                        ('2', '222.5', '-1.78798', '56.78026', '0.161399'),
                        ('2', '223.5', '-1.7739', '56.8749', '0.161652'),
                        ('2', '224.5', '-1.75901', '56.9701', '0.161923'),
                        ('2', '225.5', '-1.74339', '57.06565', '0.162209'),
                        ('2', '226.5', '-1.72716', '57.16132', '0.162509'),
                        ('2', '227.5', '-1.71041', '57.2568', '0.162819'),
                        ('2', '228.5', '-1.69327', '57.35176', '0.163138'),
                        ('2', '229.5', '-1.67585', '57.44578', '0.163463'),
                        ('2', '230.5', '-1.6583', '57.5384', '0.163791'),
                        ('2', '231.5', '-1.64075', '57.6291', '0.16412'),
                        ('2', '232.5', '-1.62333', '57.71728', '0.164447'),
                        ('2', '233.5', '-1.60621', '57.80227', '0.164771'),
                        ('2', '234.5', '-1.58953', '57.88334', '0.165088'),
                        ('2', '235.5', '-1.57347', '57.95967', '0.165398'),
                        ('2', '236.5', '-1.55818', '58.0304', '0.165698'),
                        ('2', '237.5', '-1.54385', '58.09453', '0.165985'),
                        ('2', '238.5', '-1.53064', '58.15104', '0.16626'),
                        ('2', '239.5', '-1.51875', '58.19877', '0.16652'),
                        ('2', '240', '-1.51336', '58.21897', '0.166645')"
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE weight_for_age_24_months_and_up');
    }
}
