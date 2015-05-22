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
		INSERT INTO `tblConfig` (`key`, `value`) VALUES
	('domain.templatefolder', 'templates/'),
	('startvalues.credits', '4800'),
	('startvalues.physical', '60'),
	('startvalues.social', '60'),
	('startvalues.mental', '60'),
	('startvalues.emotional', '60'),
	('startvalues.visibility', 'FALSE'),
	('startvalues.algemenevoorwaarden', 'FALSE'),
	('levels.0.threshold', '0'),
	('levels.0.multiplier', '1'),
	('levels.1.threshold', '0'),
	('levels.1.multiplier', '1'),
	('levels.2.threshold', '1400'),
	('levels.2.multiplier', '1.25'),
	('levels.3.threshold', '3000'),
	('levels.3.multiplier', '1.50'),
	('levels.4.threshold', '8000'),
	('levels.4.multiplier', '1.75'),
	('levels.5.threshold', '16000'),
	('levels.5.multiplier', '2'),
	('levels.6.threshold', '25000'),
	('levels.6.multiplier', '2'),
	('warnings.1.schenkingen', '30'),
	('warnings.1.transactiediversiteit', '.4'),
	('warnings.1.credits', '2400'),
	('warnings.1.waardering', '2.5'),
	('warnings.1.physical', '50'),
	('warnings.1.social', '50'),
	('warnings.1.mental', '50'),
	('warnings.1.emotional', '50'),
	('warnings.1.indicatorsom', '220'),
	('warnings.2.schenkingen', '60'),
	('warnings.2.transactiediversiteit', '.25'),
	('warnings.2.credits', '3600'),
	('warnings.2.waardering', '2'),
	('warnings.2.physical', '30'),
	('warnings.2.social', '30'),
	('warnings.2.mental', '30'),
	('warnings.2.emotional', '30'),
	('warnings.2.indicatorsom', '130'),
	('warnings.3.schenkingen', '80'),
	('warnings.3.transactiediversiteit', '.1'),
	('warnings.3.credits', '4200'),
	('warnings.3.waardering', '1.5'),
	('warnings.3.physical', '10'),
	('warnings.3.social', '10'),
	('warnings.3.mental', '10'),
	('warnings.3.emotional', '10'),
	('warnings.3.indicatorsom', '50'),
	('warnings.4.schenkingen', '100'),
	('warnings.4.transactiediversiteit', '.05'),
	('warnings.4.credits', '4700'),
	('warnings.4.waardering', '1'),
	('warnings.4.physical', '0'),
	('warnings.4.social', '0'),
	('warnings.4.mental', '0'),
	('warnings.4.emotional', '0'),
	('warnings.4.indicatorsom', '10'),
	('crons.indicators', '86400'),
	('crons.hourstoworkfordelay', '4'),
	('crons.x', NULL),
	('date.speed', '1'),
	('date.start', '1395100800'),
	('date.timezone', 'Europe/Brussels'),
	('geo.latitude', '50.8305303'),
	('geo.longitude', '3.2644603'),
	('debugging.showwarnings', 'TRUE'),
	('debugging.demo', 'FALSE'),
	('credits.min', '0'),
	('credits.max', '9600'),
	('credits.name.1', 'owa'),
	('credits.name.x', 'owa'),
	('credits.name.overdracht', 'owa-overdracht'),
	('verzekeringen.1', 'Schade aan derden verzekerd'),
	('verzekeringen.2', 'Arbeidsongevallen-verzekering afgesloten'),
	('analytics', NULL),
	('indicatoren.multiplier', '10'),
	('indicatoren.owaesadd', '5'),
	('mail.smtp', 'FALSE');
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
		UPDATE `tblConfig` SET `value` = '86400' WHERE `key` LIKE `crons.indicators';
	</sql> 
	<sql name="append-to-tblConfig" tag="7">
		INSERT INTO `tblConfig` (`key`, `value`) VALUES  
			('domain.name', 'localhost'),
			('domain.root', '/owaes/'),
			('domain.absroot', 'http://localhost/owaes/');
	</sql>
	<sql name="mailalerts" tag="8">
		ALTER TABLE `tblUsers` ADD `mailalerts` TEXT NOT NULL AFTER `data` ;
	</sql>
	<sql name="tblconfig-domainsetup-name" tag="9">
		update `tblConfig` set value = NULL where `key` = 'domain.name' and `value` = 'localhost'; 
	</sql>
	<sql name="tblconfig-domainsetup-root" tag="9">
		update `tblConfig` set value = NULL where `key` = 'domain.root' and `value` = '/owaes/'; 
	</sql>
	<sql name="tblconfig-domainsetup-absroot" tag="9">
		update `tblConfig` set value = NULL where `key` = 'domain.absroot' and `value` = 'http://localhost/owaes/'; 
	</sql> 
	<sql name="defaults-mailalerts-a" tag="10">
		INSERT INTO `tblConfig` (`key`, `value`) VALUES  
			('mailalert.newmessage', 86400),
			('mailalert.newsubscription', 86400),
			('mailalert.platform', 1),
			('mailalert.reminderunread', 259200),
			('mailalert.remindersubscription', 259200);
	</sql> 
	<sql name="tblConfig-to-json" tag="10">
		update tblConfig set `value` = concat('"', `value`, '"') where `key` in ('domain.templatefolder', 'date.timezone', 'credits.name.1', 'credits.name.x', 'credits.name.overdracht', 'verzekeringen.1', 'verzekeringen.2', 'mail.Host', 'mail.SMTPAuth', 'mail.SMTPSecure', 'domain.name', 'domain.root', 'domain.absroot') and not (`value` is null)
	</sql>
	<sql name="tblConfig-fix" tag="10">
		update tblConfig set `value` = '86400' where `value` = '24*60*60' and `key` = 'crons.indicators'; 
	</sql> 
	<sql name="tblMailalerts" tag="11"> 
CREATE TABLE IF NOT EXISTS `tblMailalerts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` bigint(20) unsigned NOT NULL,
  `link` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `deadline` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sent` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; 
	</sql> 
	<sql name="tblConfig-change" tag="12">  
		ALTER TABLE `tblConfig` DROP `id`;
		ALTER TABLE `tblConfig` ADD PRIMARY KEY(`key`);
	</sql> 
	<sql name="tblMailalerts-key" tag="13">  
		ALTER TABLE `tblMailalerts` ADD `sleutel` VARCHAR( 25 ) NOT NULL DEFAULT '' AFTER `user` ;
		ALTER TABLE `tblMailalerts` CHANGE `sent` `sent` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL ;
	</sql>  
</queries>