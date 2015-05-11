<queries>
	<sql name="Create_tblConfig" tag="0">
		CREATE TABLE IF NOT EXISTS `tblConfig` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`key` varchar(255) NOT NULL,
	`value` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=80 ;
	</sql>
	<sql name="Insert_tblConfig" tag="0">
		INSERT INTO `tblConfig` (`id`, `key`, `value`) VALUES
	(1, 'domain.templatefolder', 'templates/'),
	(2, 'startvalues.credits', '4800'),
	(3, 'startvalues.physical', '60'),
	(4, 'startvalues.social', '60'),
	(5, 'startvalues.mental', '60'),
	(6, 'startvalues.emotional', '60'),
	(7, 'startvalues.visibility', 'FALSE'),
	(8, 'startvalues.algemenevoorwaarden', 'FALSE'),
	(9, 'levels.0.threshold', '0'),
	(10, 'levels.0.multiplier', '1'),
	(11, 'levels.1.threshold', '0'),
	(12, 'levels.1.multiplier', '1'),
	(13, 'levels.2.threshold', '1400'),
	(14, 'levels.2.multiplier', '1.25'),
	(15, 'levels.3.threshold', '3000'),
	(16, 'levels.3.multiplier', '1.50'),
	(17, 'levels.4.threshold', '8000'),
	(18, 'levels.4.multiplier', '1.75'),
	(19, 'levels.5.threshold', '16000'),
	(20, 'levels.5.multiplier', '2'),
	(21, 'levels.6.threshold', '25000'),
	(22, 'levels.6.multiplier', '2'),
	(23, 'warnings.1.schenkingen', '30'),
	(24, 'warnings.1.transactiediversiteit', '.4'),
	(25, 'warnings.1.credits', '2400'),
	(26, 'warnings.1.waardering', '2.5'),
	(27, 'warnings.1.physical', '50'),
	(28, 'warnings.1.social', '50'),
	(29, 'warnings.1.mental', '50'),
	(30, 'warnings.1.emotional', '50'),
	(31, 'warnings.1.indicatorsom', '220'),
	(32, 'warnings.2.schenkingen', '60'),
	(33, 'warnings.2.transactiediversiteit', '.25'),
	(34, 'warnings.2.credits', '3600'),
	(35, 'warnings.2.waardering', '2'),
	(36, 'warnings.2.physical', '30'),
	(37, 'warnings.2.social', '30'),
	(38, 'warnings.2.mental', '30'),
	(39, 'warnings.2.emotional', '30'),
	(40, 'warnings.2.indicatorsom', '130'),
	(41, 'warnings.3.schenkingen', '80'),
	(42, 'warnings.3.transactiediversiteit', '.1'),
	(43, 'warnings.3.credits', '4200'),
	(44, 'warnings.3.waardering', '1.5'),
	(45, 'warnings.3.physical', '10'),
	(46, 'warnings.3.social', '10'),
	(47, 'warnings.3.mental', '10'),
	(48, 'warnings.3.emotional', '10'),
	(49, 'warnings.3.indicatorsom', '50'),
	(50, 'warnings.4.schenkingen', '100'),
	(51, 'warnings.4.transactiediversiteit', '.05'),
	(52, 'warnings.4.credits', '4700'),
	(53, 'warnings.4.waardering', '1'),
	(54, 'warnings.4.physical', '0'),
	(55, 'warnings.4.social', '0'),
	(56, 'warnings.4.mental', '0'),
	(57, 'warnings.4.emotional', '0'),
	(58, 'warnings.4.indicatorsom', '10'),
	(59, 'crons.indicators', '24*60*60'),
	(60, 'crons.hourstoworkfordelay', '4'),
	(61, 'crons.x', NULL),
	(62, 'date.speed', '1'),
	(63, 'date.start', '1395100800'),
	(64, 'date.timezone', 'Europe/Brussels'),
	(65, 'geo.latitude', '50.8305303'),
	(66, 'geo.longitude', '3.2644603'),
	(67, 'debugging.showwarnings', 'TRUE'),
	(68, 'debugging.demo', 'FALSE'),
	(69, 'credits.min', '0'),
	(70, 'credits.max', '9600'),
	(71, 'credits.name.1', 'owa'),
	(72, 'credits.name.x', 'owa'),
	(73, 'credits.name.overdracht', 'owa-overdracht'),
	(74, 'verzekeringen.1', 'Schade aan derden verzekerd'),
	(75, 'verzekeringen.2', 'Arbeidsongevallen-verzekering afgesloten'),
	(76, 'analytics', NULL),
	(77, 'indicatoren.multiplier', '10'),
	(78, 'indicatoren.owaesadd', '5'),
	(79, 'mail.smtp', 'FALSE');
	</sql>
	<sql name="insert-smtpConfs_tblConfig" tag="1">
		INSERT INTO `tblConfig` (`key`, `value`) VALUES
	('mail.Host', NULL), ('mail.SMTPAuth', 'FALSE'), ('mail.SMTPSecure', 'ssl'),
	('mail.Port', NULL), ('mail.Username', NULL), ('mail.Password', NULL);
	</sql>
	<sql name="insert-fbLogin_tblConfig" tag="2">
		INSERT INTO `tblConfig` (`key`, `value`) VALUES
	('facebook.loginapp.id', NULL), ('facebook.loginapp.secret', NULL);
	</sql>
	<sql name="update-status-inschrijvingen-stap1" tag="3">
		CREATE TABLE tmpToUpdate AS (select s.id from tblMarketSubscriptions s inner join tblPayments p on s.market = p.market and p.actief = 1 and (s.user = p.receiver or s.user = p.sender) and s.overruled = 0 and s.status = 2);
	</sql>
	<sql name="update-status-inschrijvingen-stap2" tag="4">
		update tblMarketSubscriptions set status = 5 where id in (select id from tmpToUpdate); 
	</sql>
	<sql name="update-status-inschrijvingen-stap3" tag="5">
		DROP TABLE tmpToUpdate;
	</sql>
	<sql name="update-cronsIndicators_tblConfig" tag="6">
		UPDATE `tblConfig` SET `value` = '1*24*3600' WHERE `key` LIKE `crons.indicators';
	</sql>
</queries>
