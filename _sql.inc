<queries>
	<sql name="createDB" tag="0">  
CREATE TABLE IF NOT EXISTS `tblAbuse` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created` bigint(20) unsigned NOT NULL DEFAULT '0',
  `reason` varchar(50) NOT NULL,
  `reporter` bigint(20) unsigned NOT NULL,
  `user` bigint(20) NOT NULL DEFAULT '0',
  `market` bigint(20) NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblActions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) NOT NULL DEFAULT '0',
  `created` bigint(20) NOT NULL DEFAULT '0',
  `tododate` bigint(20) NOT NULL DEFAULT '0',
  `completed` bigint(20) NOT NULL DEFAULT '0',
  `actie` varchar(50) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblBadges` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mkey` varchar(30) NOT NULL,
  `img` varchar(30) NOT NULL,
  `title` varchar(100) NOT NULL,
  `info` text NOT NULL,
  `zichtbaar` smallint(2) NOT NULL DEFAULT '0',
  `beloning` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1; 

INSERT INTO tblBadges VALUES('1', 'car', 'car.png', '', '', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('2', 'cupcake', 'cupcake.png', '', '', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('3', 'earlybird', 'earlybird.png', '', '', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('4', 'photo', 'photo.png', 'Profielfoto', 'Deze gebruiker heeft een profielfoto ingesteld. Way to go! ', '1', '{"emotional":1, "physical": 1}'); 
INSERT INTO tblBadges VALUES('5', 'play', 'play.png', '', '', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('6', 'power', 'power.png', '', '', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('7', 'tie', 'tie.png', '', '', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('17', '25transactions', 'trans25.png', '', '', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('16', '10transactions', 'trans10.png', '10 Transacties', 'Deze persoon heeft 10 transacties uitgevoerd. Goed bezig! ', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('15', '1transaction', 'trans1.png', '1e Transactie', 'Een eerste transactie is gebeurd. Deze persoon is goed gestart. ', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('18', '50transactions', 'trans50.png', '', '', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('19', 'heyhallo', 'heyhallo.png', 'Hey Hallo ', 'Stuur een privÃ©bericht naar 1 persoon', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('20', '1friend', '1friend.png', '1 vriend', '', '1', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('21', '10friends', '10friends.png', '10 vrienden', '', '1', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('22', 'ervaring', 'ervaring.png', 'ervaring toegevoegd', '', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('23', 'opleiding', 'opleiding.png', 'opleiding toegevoegd', '', '0', '{"emotional":1, "mental": 1}'); 
INSERT INTO tblBadges VALUES('24', 'infra', 'infra.png', 'infrastructuur toegevoegd', '', '0', '{"emotional":1, "physical": 1}'); 
INSERT INTO tblBadges VALUES('25', 'done-ervaring', 'ervaring.png', 'ervaring opgedaan', '', '1', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('26', 'done-infra', 'infra.png', 'infrastructuur gehuurd', '', '1', '{"emotional":1, "physical": 1}'); 
INSERT INTO tblBadges VALUES('27', 'done-opleiding', 'opleiding.png', 'opleiding genoten', '', '1', '{"emotional":1, "mental": 1}'); 
INSERT INTO tblBadges VALUES('28', 'follower', 'follower.png', 'Volger', 'Je volgt minimum Ã©Ã©n persoon', '0', '{"emotional":1, "social": 1}'); 
INSERT INTO tblBadges VALUES('29', 'profile', 'profile.png', 'Volledig profiel', 'Je profiel is volledig aangevuld', '1', '{"emotional":1, "social": 1}'); 

CREATE TABLE IF NOT EXISTS `tblCertificates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mkey` varchar(30) NOT NULL,
  `img` varchar(30) NOT NULL,
  `title` varchar(100) NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1; 

INSERT INTO tblCertificates VALUES('1', 'redcross', 'redcross.png', 'EHBO-certificaat', 'Deze persoon heeft een EHBO-cursus gevolgd. '); 

CREATE TABLE IF NOT EXISTS `tblConfig` (
  `sleutel` varchar(255) NOT NULL,
  `waarde` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sleutel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; 

CREATE TABLE IF NOT EXISTS `tblConversations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sender` bigint(20) NOT NULL,
  `receiver` bigint(20) NOT NULL,
  `subject` text NOT NULL,
  `market` bigint(20) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `sentdate` bigint(20) NOT NULL DEFAULT '0',
  `readdate` bigint(20) NOT NULL DEFAULT '0',
  `isread` int(11) NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=320 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblDbChanges` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `tag` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblExperience` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `idk` varchar(30) NOT NULL,
  `user` bigint(20) unsigned NOT NULL,
  `experience` int(11) NOT NULL DEFAULT '0',
  `datum` bigint(20) NOT NULL DEFAULT '0',
  `confirmed` int(11) NOT NULL DEFAULT '0',
  `details` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6987 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblFeedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction` bigint(20) unsigned NOT NULL,
  `from` bigint(20) NOT NULL,
  `to` bigint(20) NOT NULL,
  `stars` int(11) NOT NULL,
  `public` text NOT NULL,
  `private` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblFollowers` (
  `user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `followed` bigint(20) unsigned NOT NULL,
  `startdate` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`,`followed`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblFriends` (
  `user` bigint(20) unsigned NOT NULL COMMENT 't',
  `friend` bigint(20) unsigned NOT NULL,
  `datum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `confirmed` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`,`friend`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblGroupUsers` (
  `groep` bigint(20) unsigned NOT NULL,
  `user` bigint(20) unsigned NOT NULL,
  `invitedby` bigint(20) unsigned NOT NULL DEFAULT '0',
  `useradd` int(11) NOT NULL DEFAULT '0',
  `userdel` int(11) NOT NULL DEFAULT '0',
  `userrights` int(11) NOT NULL DEFAULT '0',
  `owaesadd` int(11) NOT NULL DEFAULT '0',
  `owaesedit` int(11) NOT NULL DEFAULT '0',
  `owaesdel` int(11) NOT NULL DEFAULT '0',
  `owaesselect` int(11) NOT NULL DEFAULT '0',
  `owaespay` int(11) NOT NULL DEFAULT '0',
  `groupinfo` int(11) NOT NULL DEFAULT '0',
  `confirmed` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`groep`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblGroups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naam` varchar(150) NOT NULL,
  `website` varchar(255) NOT NULL,
  `alias` varchar(120) NOT NULL,
  `info` text NOT NULL,
  `img` varchar(255) NOT NULL,
  `isdienstverlener` int(1) unsigned NOT NULL DEFAULT '0',
  `admin` bigint(20) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `lastupdate` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblIndicators` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) NOT NULL,
  `datum` bigint(20) NOT NULL,
  `physical` int(11) NOT NULL DEFAULT '0',
  `mental` int(11) NOT NULL DEFAULT '0',
  `emotional` int(11) NOT NULL DEFAULT '0',
  `social` int(11) NOT NULL DEFAULT '0',
  `reason` int(11) NOT NULL,
  `link` bigint(11) NOT NULL,
  `actief` tinyint(4) NOT NULL DEFAULT '1',
  `tmp` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9860 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblLog` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` bigint(20) unsigned NOT NULL DEFAULT '0',
  `datum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `info` text NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7441 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblMailalerts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` bigint(20) unsigned NOT NULL,
  `sleutel` varchar(25) NOT NULL DEFAULT '',
  `link` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `deadline` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sent` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblMarket` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `author` bigint(20) NOT NULL,
  `groep` bigint(20) unsigned NOT NULL DEFAULT '0',
  `createdby` bigint(20) NOT NULL DEFAULT '0',
  `mtype` int(11) DEFAULT '0',
  `title` varchar(125) NOT NULL,
  `body` text NOT NULL,
  `date` bigint(20) NOT NULL,
  `lastupdate` bigint(20) unsigned NOT NULL DEFAULT '0',
  `files` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `location_lat` double NOT NULL,
  `location_long` double NOT NULL,
  `timingtype` varchar(10) NOT NULL,
  `timing` bigint(20) NOT NULL,
  `credits` bigint(20) NOT NULL,
  `details` text NOT NULL,
  `keyid` varchar(256) NOT NULL,
  `physical` bigint(20) NOT NULL DEFAULT '0',
  `mental` bigint(20) NOT NULL DEFAULT '0',
  `emotional` bigint(20) NOT NULL DEFAULT '0',
  `social` bigint(20) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=178 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblMarketDates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `market` bigint(20) unsigned NOT NULL,
  `datum` bigint(20) NOT NULL,
  `start` bigint(20) unsigned NOT NULL,
  `tijd` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=650 DEFAULT CHARSET=utf8; 


CREATE TABLE IF NOT EXISTS `tblMarketSubscriptions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) NOT NULL,
  `doneby` bigint(20) NOT NULL,
  `market` bigint(20) NOT NULL,
  `status` bigint(20) NOT NULL,
  `overruled` tinyint(1) NOT NULL DEFAULT '0',
  `clickdate` bigint(20) NOT NULL,
  `snap` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblMarketTags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `market` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tag` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=299 DEFAULT CHARSET=latin1; 

CREATE TABLE IF NOT EXISTS `tblMarketTypes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sleutel` varchar(10) NOT NULL,
  `title` varchar(250) NOT NULL,
  `direction` int(5) NOT NULL COMMENT '0 = costs credits / 1 = will generate credits',
  `volgorde` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1; 

INSERT INTO tblMarketTypes VALUES('1', 'ervaring', 'werkervaring', '0', '1'); 
INSERT INTO tblMarketTypes VALUES('2', 'opleiding', 'opleidingen', '1', '2'); 
INSERT INTO tblMarketTypes VALUES('3', 'infra', 'infrastructuur', '1', '3'); 

CREATE TABLE IF NOT EXISTS `tblNotifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sleutel` varchar(30) NOT NULL,
  `readdate` bigint(20) NOT NULL DEFAULT '0',
  `message` varchar(300) NOT NULL,
  `link` varchar(150) NOT NULL,
  `author` bigint(20) unsigned NOT NULL DEFAULT '0',
  `receiver` bigint(20) unsigned NOT NULL DEFAULT '0',
  `datum` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=386 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblPayments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `datum` bigint(20) NOT NULL DEFAULT '0',
  `initiator` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sender` bigint(20) NOT NULL DEFAULT '0',
  `receiver` bigint(20) NOT NULL DEFAULT '0',
  `credits` bigint(20) unsigned NOT NULL DEFAULT '0',
  `reason` bigint(20) NOT NULL DEFAULT '0',
  `link` bigint(20) NOT NULL DEFAULT '0',
  `market` bigint(20) unsigned NOT NULL DEFAULT '0',
  `actief` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblQuests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `volgorde` bigint(20) NOT NULL DEFAULT '0',
  `titel` varchar(250) NOT NULL,
  `trigger` text NOT NULL,
  `todos` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblStagemarkt` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` bigint(20) unsigned NOT NULL DEFAULT '0',
  `groepsnaam` text NOT NULL,
  `description` text NOT NULL,
  `interesse` text NOT NULL,
  `logo` text NOT NULL,
  `website` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblStars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `datum` bigint(20) NOT NULL DEFAULT '0',
  `receiver` bigint(20) NOT NULL DEFAULT '0',
  `sender` bigint(20) NOT NULL DEFAULT '0',
  `market` bigint(20) NOT NULL DEFAULT '0',
  `stars` int(11) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `actief` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblTransactions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` bigint(20) NOT NULL,
  `initiator` bigint(20) NOT NULL,
  `sender` bigint(20) NOT NULL,
  `scoresender` int(11) NOT NULL,
  `receiver` bigint(20) NOT NULL,
  `scorereceiver` int(11) NOT NULL,
  `sendersigned` bigint(20) unsigned NOT NULL DEFAULT '0',
  `receiversigned` bigint(20) unsigned NOT NULL DEFAULT '0',
  `number` bigint(20) NOT NULL,
  `physical` int(11) NOT NULL,
  `mental` int(11) NOT NULL,
  `emotional` int(11) NOT NULL,
  `social` int(11) NOT NULL,
  `status` bigint(20) NOT NULL,
  `info` varchar(150) NOT NULL,
  `publicfeedbacktosender` text NOT NULL,
  `privatefeedbacktosender` text NOT NULL,
  `publicfeedbacktoreceiver` text NOT NULL,
  `privatefeedbacktoreceiver` text NOT NULL,
  `market` bigint(20) NOT NULL,
  `code` varchar(130) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblUserBadges` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) NOT NULL,
  `badge` bigint(20) NOT NULL,
  `date` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblUserCertificates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) NOT NULL,
  `certificate` bigint(20) NOT NULL,
  `date` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblUserEmailVerify` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` bigint(20) unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL,
  `sleutel` varchar(255) NOT NULL,
  `verified` int(11) NOT NULL DEFAULT '0',
  `datum` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblUserMailSettings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` bigint(20) unsigned NOT NULL,
  `sleutel` varchar(20) NOT NULL,
  `timing` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblUserRecover` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) NOT NULL,
  `timeasked` bigint(20) NOT NULL,
  `timeexpires` bigint(20) NOT NULL,
  `ipasked` varchar(25) NOT NULL,
  `conf` text NOT NULL,
  `passcode` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblUserSessions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) NOT NULL,
  `start` bigint(20) NOT NULL,
  `stop` bigint(20) NOT NULL,
  `sessionpass` varchar(128) NOT NULL,
  `active` decimal(10,0) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `conf` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=328 DEFAULT CHARSET=latin1; 


CREATE TABLE IF NOT EXISTS `tblUsers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `alias` varchar(32) NOT NULL,
  `login` varchar(125) NOT NULL,
  `pass` varchar(256) NOT NULL,
  `firstname` varchar(125) NOT NULL,
  `lastname` varchar(125) NOT NULL,
  `mail` varchar(125) NOT NULL,
  `mailverified` int(11) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `img` varchar(128) NOT NULL,
  `gender` varchar(10) NOT NULL DEFAULT '',
  `birthdate` double NOT NULL DEFAULT '0',
  `telephone` varchar(30) NOT NULL,
  `physical` bigint(20) NOT NULL DEFAULT '0',
  `mental` bigint(20) NOT NULL DEFAULT '0',
  `emotional` bigint(20) NOT NULL DEFAULT '0',
  `social` bigint(20) NOT NULL DEFAULT '0',
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  `lastupdate` bigint(20) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `statusinfo` text NOT NULL,
  `statusdate` bigint(20) unsigned NOT NULL DEFAULT '0',
  `bestanden` text NOT NULL,
  `location` text NOT NULL,
  `location_lat` double NOT NULL DEFAULT '0',
  `location_long` double NOT NULL DEFAULT '0',
  `showfirstname` int(2) NOT NULL DEFAULT '1',
  `showlastname` int(2) NOT NULL DEFAULT '1',
  `showemail` int(2) NOT NULL DEFAULT '0',
  `showdescription` int(2) NOT NULL DEFAULT '1',
  `showimg` int(2) NOT NULL DEFAULT '1',
  `showlocation` int(2) NOT NULL DEFAULT '1',
  `showbirthdate` int(2) NOT NULL DEFAULT '1',
  `showgender` int(2) NOT NULL DEFAULT '1',
  `showtelephone` int(2) NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `mailalerts` text NOT NULL,
  `dienstverlener` bigint(20) unsigned NOT NULL DEFAULT '0',
  `deleted` bit(1) NOT NULL DEFAULT b'0',
  `actief` int(2) NOT NULL DEFAULT '1',
  `demo` int(11) NOT NULL DEFAULT '0',
  `algemenevoorwaarden` smallint(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=latin1;  
	</sql>
	<sql name="Insert_tblConfig" tag="0">
		INSERT INTO `tblConfig` (`sleutel`, `waarde`) VALUES
	('domain.templatefolder', '"templates/"'),
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
	('date.timezone', '"Europe/Brussels"'),
	('geo.latitude', '50.8305303'),
	('geo.longitude', '3.2644603'),
	('debugging.showwarnings', 'TRUE'),
	('debugging.demo', 'FALSE'),
	('credits.min', '0'),
	('credits.max', '9600'),
	('credits.name.1', '"owa"'),
	('credits.name.x', '"owa"'),
	('credits.name.overdracht', '"owa-overdracht"'),
	('verzekeringen.1', '"Schade aan derden verzekerd"'),
	('verzekeringen.2', '"Arbeidsongevallen-verzekering afgesloten"'),
	('analytics', NULL),
	('indicatoren.multiplier', '10'),
	('indicatoren.owaesadd', '5'),
	('mail.smtp', 'FALSE'),
	('mail.Host', NULL), 
	('mail.SMTPAuth', 'FALSE'), 
	('mail.SMTPSecure', '"ssl"'),
	('mail.Port', NULL), 
	('mail.Username', NULL), 
	('mail.Password', NULL),
	('mailalert.newmessage', 86400),
	('mailalert.newsubscription', 86400),
	('mailalert.platform', 1),
	('mailalert.reminderunread', 259200),
	('mailalert.remindersubscription', 259200),
	('facebook.loginapp.id', NULL), 
	('facebook.loginapp.secret', NULL),
	('domain.name', NULL),
	('domain.root', NULL),
	('domain.absroot', NULL);
	</sql>   
	<sql name="deleted_field_users" tag="18">
		ALTER TABLE `tblUsers` CHANGE `deleted` `deleted` INT( 1 ) NOT NULL DEFAULT '0';
	</sql>  
	<sql name="firstUser" tag="19">
		insert into tblUsers (login, firstname, lastname, alias, mail, mailverified, actief, algemenevoorwaarden, visible, pass, admin) values ('owaes', 'Owaes', 'Owaes', 'owaes', 'changeme@owaes.org', '1', '1', 1, 0,  'e6f86f6ef61a21d693b73bcc92a247c2', 1);
	</sql>  
	<sql name="actie-locatie-invullen" tag="20">
		insert into tblActions (user, actie) (select id, 'location' from tblUsers where id not in (select user from tblActions where actie = 'location')); 
	</sql> 
	<sql name="actie-locatie-badge" tag="21">
INSERT INTO tblBadges (`id` , `mkey` ,`img` ,`title` ,`info` ,`zichtbaar` ,`beloning`)VALUES (NULL , 'location', 'location.png', 'Locatie ingevuld', 'Deze gebruiker heeft zijn profiel aangevuld met zijn woonplaats. ', '1', '{"emotional":1, "mental": 1}');
	</sql>  
	<sql name="tblStagemarktStudenten" tag="22">
		CREATE TABLE IF NOT EXISTS `tblStagemarktStudInschrijvingen` (`student` bigint(20) unsigned NOT NULL, `k1` bigint(20) NOT NULL, `k2` bigint(20) NOT NULL, `k3` bigint(20) NOT NULL, `k4` bigint(20) NOT NULL,`k5` bigint(20) NOT NULL, PRIMARY KEY (`student`)) ENGINE=InnoDB DEFAULT CHARSET=latin1; 
	</sql> 
	<sql name="tblStagemarktBedrijvenVolzet" tag="22"> 
CREATE TABLE IF NOT EXISTS `tblStagemarktVolzet` (
  `bedrijfsid` bigint(20) NOT NULL,
  PRIMARY KEY (`bedrijfsid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; 
	</sql> 
	<sql name="tblStagemarktDates" tag="22"> 
CREATE TABLE IF NOT EXISTS `tblStagemarktDates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bedrijf` bigint(20) NOT NULL DEFAULT '0',
  `student` bigint(20) NOT NULL DEFAULT '0',
  `slot` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; 
	</sql> 
	<sql name="tblGroups-wijzoeken" tag="23"> 
ALTER TABLE `tblGroups` ADD `wijzoeken` TEXT NOT NULL ;
	</sql> 
	<sql name="tblPayments-complete" tag="24"> 
ALTER TABLE `tblPayments` ADD `voorschot` BIGINT NOT NULL DEFAULT '0' AFTER `market` ;
	</sql> 
	<sql name="tblPayments-groups" tag="25"> 
		ALTER TABLE `tblPayments` ADD `sendergroup` BIGINT UNSIGNED NOT NULL DEFAULT '0' AFTER `sender` ;
		ALTER TABLE `tblPayments` ADD `receivergroup` BIGINT UNSIGNED NOT NULL DEFAULT '0' AFTER `receiver` ;
	</sql>  
  <sql name="tblMarketTypes-sleutel" tag="26"> 
    ALTER TABLE `tblMarketTypes` CHANGE `key` `sleutel` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
  </sql> 
  <sql name="indicatoren-float" tag="27"> 
    ALTER TABLE `tblIndicators` CHANGE `physical` `physical` FLOAT( 11 ) NOT NULL DEFAULT '0';
    ALTER TABLE `tblIndicators` CHANGE `mental` `mental` FLOAT( 11 ) NOT NULL DEFAULT '0';
    ALTER TABLE `tblIndicators` CHANGE `emotional` `emotional` FLOAT( 11 ) NOT NULL DEFAULT '0';
    ALTER TABLE `tblIndicators` CHANGE `social` `social` FLOAT( 11 ) NOT NULL DEFAULT '0';
  </sql> 

</queries>