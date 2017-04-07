CREATE TABLE `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `google_group` varchar(255) NOT NULL,
  `mayolink_account` varchar(255) NOT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `organization` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4;

INSERT INTO `sites` (name, google_group, mayolink_account) VALUES 
('Test Site A', 'a', '7035588'),
('Test Site B', 'b', '7035500'),
('University of Arizona CATS','uofacats', '7035650'),
('Banner Health Campus','bannerscampus', '7035651'),
('Banner Health Phoenix','bannerphoenix', '7035652'),
('Banner Health Estrella','bannerestrella', '7035653'),
('Banner Health Desert','bannerdesert', '7035654'),
('University of Illinois at Chicago Research','uicresearch', '7035707'),
('Northwestern University Galter Health Sciences Library','nwfeinberggalter', '7035702'),
('University of Chicago','uofchicago', '7035703'),
('Rush University','rushuniv', '7035704'),
('NorthShore University Health System','northshoree', '7035705'),
('Columbia University Irving Institute','irvingcolumbia', '7035709'),
('Harlem','harlem', '7035710'),
('Weill Cornell Medical Center','weillcornell', '7035711'),
# Note - SDBB and Walgreens share the same account
('San Diego Blood Bank','sdbb', '7035735'),
('Walgreens','walgreens', '7035735'),
('VA Boston HC','vabostonhc', '7035759'),
('VA Palo Alto','vapaloalto', '7035758'),
('Jeanette Phillips','jeanettephillips', '7035760'),
('Monroeville','monroeville', '7035769'),
('Aiken Medical Center','aikenmed', '7035770'),
('University of Pittsburgh Medical Center','upmc', '7035771'),
('Montefiore CTRC','montefiorectrc', '7035772'),
('Pelion Family Practice','pelionfp', '7035779'),
('Waverly Family Practice','waverlyfp', '7035780'),
('Eau Claire Wisconsin','eauclairewi', '7035781'),
('Morristown','morristown', '7035777'),
('Knoxville','knoxville', '7035778'),
('James Anderson','jamesanderson', '7035782'),
('Copiah Medical Associates','copiah', '7035783'),
('Otay Mesa Medical Offices','otay', '7035784'),
('Comprehensive Health Center Ocean View','chcoceanview', '7035785');

