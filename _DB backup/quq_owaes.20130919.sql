-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 19, 2013 at 09:29 AM
-- Server version: 5.0.91-community
-- PHP Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `quq_owaes`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblBadges`
--

CREATE TABLE IF NOT EXISTS `tblBadges` (
  `id` bigint(20) NOT NULL auto_increment,
  `mkey` varchar(30) NOT NULL,
  `img` varchar(30) NOT NULL,
  `title` varchar(100) NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `tblBadges`
--

INSERT INTO `tblBadges` (`id`, `mkey`, `img`, `title`, `info`) VALUES
(1, 'car', 'car.png', '', ''),
(2, 'cupcake', 'cupcake.png', '', ''),
(3, 'earlybird', 'earlybird.png', '', ''),
(4, 'photo', 'photo.png', '', ''),
(5, 'play', 'play.png', '', ''),
(6, 'pwoer', 'power.png', '', ''),
(7, 'tie', 'tie.png', '', ''),
(17, '25transactions', 'trans25.png', '', ''),
(16, '10transactions', 'trans10.png', '', ''),
(15, '1transaction', 'trans1.png', '1st Transaction', ''),
(18, '50transactions', 'trans50.png', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tblCertificates`
--

CREATE TABLE IF NOT EXISTS `tblCertificates` (
  `id` bigint(20) NOT NULL auto_increment,
  `mkey` varchar(30) NOT NULL,
  `img` varchar(30) NOT NULL,
  `title` varchar(100) NOT NULL,
  `info` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tblCertificates`
--

INSERT INTO `tblCertificates` (`id`, `mkey`, `img`, `title`, `info`) VALUES
(1, 'redcross', 'redcross.png', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tblConversations`
--

CREATE TABLE IF NOT EXISTS `tblConversations` (
  `id` bigint(20) NOT NULL auto_increment,
  `sender` bigint(20) NOT NULL,
  `receivers` varchar(120) NOT NULL,
  `subject` text NOT NULL,
  `market` bigint(20) NOT NULL default '0',
  `message` text NOT NULL,
  `sentdate` bigint(20) NOT NULL default '0',
  `readdate` bigint(20) NOT NULL default '0',
  `isread` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=78 ;

--
-- Dumping data for table `tblConversations`
--

INSERT INTO `tblConversations` (`id`, `sender`, `receivers`, `subject`, `market`, `message`, `sentdate`, `readdate`, `isread`) VALUES
(1, 1, '1,6', 'test', 0, 'bericht', 1372674174, 0, 0),
(2, 1, '1,6', 'test', 0, 'bericht', 1372674195, 0, 0),
(3, 1, '1,6', 'titeltjen', 0, 'berichtjen', 1372680498, 0, 0),
(4, 1, '1', '', 0, 'bericht', 1372687787, 1377780864, 1),
(5, 1, '1,5', '', 0, '', 1372689038, 1377612156, 1),
(6, 1, '1,5', '', 0, '', 1372689157, 1377612156, 1),
(7, 1, '1,5', '', 0, '', 1372689183, 1377612156, 1),
(8, 1, '1,5', '', 0, '', 1372689202, 1377612156, 1),
(9, 1, '1,5', '', 0, '', 1372689236, 1377612156, 1),
(10, 1, '1,5', '', 0, 'qdsfsqdf', 1372689260, 1377612156, 1),
(11, 1, '1,5', '', 0, 'snel verzenden', 1372689297, 1377612156, 1),
(12, 5, '2,5', '', 0, 'test naar Marcel', 1372753656, 1377767470, 1),
(13, 1, '1,21', '', 0, '', 1372923188, 0, 0),
(14, 1, '1,21', 'gezocht: autowasser', 0, 'u werd niet aanvoord voor deze opdracht', 1373027188, 0, 0),
(15, 1, '1', '\nfoto''s op je PC in mapjes te zetten, evt bewerken', 0, 'Uw inschrijving werd bevestigd', 1373292135, 1377780864, 1),
(16, 1, '1,18', '\nfoto''s op je PC in mapjes te zetten, evt bewerken', 0, 'Uw inschrijving werd bevestigd', 1373292147, 1377780999, 1),
(17, 1, '1,5', 'gezocht: autowasser', 0, 'u werd niet gekozen voor deze opdracht', 1373292394, 1377612156, 1),
(18, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373295127, 1377612156, 1),
(19, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373295174, 1377612156, 1),
(20, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373295244, 1377612156, 1),
(21, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373295284, 1377612156, 1),
(22, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373295306, 1377612156, 1),
(23, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373356767, 1377612156, 1),
(35, 5, '1,5', 'qdsf', 0, 'u werd niet gekozen voor deze opdracht', 1373361413, 1377612156, 1),
(36, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373363603, 1377612156, 1),
(38, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373363617, 1377612156, 1),
(39, 5, '5,18', 'werker', 0, 'u werd niet gekozen voor deze opdracht', 1373363632, 1377767865, 1),
(40, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373363635, 1377612156, 1),
(41, 5, '1,5', 'werker', 0, 'Uw inschrijving werd bevestigd', 1373368079, 1377612156, 1),
(42, 5, '1,5', '\nnaailes aangeboden', 0, 'Uw inschrijving werd bevestigd', 1373446487, 1377612156, 1),
(43, 1, '1,5', 'gezocht: autowasser', 0, 'Uw inschrijving werd bevestigd', 1373446526, 1377612156, 1),
(44, 1, '1,18', '\nfoto''s op je PC in mapjes te zetten, evt bewerken', 0, 'Uw inschrijving werd bevestigd', 1373446577, 1377780999, 1),
(45, 16, '1,16', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373622506, 0, 0),
(46, 16, '5,16', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373622506, 1377781943, 1),
(47, 16, '15,16', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373622506, 0, 0),
(48, 16, '1,16', 'Cursus assertiviteit', 0, 'Uw inschrijving werd bevestigd', 1373623089, 0, 0),
(49, 16, '1,16', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373623164, 0, 0),
(50, 16, '5,16', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373623164, 1377781943, 1),
(51, 16, '16,18', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373623164, 1378201462, 1),
(52, 16, '1,16', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373623284, 0, 0),
(53, 16, '5,16', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373623284, 1377781943, 1),
(54, 16, '16,18', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373623284, 1378201462, 1),
(55, 16, '1,16', 'Cursus assertiviteit', 0, 'Uw inschrijving werd bevestigd', 1373623399, 0, 0),
(56, 16, '5,16', 'Cursus assertiviteit', 0, 'Uw inschrijving werd bevestigd', 1373623399, 1377781943, 1),
(57, 16, '16,18', 'Cursus assertiviteit', 0, 'u werd niet gekozen voor deze opdracht', 1373623399, 1378201462, 1),
(58, 5, '1,5', 'ik vraag iets / iemand', 0, 'Uw inschrijving werd bevestigd', 1374226011, 1377612156, 1),
(59, 5, '1,5', 'ik bied iets aan', 0, 'Uw inschrijving werd bevestigd', 1374226200, 1377612156, 1),
(60, 5, '1,5', 'qdsf', 0, 'Uw inschrijving werd bevestigd', 1374226362, 1377612156, 1),
(61, 5, '1,5', '', 0, 'qdsfsdqfqsdf', 1377614687, 1377614687, 1),
(62, 5, '1,5', '', 0, 'Benedikt test ', 1377614741, 1377614741, 1),
(63, 5, '1,5', '', 0, 'Hallo bericht', 1377767880, 1377767880, 1),
(64, 5, '1,5', '', 0, 'test', 1377777249, 1377777249, 1),
(65, 5, '1,5', '', 0, 'dsf', 1377777259, 1377777259, 1),
(66, 5, '1,5', '', 0, 'van Els, naar Alexander', 1377779356, 1377779356, 1),
(67, 1, '1,5', '', 0, 'hallo', 1377781278, 1377781278, 1),
(68, 1, '1,5,6', '', 0, 'hallo\r\ntest tussen 1, 5 en 6', 1377781351, 1377781351, 1),
(69, 5, '1,5,6', '', 0, 'ik test mee', 1377781369, 1377781369, 1),
(70, 1, '1,5,6', '', 0, 'hallo\r\ntest tussen 1, 5 en 6', 1377781811, 1377781811, 1),
(71, 5, '1,5', 'dsqf', 0, 'Uw inschrijving werd bevestigd', 1378387332, 1378387371, 1),
(72, 5, '1,5', '', 34, 'Uw inschrijving werd bevestigd', 1378387879, 1378388000, 1),
(73, 5, '1,5', '', 0, 'u werd niet gekozen voor deze opdracht', 1378467416, 1378467439, 1),
(74, 5, '1,5', '', 0, 'fdgdsfgdsfg', 1378467517, 1378467517, 1),
(75, 5, '1,5', '', 28, 'Uw inschrijving werd bevestigd', 1378467926, 1378467931, 1),
(76, 5, '1,5', '', 39, 'Uw inschrijving werd bevestigd', 1378469388, 0, 0),
(77, 5, '5,16', '', 39, 'u werd niet gekozen voor deze opdracht', 1378469388, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblFeedback`
--

CREATE TABLE IF NOT EXISTS `tblFeedback` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `transaction` bigint(20) unsigned NOT NULL,
  `from` bigint(20) NOT NULL,
  `to` bigint(20) NOT NULL,
  `stars` int(11) NOT NULL,
  `public` text NOT NULL,
  `private` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tblMarket`
--

CREATE TABLE IF NOT EXISTS `tblMarket` (
  `id` bigint(20) NOT NULL auto_increment,
  `author` bigint(20) NOT NULL,
  `task` tinyint(1) NOT NULL default '0' COMMENT '0: ik wil dit doen en credits krijgen / 1: ik geef credits om dit te komen doen',
  `title` varchar(125) NOT NULL,
  `body` text NOT NULL,
  `date` bigint(20) NOT NULL,
  `img` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `location_lat` double NOT NULL,
  `location_long` double NOT NULL,
  `timingstart` bigint(20) NOT NULL,
  `timingstop` bigint(20) NOT NULL,
  `timing` bigint(20) NOT NULL,
  `credits` bigint(20) NOT NULL,
  `details` text NOT NULL,
  `keyid` varchar(256) NOT NULL,
  `physical` bigint(20) NOT NULL default '0',
  `mental` bigint(20) NOT NULL default '0',
  `emotional` bigint(20) NOT NULL default '0',
  `social` bigint(20) NOT NULL default '0',
  `state` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `tblMarket`
--

INSERT INTO `tblMarket` (`id`, `author`, `task`, `title`, `body`, `date`, `img`, `location`, `location_lat`, `location_long`, `timingstart`, `timingstop`, `timing`, `credits`, `details`, `keyid`, `physical`, `mental`, `emotional`, `social`, `state`, `deleted`) VALUES
(1, 1, 0, '\nfoto''s op je PC in mapjes te zetten, evt bewerken', 'Voor de beginner met een digitale camera kan ik helpen (en aanleren) foto''s op je PC in mapjes te zetten, zonodig beetje te bewerken enz', 0, 'img', '', 0, 0, 0, 0, 0, 0, 'details', '', 50, 0, 0, 50, 1, 0),
(2, 2, 1, '\nKlushulp met boormachine gevraagd', 'Ik heb een aantal lijsten en planken die ik wil ophangen.\nVerschillende groottes/lengtes en het hoeft niet allemaal in 1 keer.\n\n-lijst (circa 60x50) voor boven de trap, handig als je 1,70m of langer bent.\n-plank in keuken\n-canvas/lijsten/lijstjes in huiskamer\naanvulling1:\n-plafondlamp (kroonsteentje aanwezig, mini-schroevendraaier ook)\n-rookmelder(s) (even wachten tot ik in juli deze mag uitzoeken met de trouwe-klant-bon)\n\nMijn voorraad schroeven en pluggen ga ik nog inventariseren/uitbreiden, dus liever niet bellen/mailen op dezelfde dag dat je wilt komen helpen.', 0, '', '', 0, 0, 0, 0, 0, 3, '', '', 50, 75, 0, 0, 0, 0),
(3, 3, 1, 'verven van de boot', 'wie wil mij helpen met het verven van de boot. ik heb ong 5 mensen nodig. Ik zorg zelf voor kwast en verf en dan is het de bedoeling dat we samen opwerken . de een doet de randjes en de ander gaater met de roller langs zo werk je lekker snel op en dan moet binnen een paar uur de boot in de verf zitten\n\nalvast bedankt .  ', 1369315308, 'img', '', 0, 0, 0, 0, 0, 5, 'details', '', 25, 25, 25, 25, 0, 0),
(4, 3, 1, 'Gevraagd: Hulp bij tuinieren 30 maart', 'Zijn er 2 of 3 mensen die zaterdag toevallig nog niks te doen hebben en bereid zijn me 1 of 2 uurtjes te helpen mijn tuintje zomerklaar te maken?', 1369317058, 'img', '', 0, 0, 0, 0, 0, 8, '{"title":"dfjhlkml","location":"jmkl","timingstart":"jklj","timingstop":"klj","timingfree":"free","body":"klj","types":"","fitness":"mljmlkj","development":"ljk","social":"klj","green":"klj","credits":"lkj","img":"","owaesadd":"opslaan"}', '', 0, 25, 50, 25, 0, 0),
(5, 4, 0, 'Canna zaadjes', 'Na de bloei is deze Canna (zie bijlage) zaadjes aan het produceren. [Sommige zaden geven gele bloemen, die komen dus van een nadere Canna]\n\nWil je deze schoonheid ook op je balkon of in de tuin? Ik deel ze uit per 5 stuks ; voor bezorgen komen er wat credits bij - afhankelijk van waar je woont\n\nDe zaadjes moeten wel wat bewerkt worden voor ze het gaat doen - gebruiksaanwijzing krijg je erbij', 1369317072, 'img', '', 0, 0, 0, 0, 0, 1, '{"title":"dfjhlkml","location":"jmkl","timingstart":"jklj","timingstop":"klj","timingfree":"free","body":"klj","types":"","fitness":"mljmlkj","development":"ljk","social":"klj","green":"klj","credits":"lkj","img":"","owaesadd":"opslaan"}', '', 25, 25, 25, 25, 0, 0),
(6, 5, 0, '\nnaailes aangeboden', 'gezellig nieuw naai atelier in Kortrijk biedt les om ritsen te vervangen en andere kleine reparaties uit te voeren.', 1369317085, 'img', '', 0, 0, 0, 0, 0, 2, 'details', '', 50, 25, 25, 0, 1, 0),
(7, 6, 1, '\ncreatieve naaisters gezocht', 'wij zijn nu op zoek naar creatieve naaisters die scholieren wil begeleiden met handwerk. project hergebruikbaar textiel.', 1369317312, 'img', '', 0, 0, 0, 0, 0, 10, '{"title":"dfjhlkml","location":"jmkl","timingstart":"jklj","timingstop":"klj","timingfree":"free","body":"klj","types":"","fitness":"mljmlkj","development":"ljk","social":"klj","green":"klj","credits":"lkj","img":"","owaesadd":"opslaan"}', '', 25, 25, 25, 25, 0, 0),
(8, 1, 1, 'gezocht: autowasser', 'Is er iemand bereid om mijn auto te wassen? Bij voorkeur van binnen en van buiten, maar alleen van buiten kan ook.\n\nHet kan hier (heb alleen geen buitenkraan), maar kan ook naar je toe komen.', 1369317327, 'img', '', 0, 0, 0, 0, 0, 2, 'details', '', 0, 100, 0, 0, 1, 0),
(28, 5, 1, 'qdsf', '', 1378210842, 'img', 'fixed', 0, 0, 1378251000, 1378213200, 0, 0, 'details', '', 25, 25, 25, 25, 1, 0),
(9, 2, 0, 'stoomreiniger', 'Vanmorgen heb ik bij de Aldi een draagbare stoomreiniger gekocht. Als het goed bevalt zal ik hem in een "uitleenadvertentie" zetten. Ik heb een maand, als ik hem evt. nog terug wil brengen.\nWie wil dat ding samen met mij uitproberen ??', 1369317991, 'img', '', 0, 0, 0, 0, 0, 1, '{"key":"80d4e6aa3dfa9920131e1e0e71c36a0a","title":"test","location":"","timingstart":"","timingstop":"","body":"","fitness":"","development":"","social":"","green":"","credits":"","img":"","owaesadd":"opslaan"}', '', 75, 0, 25, 0, 0, 0),
(10, 3, 0, '\nInstalleren pc & printer/scan-apparaat', 'Ik heb een desktop (Windows XP, al enkele jaren buiten gebruik) en een 3-in-1 scanner/printer/kopieerder.\nIn gebruik: laptopje met Windows 7, zo''n 11"inch: geen cd-romstation.\n\nHet 3-in-1 apparaat heb ik van mijn moeder, zij gebruikte hem bij een Windows-Millenium (?) desktop en had de indruk dat ie het niet deed met de nieuwe Windows-Vista desktop.\n\nWat wil ik weten:\n-Kan mijn desktop overweg met het 3-in-1 apparaat?\n-Is mijn desktop geschikt voor Office 2010? (heb ergens cd''s van Windows 2002/2003, dat doet/deed het wel)\n-Kan mijn laptopje overweg met 3-in-1 of: zou een nieuwere desktop (Windows 8?) ermee overweg kunnen? (Kent iemand toevallig nog bedrijven die de oude pc''s weg doen voor symbolische bedragen? Zo ben ik aan de Windows XP gekomen) Mijn laptop is na ruim 3 jaar binnenkort misschien "op"...\n\nEr zitten (installatie)cd''s bij het 3-in-1 apparaat.\nOffice 2010 heb ik trouwens nog niet, weet niet of die systeemeisen te googelen zijn, zal ik komend weekend eens doen...\nOp mijn laptop heb ik Open Office, maar dat lijkt niet ideaal met excel.', 1369317994, 'img', '', 0, 0, 0, 0, 0, 2, '{"key":"80d4e6aa3dfa9920131e1e0e71c36a0a","title":"test","location":"","timingstart":"","timingstop":"","body":"","fitness":"","development":"","social":"","green":"","credits":"","img":"","owaesadd":"opslaan"}', '', 0, 25, 50, 25, 0, 0),
(11, 4, 0, 'Computerhulp', 'Als je problemen hebt met je computer of graag iets wil leren ben ik beschikbaar. Ik kan hulp geven met windows, mac en linux computers.\n\nIk ben zelf programmeur en vanwege mijn handicap leef ik mijn hele leven op het internet. Als vrienden hulp nodig hebben met hun computer kijken ze altijd naar mij. Ik vind het leuk om te doen en kan dingen op een rustige en simpele manier uitleggen.\n\nAls het om een laptop gaat zou je bij mij aan huis kunnen komen, anders is het telefonisch of via skype ook mogelijk. Ik kan helaas niet naar je toe komen omdat ik bedlegerig ben. Ik kan om die reden ook niet helpen met het verwijderen van virussen.\n\nVoorbeelden van dingen waar ik in het verleden mensen bij geholpen heb:\n-Het installeren van een virusscanner\n-Ik wil graag pdf bijlages kunnen lezen\n-Ik vergeet mijn wachtwoorden altijd\n-Ik wil zelf muziek op mijn mp3 speler kunnen zetten\n-Ik wil zelf foto''s van mijn camera kunnen halen\n-Ik wil skype of msn leren gebruiken\n-Ik wil internet telefonie\n-Ik wil mijn webcam leren gebruiken\n-Het instellen van een email programma\n-Ik wil graag een facebook account\n-Ik wil foto''s op facebook kunnen plaatsen\n-Hoe vind ik dingen op het internet?\n-Ik wil graag films en muziek downloaden\n-Ik wil filmpjes van youtube downloaden\n-Ik maak me zorgen om mijn privacy, wat kan ik hier zelf aan doen?\n-Ik ben overgestapt op een ander besturingssysteem, hoe werkt alles hier?\n\nGeen vraag is te makkelijk, maar ik kan ook helpen met moeilijkere onderwerpen zoals het maken van een eigen website of 3d modellen ontwerpen, of bijvoorbeeld ''ik wil leren programmeren''.\n\nOok kan ik helpen als je vanwege een beperking problemen hebt met het gebruiken van je computer.', 1369318053, 'img', '', 0, 0, 0, 0, 0, 0, '{"key":"ff01e48dfc260e343334bea0a09eb503","title":"testnieuw","location":"","timingstart":"","timingstop":"","body":"","fitness":"","development":"","social":"","green":"","credits":"","img":"","owaesadd":"opslaan"}', 'ff01e48dfc260e343334bea0a09eb503', 25, 25, 0, 50, 0, 0),
(16, 16, 0, 'Cursus assertiviteit', 'Assertiviteit zorgt er voor dat je kunt opkomen voor jezelf. Dit kun je leren! Dan kom je in de juiste omstandigheden op een goede manier over, kun je probleemloos je mening zeggen en kun je ook conflicten grondig aanpakken.\r\n\r\nIedereen voelt zich wel eens verlegen. Je gaat naar een feestje met veel onbekenden, je moet praten voor een groep, je chef vroeg je om een dossier meer toe te lichtenâ€¦ Onzekerheid kan verlammend werken. Deze cursus richt zich tot iedereen die voor een uitdaging staat en soms al te zeer twijfelt aan zijn capaciteiten. Ook wie graag een leidinggevende functie ambieert, kan met deze cursus meer inzicht verwerven.', 1372165141, 'img', '', 0, 0, 0, 0, 0, 2, 'details', '', 0, 25, 0, 75, 1, 0),
(18, 21, 0, 'Cursus Wiskunde', 'cursus wiskunde ... ', 1372254902, 'img', 'fixed', 0, 0, 0, 0, 0, 36, 'details', '', 25, 25, 25, 25, 0, 0),
(19, 5, 0, 'test', 'test', 1372402277, 'img', 'fixed', 0, 0, 0, 0, 0, 12, 'details', '', 25, 25, 50, 0, 0, 0),
(20, 5, 0, 'test', 'test', 1372402303, 'img', 'fixed', 0, 0, 0, 0, 0, 12, 'details', '', 25, 25, 0, 50, 0, 0),
(21, 5, 0, 'test', 'test', 1372402786, 'img', 'fixed', 0, 0, 1371016800, 1371037800, 0, 12, 'details', '', 100, 0, 0, 0, 0, 0),
(22, 5, 0, 'werker', 'dsff sqdf ', 1372402887, 'img', '', 0, 0, 0, 0, 0, 5, 'details', '', 25, 25, 25, 25, 1, 0),
(23, 5, 1, 'werker', 'dsff sqdf ', 1372403057, 'img', '', 0, 0, 0, 0, 0, 5, 'details', '', 50, 0, 50, 0, 1, 0),
(24, 5, 1, 'werker', 'dsff sqdf ', 1372403227, 'img', '', 0, 0, 0, 0, 0, 5, 'details', '', 75, 0, 0, 25, 1, 0),
(25, 5, 1, 'qdsf', 'dfsqf', 1372403369, 'img', '', 0, 0, 0, 0, 0, 0, 'details', '', 25, 50, 25, 0, 1, 0),
(26, 5, 0, 'ik bied iets aan', '', 1373376610, 'img', '', 0, 0, 0, 0, 0, 0, 'details', '', 25, 25, 25, 25, 1, 0),
(27, 5, 1, 'ik vraag iets / iemand', '', 1373376624, 'img', '', 0, 0, 0, 0, 0, 0, 'details', '', 25, 25, 25, 25, 1, 0),
(29, 5, 0, 'Kortrijkse test ', 'in GKG om 12/09/2013 16:30 , creatief, 33 credits, 25, 50, 25, 0', 1378370839, 'img', 'fixed', 51, 3, 1378996200, 1379010600, 0, 0, 'details', '', 25, 50, 25, 0, 0, 0),
(30, 5, 1, 'dfs', '12/09/2013 08:30 tot 4 uur later, 52 credits\r\n50, 25, 0, 25', 1378381052, 'img', 'fixed', 50.824629, 3.249539, 1378967400, 1378981800, 0, 0, 'details', '', 50, 25, 0, 25, 0, 0),
(31, 5, 1, 'dfs', '12/09/2013 08:30 tot 4 uur later, 52 credits\r\n50, 25, 0, 25', 1378381169, 'img', 'fixed', 50.824629, 3.249539, 1378967400, 1378981800, 0, 0, 'details', '', 50, 25, 0, 25, 0, 0),
(32, 5, 1, 'dfs', '12/09/2013 08:30 tot 4 uur later, 52 credits\r\n50, 25, 0, 25', 1378381178, 'img', 'fixed', 50.824629, 3.249539, 1378967400, 1378981800, 0, 0, 'details', '', 50, 25, 0, 25, 0, 0),
(33, 5, 1, 'dfs', '12/09/2013 08:30 tot 4 uur later, 52 credits\r\n50, 25, 0, 25', 1378381200, 'img', 'fixed', 50.824629, 3.249539, 1378967400, 1378981800, 0, 0, 'details', '', 50, 25, 0, 25, 0, 0),
(34, 5, 1, 'fg', '4 uur in ieper', 1378381289, 'img', 'fixed', 50.85, 2.8833333, 1378382400, 1378386000, 0, 12, 'details', '', 25, 25, 25, 25, 1, 0),
(35, 5, 1, 'dsqf', 'dsqf', 1378381452, 'img', 'free', 0, 0, 1378382400, 1378386000, 0, 0, 'details', '', 50, 25, 25, 0, 1, 0),
(36, 5, 0, 'Test', '4 uur thuis, 45 credits, 75 0 25 0', 1378381592, 'img', 'fixed', 50.8570623, 2.892535, 1378382400, 1378386000, 0, 0, 'details', '', 75, 0, 25, 0, 0, 0),
(37, 5, 0, 'Test', '4 uur thuis, 45 credits, 75 0 25 0', 1378382240, 'img', 'fixed', 50.8570623, 2.892535, 1378382400, 1378386000, 0, 0, 'details', '', 75, 0, 25, 0, 0, 0),
(38, 5, 0, 'ik bied iets aan', '3 uur, 43 credits', 1378382397, 'img', 'fixed', 50.8570623, 2.892535, 1378382400, 1378386000, 0, 43, 'details', '', 0, 25, 50, 25, 0, 0),
(39, 5, 0, 'ik bied iets aan', '3 uur, 43 credits', 1378383609, 'img', 'fixed', 50.8570623, 2.892535, 0, 0, 3, 43, 'details', '', 0, 25, 50, 25, 1, 0),
(40, 5, 0, 'Titel', 'test', 1378467351, 'img', 'free', 51.053468, 3.73038, 0, 0, 1, 0, 'details', '', 50, 25, 25, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblMarketSubscriptions`
--

CREATE TABLE IF NOT EXISTS `tblMarketSubscriptions` (
  `id` bigint(20) NOT NULL auto_increment,
  `user` bigint(20) NOT NULL,
  `doneby` bigint(20) NOT NULL,
  `market` bigint(20) NOT NULL,
  `mtype` bigint(20) NOT NULL,
  `clickdate` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=328 ;

--
-- Dumping data for table `tblMarketSubscriptions`
--

INSERT INTO `tblMarketSubscriptions` (`id`, `user`, `doneby`, `market`, `mtype`, `clickdate`) VALUES
(1, 1, 1, 10, 0, 0),
(2, 1, 1, 10, 0, 0),
(3, 1, 1, 10, 0, 0),
(4, 1, 1, 10, 0, 0),
(5, 1, 1, 10, 0, 0),
(6, 1, 1, 10, 1, 0),
(7, 1, 1, 10, 0, 0),
(8, 1, 1, 10, 0, 0),
(9, 1, 1, 10, 0, 0),
(10, 1, 1, 10, 0, 0),
(11, 1, 1, 10, 1, 0),
(12, 1, 1, 14, 0, 0),
(13, 1, 1, 14, 1, 0),
(14, 1, 1, 14, 0, 0),
(15, 1, 1, 14, 1, 0),
(16, 1, 1, 8, 0, 0),
(17, 1, 1, 7, 0, 0),
(18, 1, 1, 8, 1, 0),
(19, 1, 1, 8, 0, 0),
(20, 1, 1, 13, 0, 0),
(21, 1, 1, 13, 0, 0),
(22, 1, 1, 13, 1, 0),
(23, 1, 1, 13, 0, 0),
(24, 1, 1, 13, 0, 0),
(25, 1, 1, 12, 0, 0),
(26, 1, 1, 12, 0, 0),
(27, 1, 1, 12, 1, 0),
(28, 1, 1, 13, 1, 0),
(29, 1, 1, 13, 0, 0),
(30, 1, 1, 13, 1, 0),
(31, 1, 1, 9, 0, 0),
(32, 1, 1, 9, 0, 0),
(33, 1, 1, 9, 0, 0),
(34, 1, 1, 9, 0, 0),
(35, 1, 1, 9, 1, 0),
(36, 1, 1, 9, 0, 0),
(37, 1, 1, 9, 1, 0),
(38, 1, 1, 14, 0, 0),
(39, 1, 1, 14, 1, 0),
(40, 17, 17, 14, 0, 0),
(41, 17, 17, 14, 0, 0),
(42, 17, 17, 14, 1, 0),
(43, 17, 17, 13, 0, 0),
(44, 17, 17, 13, 0, 0),
(45, 17, 17, 13, 0, 0),
(46, 17, 17, 11, 0, 0),
(47, 17, 17, 11, 1, 0),
(48, 17, 17, 11, 0, 0),
(49, 17, 17, 12, 1, 0),
(50, 17, 17, 12, 1, 0),
(51, 17, 17, 12, 1, 0),
(52, 17, 17, 12, 1, 0),
(53, 17, 17, 12, 1, 0),
(54, 17, 17, 12, 1, 0),
(55, 17, 17, 12, 1, 0),
(56, 17, 17, 12, 1, 0),
(57, 17, 17, 12, 1, 0),
(58, 17, 17, 14, 0, 0),
(59, 17, 17, 14, 1, 0),
(60, 17, 17, 8, 0, 0),
(61, 17, 17, 8, 0, 0),
(62, 17, 17, 8, 0, 0),
(63, 17, 17, 8, 1, 0),
(64, 17, 17, 8, 0, 0),
(65, 17, 17, 9, 1, 0),
(66, 17, 17, 8, 0, 0),
(67, 17, 17, 8, 1, 0),
(68, 17, 17, 8, 1, 0),
(69, 17, 17, 8, 0, 0),
(70, 17, 17, 7, 0, 0),
(71, 17, 17, 7, 0, 0),
(72, 17, 17, 7, 0, 0),
(73, 17, 17, 7, 0, 0),
(74, 17, 17, 7, 0, 0),
(75, 17, 17, 7, 1, 0),
(76, 17, 17, 4, 0, 0),
(77, 17, 17, 4, 1, 0),
(78, 17, 17, 4, 0, 0),
(79, 17, 17, 4, 1, 0),
(80, 17, 17, 4, 0, 0),
(81, 17, 17, 10, 0, 0),
(82, 17, 17, 10, 0, 0),
(83, 17, 17, 10, 0, 0),
(84, 17, 17, 10, 0, 0),
(85, 17, 17, 10, 0, 0),
(86, 17, 17, 10, 0, 0),
(87, 17, 17, 10, 1, 0),
(88, 17, 17, 10, 0, 0),
(89, 17, 17, 4, 0, 0),
(90, 17, 17, 4, 0, 0),
(91, 17, 17, 4, 0, 0),
(92, 17, 17, 4, 0, 0),
(93, 17, 17, 4, 0, 0),
(94, 17, 17, 4, 0, 0),
(95, 17, 17, 4, 0, 0),
(96, 17, 17, 4, 0, 0),
(97, 17, 17, 4, 0, 0),
(98, 17, 17, 4, -1, 0),
(99, 17, 17, 4, 0, 0),
(100, 17, 17, 4, -1, 0),
(101, 17, 17, 4, 1, 0),
(102, 17, 17, 4, 0, 0),
(103, 17, 17, 4, 1, 0),
(104, 17, 17, 4, 0, 0),
(105, 17, 17, 4, -1, 0),
(106, 17, 17, 10, -1, 0),
(107, 17, 17, 10, 1, 0),
(108, 17, 17, 4, 0, 0),
(109, 17, 17, 3, 1, 0),
(110, 17, 17, 3, 1, 0),
(111, 17, 17, 8, -1, 0),
(112, 17, 17, 7, -1, 0),
(113, 17, 17, 7, -1, 0),
(114, 17, 17, 4, -1, 0),
(115, 17, 17, 4, -1, 0),
(116, 17, 17, 4, -1, 0),
(117, 17, 17, 4, -1, 0),
(118, 17, 17, 4, 1, 0),
(119, 17, 17, 7, 0, 0),
(120, 17, 17, 7, 0, 0),
(121, 17, 17, 7, 0, 0),
(122, 17, 17, 7, 0, 0),
(123, 17, 17, 7, 0, 0),
(124, 17, 17, 7, 0, 0),
(125, 17, 17, 8, 0, 0),
(126, 17, 17, 8, -1, 0),
(127, 17, 17, 8, 0, 0),
(128, 17, 17, 8, -1, 0),
(129, 17, 17, 8, 0, 0),
(130, 17, 17, 8, -1, 0),
(131, 17, 17, 8, 0, 0),
(132, 17, 17, 8, -1, 0),
(133, 17, 17, 8, 0, 0),
(134, 17, 17, 8, -1, 0),
(135, 17, 17, 8, 1, 0),
(136, 17, 17, 8, -1, 0),
(137, 1, 1, 8, 1, 0),
(138, 1, 1, 1, 0, 0),
(139, 1, 1, 1, 0, 0),
(140, 1, 1, 1, 1, 0),
(141, 1, 1, 1, 0, 0),
(142, 1, 1, 1, 1, 0),
(143, 1, 1, 7, 1, 0),
(144, 1, 1, 7, 0, 0),
(145, 1, 1, 7, -1, 0),
(146, 1, 1, 4, 0, 0),
(147, 1, 1, 4, 1, 0),
(148, 1, 1, 4, 0, 0),
(149, 1, 1, 4, -1, 0),
(150, 1, 1, 2, 0, 0),
(151, 1, 1, 2, -1, 0),
(152, 1, 1, 2, 1, 0),
(153, 1, 1, 2, -1, 0),
(154, 1, 1, 2, 0, 0),
(155, 1, 1, 14, -1, 0),
(156, 1, 1, 13, -1, 0),
(157, 1, 1, 13, -1, 0),
(158, 1, 1, 12, -1, 0),
(159, 1, 1, 12, -1, 0),
(160, 1, 1, 12, -1, 0),
(161, 1, 1, 12, -1, 0),
(162, 5, 5, 8, 0, 0),
(163, 5, 5, 8, 1, 0),
(164, 5, 5, 8, -1, 0),
(165, 5, 5, 7, 0, 0),
(166, 8, 8, 24, 0, 0),
(167, 1, 1, 6, 1, 0),
(168, 1, 1, 6, 0, 0),
(169, 1, 1, 6, 1, 0),
(170, 1, 1, 3, 1, 0),
(171, 1, 1, 3, 0, 0),
(172, 1, 1, 3, 1, 0),
(173, 1, 1, 3, -1, 0),
(174, 1, 1, 4, 0, 0),
(175, 1, 1, 4, 1, 0),
(176, 1, 1, 11, 1, 0),
(177, 1, 1, 11, 0, 0),
(178, 1, 1, 11, 1, 0),
(179, 1, 1, 11, 0, 0),
(180, 1, 1, 10, 0, 0),
(181, 1, 1, 10, 1, 0),
(182, 1, 1, 10, 0, 0),
(183, 1, 1, 10, -1, 0),
(184, 1, 1, 10, 0, 0),
(185, 1, 1, 10, 1, 0),
(186, 1, 1, 10, 0, 0),
(187, 1, 1, 10, -1, 0),
(188, 1, 1, 7, 0, 0),
(189, 1, 1, 7, 1, 0),
(190, 18, 18, 1, 1, 0),
(191, 1, 1, 16, 0, 0),
(192, 1, 1, 16, 1, 0),
(193, 1, 1, 7, 0, 0),
(194, 1, 1, 4, 0, 0),
(195, 1, 1, 4, 1, 0),
(196, 20, 20, 8, 0, 0),
(197, 20, 20, 8, 1, 0),
(198, 5, 5, 17, 0, 0),
(199, 21, 21, 8, 0, 0),
(200, 21, 21, 8, 1, 0),
(201, 21, 21, 8, 0, 0),
(202, 1, 1, 18, 0, 0),
(203, 1, 1, 22, 0, 0),
(204, 1, 1, 24, 0, 0),
(205, 1, 1, 25, 0, 0),
(206, 1, 1, 25, 0, 0),
(207, 1, 1, 23, 0, 0),
(208, 1, 1, 23, 0, 0),
(209, 1, 1, 23, 0, 0),
(210, 1, 1, 23, 0, 0),
(211, 1, 1, 23, 1, 0),
(212, 1, 1, 20, 0, 0),
(213, 1, 1, 20, 0, 0),
(214, 1, 1, 20, 0, 0),
(215, 1, 1, 20, 0, 0),
(216, 1, 1, 20, 0, 0),
(217, 1, 1, 20, 0, 0),
(218, 1, 1, 20, 0, 0),
(219, 1, 1, 20, 0, 0),
(220, 1, 1, 20, 0, 0),
(221, 1, 1, 19, 1, 0),
(222, 1, 1, 23, 0, 0),
(223, 1, 1, 23, 1, 0),
(224, 1, 1, 23, 0, 0),
(225, 1, 1, 23, 1, 0),
(226, 18, 18, 23, 1, 0),
(227, 18, 18, 23, 0, 0),
(228, 1, 1, 23, 0, 0),
(229, 1, 1, 25, 1, 0),
(230, 1, 1, 25, 0, 0),
(231, 1, 1, 25, 1, 1373017221),
(232, 1, 1, 25, -1, 1373017222),
(233, 1, 1, 25, 1, 1373017329),
(234, 1, 1, 25, 0, 1373017348),
(235, 0, 1, 8, -1, 1373018615),
(236, 1, 1, 8, -1, 1373018726),
(237, 20, 1, 8, -1, 1373018737),
(238, 21, 1, 8, -1, 1373027188),
(239, 1, 1, 1, 2, 1373292135),
(240, 18, 1, 1, 2, 1373292147),
(241, 5, 1, 8, -1, 1373292394),
(242, 1, 5, 24, 2, 1373295127),
(243, 1, 5, 24, 2, 1373295174),
(244, 1, 5, 24, 2, 1373295244),
(245, 1, 5, 24, 2, 1373295284),
(246, 1, 5, 24, 2, 1373295306),
(247, 1, 5, 24, 2, 1373356767),
(248, 0, 0, 24, 2, 1373360986),
(249, 0, 0, 24, 2, 1373361034),
(250, 0, 0, 24, 2, 1373361091),
(251, 0, 0, 24, 2, 1373361107),
(252, 0, 0, 24, 2, 1373361128),
(253, 0, 0, 24, 2, 1373361197),
(254, 0, 0, 24, 2, 1373361251),
(255, 0, 0, 24, 2, 1373361267),
(256, 0, 0, 24, 2, 1373361293),
(257, 0, 0, 24, 2, 1373361340),
(258, 0, 0, 24, 2, 1373361375),
(259, 1, 5, 25, -1, 1373361413),
(260, 1, 5, 22, 2, 1373363603),
(261, 0, 5, 24, -1, 1373363613),
(262, 1, 5, 24, 2, 1373363617),
(263, 18, 5, 23, -1, 1373363632),
(264, 1, 5, 23, 2, 1373363635),
(265, 5, 5, 8, 0, 1373364024),
(266, 5, 5, 8, 1, 1373364025),
(267, 5, 5, 8, -1, 1373364026),
(268, 5, 5, 8, 0, 1373364027),
(269, 5, 5, 8, -1, 1373364028),
(270, 5, 5, 7, 1, 1373364030),
(271, 5, 5, 7, 1, 1373364030),
(272, 5, 5, 7, 1, 1373364030),
(273, 5, 5, 7, 1, 1373364030),
(274, 5, 5, 7, 1, 1373364030),
(275, 5, 5, 7, 1, 1373364030),
(276, 5, 5, 7, -1, 1373364032),
(277, 5, 5, 7, 1, 1373364032),
(278, 5, 5, 8, 1, 1373368065),
(279, 1, 5, 24, 2, 1373368079),
(280, 1, 5, 6, 2, 1373446487),
(281, 5, 1, 8, 2, 1373446526),
(282, 18, 1, 1, 2, 1373446577),
(283, 5, 5, 4, 0, 1373616689),
(284, 5, 5, 4, 1, 1373616728),
(285, 5, 5, 4, 0, 1373616728),
(286, 5, 5, 4, -1, 1373616849),
(287, 5, 5, 7, 0, 1373618465),
(294, 5, 5, 16, 1, 0),
(295, 18, 18, 16, 1, 0),
(304, 18, 16, 16, -1, 1373623399),
(302, 1, 16, 16, 2, 1373623399),
(303, 5, 16, 16, 2, 1373623399),
(305, 1, 1, 27, 0, 1374226001),
(306, 1, 5, 27, 2, 1374226011),
(307, 1, 1, 26, 0, 1374226196),
(308, 1, 5, 26, 2, 1374226200),
(309, 1, 1, 25, 0, 1374226358),
(310, 1, 5, 25, 2, 1374226362),
(311, 18, 18, 21, 0, 1374526952),
(312, 1, 1, 21, 0, 1377780930),
(313, 1, 1, 35, 0, 1378387304),
(314, 1, 5, 35, 2, 1378387332),
(315, 1, 1, 34, 0, 1378387874),
(316, 1, 5, 34, 2, 1378387879),
(317, 1, 1, 40, 0, 1378467400),
(318, 1, 5, 40, -1, 1378467416),
(319, 1, 1, 33, 0, 1378467457),
(320, 1, 1, 28, 0, 1378467476),
(321, 1, 1, 28, -1, 1378467791),
(322, 1, 1, 28, 0, 1378467795),
(323, 1, 5, 28, 2, 1378467926),
(324, 1, 1, 39, 0, 1378468104),
(325, 16, 16, 39, 0, 1378468148),
(326, 1, 5, 39, 2, 1378469388),
(327, 16, 5, 39, -1, 1378469388);

-- --------------------------------------------------------

--
-- Table structure for table `tblTransactions`
--

CREATE TABLE IF NOT EXISTS `tblTransactions` (
  `id` bigint(20) NOT NULL auto_increment,
  `date` bigint(20) NOT NULL,
  `initiator` bigint(20) NOT NULL,
  `sender` bigint(20) NOT NULL,
  `scoresender` int(11) NOT NULL,
  `receiver` bigint(20) NOT NULL,
  `scorereceiver` int(11) NOT NULL,
  `sendersigned` bigint(20) unsigned NOT NULL default '0',
  `receiversigned` bigint(20) unsigned NOT NULL default '0',
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
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

--
-- Dumping data for table `tblTransactions`
--

INSERT INTO `tblTransactions` (`id`, `date`, `initiator`, `sender`, `scoresender`, `receiver`, `scorereceiver`, `sendersigned`, `receiversigned`, `number`, `physical`, `mental`, `emotional`, `social`, `status`, `info`, `publicfeedbacktosender`, `privatefeedbacktosender`, `publicfeedbacktoreceiver`, `privatefeedbacktoreceiver`, `market`, `code`) VALUES
(31, 1377181695, 5, 5, 0, 8, 5, 1377183018, 0, 5, 75, 0, 0, 25, 0, 'update', '', '', 'Publieke feedback 5 naar 8 ', 'Persoonlijke feedback 5 naar 8 ', 24, '3f6f663e92d0f09381cb4509b9d0ceab'),
(32, 1377183110, 5, 5, 5, 1, 0, 1377183679, 1377183679, 5, 75, 0, 0, 25, 20, 'update', '', '', '', '', 24, '252cac94cc39bf6d3feb334d0414dfc7'),
(33, 1378388226, 1, 5, 0, 1, 5, 1378388244, 1378388244, 12, 25, 25, 25, 25, 20, 'update', 'publiek', 'publiek', '', '', 34, '8edc025d1a95e1b3858ab84c3673859c'),
(34, 1378467862, 5, 1, 0, 5, 5, 1378467869, 1378467869, 2, 50, 25, 25, 0, 20, 'update', '', '', '', '', 6, 'efd0a9b0e139c3da27122a6a78a27f1f');

-- --------------------------------------------------------

--
-- Table structure for table `tblUserBadges`
--

CREATE TABLE IF NOT EXISTS `tblUserBadges` (
  `id` bigint(20) NOT NULL auto_increment,
  `user` bigint(20) NOT NULL,
  `badge` bigint(20) NOT NULL,
  `date` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `tblUserBadges`
--

INSERT INTO `tblUserBadges` (`id`, `user`, `badge`, `date`) VALUES
(1, 1, 1, 0),
(2, 2, 2, 0),
(3, 3, 3, 0),
(4, 4, 4, 0),
(5, 5, 5, 0),
(6, 6, 6, 0),
(7, 7, 7, 0),
(8, 7, 6, 0),
(9, 5, 4, 0),
(10, 3, 2, 0),
(11, 1, 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblUserCertificates`
--

CREATE TABLE IF NOT EXISTS `tblUserCertificates` (
  `id` bigint(20) NOT NULL auto_increment,
  `user` bigint(20) NOT NULL,
  `certificate` bigint(20) NOT NULL,
  `date` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `tblUserCertificates`
--

INSERT INTO `tblUserCertificates` (`id`, `user`, `certificate`, `date`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 0),
(3, 3, 1, 0),
(4, 4, 1, 0),
(5, 5, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblUserRecover`
--

CREATE TABLE IF NOT EXISTS `tblUserRecover` (
  `id` bigint(20) NOT NULL auto_increment,
  `user` bigint(20) NOT NULL,
  `timeasked` bigint(20) NOT NULL,
  `timeexpires` bigint(20) NOT NULL,
  `ipasked` varchar(25) NOT NULL,
  `conf` text NOT NULL,
  `passcode` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tblUserRecover`
--

INSERT INTO `tblUserRecover` (`id`, `user`, `timeasked`, `timeexpires`, `ipasked`, `conf`, `passcode`) VALUES
(1, 5, 1370872378, 1370873278, '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0', '7c8f169b0807e2eeab04f6bf36382709'),
(2, 5, 1370872514, 1370873414, '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0', 'dc0bf47a2c7ec0eeab5ba955fe3867ca'),
(3, 7, 1370872521, 1370873421, '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0', '641bb6658b7e45407814a09cac046808'),
(4, 1, 1370875158, 1370876058, '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0', '8e0edffbbb9146b3a719191298036e9c'),
(5, 1, 1370876167, 1370877067, '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0', '5830e21969c07ca5ff7411058d1287da'),
(6, 1, 1370876869, 1370877769, '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0', '4af454299fb39cbbd4a5170d192da62c'),
(7, 1, 1370876885, 1370877785, '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0', 'a5b408b7c2ee870376c848272cea2bb3'),
(8, 1, 1370876976, 1370877876, '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0', '8594fe560fa0df9f607960615adf7cbe');

-- --------------------------------------------------------

--
-- Table structure for table `tblUsers`
--

CREATE TABLE IF NOT EXISTS `tblUsers` (
  `id` bigint(20) NOT NULL auto_increment,
  `alias` varchar(32) NOT NULL,
  `login` varchar(125) NOT NULL,
  `pass` varchar(256) NOT NULL,
  `firstname` varchar(125) NOT NULL,
  `lastname` varchar(125) NOT NULL,
  `mail` varchar(125) NOT NULL,
  `description` text NOT NULL,
  `img` varchar(128) NOT NULL,
  `physical` bigint(20) NOT NULL default '0',
  `mental` bigint(20) NOT NULL default '0',
  `emotional` bigint(20) NOT NULL default '0',
  `social` bigint(20) NOT NULL default '0',
  `deleted` bit(1) NOT NULL default b'0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `tblUsers`
--

INSERT INTO `tblUsers` (`id`, `alias`, `login`, `pass`, `firstname`, `lastname`, `mail`, `description`, `img`, `physical`, `mental`, `emotional`, `social`, `deleted`) VALUES
(1, 'benediktbeun', 'beuntje', '4302e5b63797f920e8f5852fb4fb3a02', 'Alexander', 'Beun', 'benedikt@beuntje.com', 'Extra info over mij', 'a968da0c59bf39309b47ca1d284a511e.png', 35, 73, 63, 97, b'0'),
(2, 'marcel_dewilde', 'test', '', 'Marcel', 'Dewilde', 'jdlkjdqslkfjq@test.com', 'Extra info over tester', '', 94, 80, 18, 48, b'0'),
(3, 'mark_vandamme', 'test', '098f6bcd4621d373cade4e832627b4f6', 'Mark', 'Van Damme', 'test@test.be', '', 'mark70.jpg', 87, 90, 91, 83, b'0'),
(4, 'frederik_vanderghote', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Frederik', 'Vanderghote', 'admin@beuntje.com', '', 'frederik70.jpg', 43, 65, 98, 93, b'0'),
(5, 'els_feys', 'a', '0cc175b9c0f1b6a831c399e269772661', 'Els', 'Feys', 'admin@beuntje.com', '', '2f51e38ebdf4161a27f99d7326b58540.jpg', 74, 88, 19, 30, b'0'),
(6, 'janne_deraedt', 't', 'e358efa489f58062f10dd7316b65649e', 'Janne', 'Deraedt', 'benedict@beuntje.com', '', 'janne70.jpg', 95, 83, 30, 3, b'0'),
(7, 'nadine_vitse', 'j', '363b122c528f54df4a0446b6bab05515', 'Nadine', 'Vitse', 'benedikte@beuntje.com', '', '', 26, 19, 17, 26, b'0'),
(8, '', 'd', '8277e0910d750195b448797616e091ad', 'Jan', 'Van Den Weghe', 'benedikt.beun@beuntje.com', '', '', 82, 30, 3, 28, b'0'),
(9, '', 't', 'e358efa489f58062f10dd7316b65649e', 'Bart', 'Wouters', 'benedikt@wavingwhale.be', '', '', 30, 68, 47, 34, b'0'),
(11, '', 'jlkjlj', '4302e5b63797f920e8f5852fb4fb3a02', 'Marcel', 'Blomme', 'jdlkjdqsdqfdsfslkfjq@test.com', '', '', 28, 36, 97, 78, b'0'),
(15, 'tvh', 'tvh', 'f09023f4126fae1d6c4cb8afd05b3613', '', 'TVH', 'erlkjamlkezrj@test.com', 'Sinds de oprichting in 1969 is TVH - Group Thermote & Vanhalst gekend als een firma met een passie voor heftrucks, hoogwerkers en industriÃ«le in-plant voertuigen. Voor meer dan 20 000 klanten, in ruim 160 landen, zoekt en biedt TVH de beste oplossing. Als ''s werelds grootste one-stop-shop zorgt het bedrijf ervoor dat alles binnen de kortst mogelijke tijd bij u geleverd wordt.TVH is onderverdeeld in 5 verschillende divisies, met elk hun eigen specialisaties: ', '5032fc73c0f9ae6d176679f06d1d3aef.jpg', 100, 64, 21, 14, b'0'),
(16, 'ocmw', 'ocmw', '0a1587d74d7899556ba12b0b4bfd0e41', '', 'OCMW', 'ezarjlkm@test.com', 'Een OCMW, voluit ''Openbaar centrum voor maatschappelijk welzijn'', verzekert een aantal maatschappelijke dienstverleningen en zorgt zo voor het welzijn van iedere burger. Elke gemeente of stad heeft een eigen OCMW dat een brede waaier aan diensten aanbiedt.', '70bdb24044e31de53307d6490db5c6ea.jpg', 8, 95, 51, 73, b'0'),
(17, 'a_a', '', 'd41d8cd98f00b204e9800998ecf8427e', 'Ludo', 'Crevits', '', '', '', 10, 30, 21, 13, b'0'),
(18, 'geert_hofman', 'GeertHa', 'a849191c4756a6f8e7a97284f49f9248', 'Geert', 'Hofman', 'geert.hofman@howest.be', '', '', 4, 79, 85, 86, b'0'),
(21, 'benedikt_beun', 'benediktbeun', '4302e5b63797f920e8f5852fb4fb3a02', 'Benedikt', 'Beun', 'benedikt.beun@howest.be', '', '', 75, 16, 57, 37, b'0'),
(22, 'kurt_callewaert', 'kurt', '607bd9e56b03d15a257732e044793ff9', 'Kurt', 'Callewaert', 'kurt.callewaert@howest.be', '', '', 0, 0, 0, 0, b'0');

-- --------------------------------------------------------

--
-- Table structure for table `tblUserSessions`
--

CREATE TABLE IF NOT EXISTS `tblUserSessions` (
  `id` bigint(20) NOT NULL auto_increment,
  `user` bigint(20) NOT NULL,
  `start` bigint(20) NOT NULL,
  `stop` bigint(20) NOT NULL,
  `sessionpass` varchar(128) NOT NULL,
  `active` decimal(10,0) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `conf` varchar(256) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=230 ;

--
-- Dumping data for table `tblUserSessions`
--

INSERT INTO `tblUserSessions` (`id`, `user`, `start`, `stop`, `sessionpass`, `active`, `ip`, `conf`) VALUES
(1, 0, 1369740446, 1370950046, '10fbcad36ae80a0dc4f0ce7f8312c2e5', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(2, 0, 1369740511, 1370950111, '3cef05a6a078f61f96f98ca5da5992f3', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(3, 0, 1369740623, 1370950223, '3d611f3d06e79e9e6ed82b7a945d85e2', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(4, 0, 1369740804, 1370950404, '2623cc9260e8e3117c5d9fa4971769f6', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(5, 0, 1369741042, 1370950642, '2d4a6ad410cf761bb4ee689c3d5b7bfa', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(6, 0, 1369741051, 1370950651, '26aa873063fd897ff32181f97eb215ed', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(7, 0, 1369741090, 1370950690, '3efc75705a4328152cc2141e00349f56', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(8, 0, 1369741198, 1370950798, '878cfed019131dc9b49dc3b5c6dd1bfd', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(9, 0, 1369741205, 1370950805, '1a6e9cf4d35f350659a03203bd0ac823', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(10, 0, 1369741214, 1370950814, '0d8e519d45bcd7d93fb2858832d048e6', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(11, 0, 1369741396, 1370950996, '42893f9fd013bb04453f97f5e68c3489', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(12, 0, 1369741488, 1370951088, '53164cb9a624c1fde997fab15d339871', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(13, 0, 1369741675, 1370951275, '7ce217f990d4eb03f724a678717aaf44', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(14, 0, 1369742943, 1370952543, '743ba4955147adeeb55a4deda552dac7', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(15, 0, 1369742968, 1370952568, '6fbb8bf44e785693ce6d0d34178d72e3', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(16, 1, 1369743034, 1370952634, '5da9dea5e5c15a5de6e3e5fd5df3e358', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(17, 1, 1369906439, 1371116039, 'a610cd56c3db53d8a542edc3b71588f5', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(18, 1, 1369906837, 1371116437, '5cee60c5735e4e76df79a1d30b877842', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(19, 1, 1369924113, 1371133713, '692178da78d5df5a45c80c2ab0f87d2a', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(20, 1, 1370253534, 1371463134, 'b997ec62d89be47d6fdaa7f5ceb99aa7', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(21, 1, 1370259540, 1371469140, '97ec23230ee0f22462c5a5c8898ade76', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(22, 1, 1370260920, 1371470520, 'fbaa4d440c31e1227685c54f054f33f9', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(23, 1, 1370268795, 1371478395, '4cfd101d98907655a8f9d58e54e5bf6f', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(24, 1, 1370328531, 1371538131, 'f70d8103ec952fbc9f1b1bc5786f6c33', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(25, 1, 1370338582, 1370348219, 'b2673dece2c5452319798d906bbce08e', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(26, 1, 1370348231, 1370348233, '717be726f148811960c1302d81657b94', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(27, 1, 1370348239, 1371557839, '34bfc84154f01799399bc1f5bbee9257', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(28, 1, 1370418723, 1371628323, '757f59b6a9df97575219c3e52ec1825a', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(29, 1, 1370426415, 1370433429, '2476068b2b55044e92c9b637ecada5cf', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(30, 1, 1370433442, 1370433448, '2f2bb28c5c3217c1c1b251fde9989cf3', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(31, 1, 1370433490, 1371643090, '79cc117df639b49d3bed1cbc63c81569', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(32, 1, 1370508163, 1371717763, '236f06378927d03028e51887632e997e', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(33, 1, 1370518157, 1370520207, 'c0c333d1bd7e0a320e4a2e8e7f685f2e', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(34, 1, 1370520211, 1370520242, 'e87b1d0f3319724f56872778dc697fcd', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(35, 1, 1370520246, 1371729846, 'd3f811da3d37bc4a037a3d17b8d35f6f', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(36, 1, 1370528312, 1370528321, '565a6adea6a2f25e06cc53d965e4c235', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'),
(37, 4, 1370595554, 1370595590, '7002f55821b2af43a4249acd685597f3', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(38, 5, 1370595607, 1371805207, '7a146a3ce4d11c49928475375a0a9e61', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(39, 5, 1370606195, 1371815795, 'ec78d2a7762986ef43c2a401017448cb', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(40, 1, 1370856635, 1372066235, '6c702845bfd7b51f60479dc6d7db8e83', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(41, 1, 1370856643, 1372066243, '98e5e83e79834f9cf93d256cdce1afd0', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(42, 5, 1370856651, 1372066251, '7797b3d6f1a78daab0146a9782b23490', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(43, 1, 1370857557, 1370857561, '01a512e5d8d3819b0d3dcd49070d5e43', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(44, 5, 1370857794, 1372067394, '2fbe4e74d9928caefe3fe68200a24dc4', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(45, 5, 1370863679, 1372073279, '13a15c2912ce431b62ca410bb44cd717', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(46, 5, 1370865200, 1370866757, '17636f7766d7f1cfbf2bdadc0411316e', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(47, 6, 1370866765, 1370867017, 'c4b3f25fe94ba2702175624855749620', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(48, 7, 1370867023, 1370867081, 'eb7957141dba234cbaa3c23d4bbde2d2', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(49, 5, 1370867084, 1370867087, '61dd713853a6def672efef289c258ad8', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(50, 5, 1370867092, 1370867104, '07d2488fc5dd6e70bc04df95b941cafc', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(51, 1, 1370876312, 1370876317, '35de98870d82b4071bb83361f0085c7d', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(52, 1, 1370876326, 1370876865, 'c5130af416943850b792f327e81db03c', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(53, 1, 1370877020, 1370877161, 'ec6301e862eadc812d4864662b09d375', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(54, 8, 1370877329, 1372086929, 'ecf03016ae7bd1182dd3ccaea7d623a1', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(55, 5, 1371193901, 1371196907, '84060b0e09499107ec725ced5278663e', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(56, 5, 1371196928, 1371196981, '8253833957c1a003eb7df38fc7e3ac8e', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(57, 6, 1371197113, 1371204512, '90b4da1776538aa91a83d5255d2d8c9a', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(58, 5, 1371197437, 1372407037, '583318efeeaa7bcc418e93a1db66137d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36'),
(59, 5, 1371204517, 1371204529, '44867810d3c7246c36232494d0c60317', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(60, 5, 1371207096, 1371208018, 'f4a1e925daba40abdf9e143a747f3f65', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(61, 5, 1371208029, 1372417629, '11b575d952c0ebe2f82f3cb90f9aab2d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(62, 5, 1371453114, 1371461023, 'a2ef764815e99247b619f5986c0e3743', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(63, 1, 1371461028, 1372670628, '5f2f648fae6d86531a591bec92ee834b', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(64, 1, 1371471474, 1371477641, 'ff7e921e150b973edd7e68159d4ee397', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(65, 1, 1371477646, 1371480063, '75ff2c6c56f7dcc692b29514e3a8a358', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(66, 5, 1371480121, 1372689721, '7a80873c36a037d1a0a7ec9897c74a0d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(67, 5, 1371630671, 1371630756, 'f20039b83a1db4fefe5f5ef33cc36470', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(68, 1, 1371630760, 1371631399, 'e8a13dcc73046fff84e91a9981abb875', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(69, 10, 1371631420, 1371631720, '8170d44406ff27933fefc857546b18e3', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(70, 11, 1371631735, 1371631850, '284ff88b6a9bde6da585a80755a0f066', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(71, 12, 1371631865, 1371631870, '9a2156f0b2b1c03932d93d3615a3c405', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(72, 14, 1371631932, 1371633086, 'c2e69e401bd1c709aab45d21227feef0', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(73, 15, 1371633096, 1371633103, '1c4b7b5e1653f0301c26de42dbb5e190', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(74, 16, 1371633112, 1371634835, '9b25af4e263d5c2e882514863de16308', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(75, 1, 1371634841, 1371639999, '51ce31eb6b7e640ed736b7add3e516ad', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(76, 17, 1371640041, 1371641635, '13f2d00e5a94c0360ab05c80de0ddb65', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(77, 1, 1371641640, 1371643325, '198910e7f6dc11146f7284a66b35eee1', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(78, 5, 1371643328, 1371649326, '7744640307b2abece00cfdfefe27c450', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(79, 1, 1371649330, 1372858930, '8706387d7d56b6dc452831042a36f46d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(80, 1, 1371711280, 1372920880, '026762da39350ed532f47d529f42854a', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(81, 1, 1371713776, 1372923376, 'c58504b563408b7000b62ee3b84b9706', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(82, 1, 1371722217, 1372931817, '8fd4a3786a48691c8f78a4087c19b4a4', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(83, 18, 1371729719, 1371729996, '2602caa83547eab8995d90e2b58abf1a', '0', '193.191.136.194', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(84, 1, 1371798124, 1373007724, '5f83b2def0f221c390685d6e30bae53d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(85, 1, 1371809157, 1373018757, '12f2fb22ac60085ece91dbbed4922eca', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(86, 1, 1371816207, 1373025807, 'ba0418511c8013501587baf768989e62', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(87, 1, 1371817639, 1373027239, 'fd9a2019391c9aaa78ffa60aa806e33d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(88, 1, 1372058564, 1373268164, '83592481028404b8ff2a1e0276960a52', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(89, 1, 1372067346, 1373276946, '64bfb74c548a5852be2f8aa477ff37ba', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36'),
(90, 1, 1372083687, 1372087775, '2cb13cd2caab6d78575a22a5a0ff3e96', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(91, 1, 1372084528, 1373294128, '637a5d45b5b2e5d76a4aee543b59b2a7', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36'),
(92, 1, 1372087780, 1373297380, 'e5f4b819c1ed0f6a0f90e5dbbbdf3968', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(93, 1, 1372143057, 1373352657, 'b43ac7c6233264012fd1b35d2761215d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(94, 1, 1372149301, 1372149479, '55e03f33499ed1786b4acd6e34a01f32', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(95, 15, 1372149511, 1372149523, 'cad5c9ff00acccd6b9218982cb8489ea', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(96, 16, 1372149527, 1372149539, 'aa833b701bffb52862f4977ec04272d1', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(97, 15, 1372149542, 1372149711, 'f412a79bea3709fc04c541b546224cd8', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(98, 16, 1372149715, 1372150145, '7d153eec4c7eff95cf74801e05ef18b7', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(99, 15, 1372150148, 1372150157, '5d3cc96258fab7fe5f1519d13af219ec', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(100, 16, 1372150173, 1373359773, '2c508784690229e1b5e41417dee2269d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(101, 1, 1372158498, 1373368098, 'ef5a3effb32a0a0ed0042d6321ddf252', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(102, 18, 1372159391, 1373368991, '37b47fceb5255914c4334206ffdd6815', '1', '178.117.118.63', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(103, 1, 1372164582, 1372165032, '02681988b525aeae07306ffaab7ff196', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(104, 16, 1372165039, 1373374639, '5f368d942d39e49a0ea24063584d3b1c', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(105, 18, 1372165672, 1373375272, '59e6e99d75396aff9b3c585ed9eccc55', '1', '178.117.118.63', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(106, 1, 1372169124, 1372170147, '266050e3d3eacc7f3d60ed2ad871e58e', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(107, 16, 1372170151, 1373379751, '8b6d2bb204431c459b9335621a454a9c', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(108, 5, 1372233288, 1372234101, '5ff28e099043a5d90a4e27f747f84390', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(109, 19, 1372234150, 1373443750, '814238662ebc018a6b8d8e7d2f99ab83', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(110, 1, 1372234332, 1372240807, 'dc2c8c8ce37105822842ab80c6950762', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36'),
(111, 20, 1372240867, 1372241782, '12d06a95cf6dbeed48cf46d4c8078e75', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36'),
(112, 5, 1372241787, 1373451387, '1c019c75d3cf0c124505acfc87aeaf7c', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36'),
(113, 1, 1372241815, 1373451415, '79310c5ca765a054b8081897da2e4aa1', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(114, 5, 1372247689, 1373457289, '3ba54f702d150b90c78fdb721b30b74d', '1', '213.181.46.115', 'Mozilla/5.0 (Linux; U; Android 4.0.4; en-gb; GT-P7510 Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30'),
(115, 5, 1372247760, 1372247780, 'd055b299f7f0ba9ca38b42220f62619d', '0', '213.181.46.115', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(116, 21, 1372254189, 1372255127, '0fb2176fa65fd0c455abbed130fea314', '0', '213.181.46.115', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36'),
(117, 1, 1372255131, 1372255158, 'ddcc7344cbd7471158fee7c5f30f27e9', '0', '213.181.46.115', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36'),
(118, 21, 1372255163, 1373464763, '7bd5fa6f0458e605341d6c88d697a364', '1', '213.181.46.115', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36'),
(119, 5, 1372316032, 1373525632, '8cdcf5763f6bcad1337bcae9d0b746af', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(120, 5, 1372320500, 1373530100, '3e669e6a222a7cec7653ee59ecd12c22', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(121, 5, 1372323255, 1373532855, '42a1b0bd0b70700f8651d37968e22ea4', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(122, 5, 1372402108, 1373611708, '84e99a62a22673db38e1cfb802acba6e', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(123, 5, 1372417071, 1373626671, 'cac2ac1491cd6017b87c81ae0f66630a', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(124, 5, 1372661537, 1372663907, '9abc2ef45c2f735c7df2de9dd691cf6b', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(125, 1, 1372663912, 1373873512, '5cbf5a996c10310b257ba85b69a7af36', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(126, 5, 1372747116, 1373956716, 'c2090deaa6bd5985bfc58882cfbb8123', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(127, 5, 1372750622, 1373960222, 'dae44f7ccc78b2a4eefd1aa10a4d1cc0', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(128, 5, 1372753008, 1373962608, '2b351f5d80bd0154abb917c1f9571633', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(129, 5, 1372755828, 1373965428, 'a4d944d44beee4385743d2b4968adfad', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(130, 1, 1372766692, 1373976292, 'b30a3766e67cde8e4725518f62bfc1fa', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(131, 18, 1372919261, 1374128861, '4992a36c68394ccfd95450ba04aa7882', '1', '193.191.136.194', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(132, 5, 1372922850, 1372923173, 'f8dd88dc1f629467878caffcfd4b11f0', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(133, 1, 1372923177, 1374132777, '291ebb1bf72d4e92df3526fecd223bad', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0'),
(134, 1, 1372926137, 1374135737, '78002679efcb0e775eb4c7f5a5c53985', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(135, 1, 1372936564, 1374146164, 'c50e0ef2c1dc479bf8f7072ae01ae68e', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(136, 1, 1373007652, 1374217252, '8304c8c133ea423a4156176528930b61', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(137, 1, 1373023688, 1374233288, 'e8083a213c56125473835b0e4341eff0', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(138, 5, 1373273806, 1374483406, 'c40a88aae22771667b5f5fd3fee243da', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(139, 5, 1373288048, 1373290785, '2a09f37a762693bdca5022cc65cb54cc', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(140, 1, 1373290790, 1374500390, 'd9ee0cc01662e4f66e3f2d7529d5f79e', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(141, 5, 1373295119, 1374504719, '73f4ae85242c1b69da6d8af5845b6ed2', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(142, 5, 1373352009, 1374561609, 'e6e011fdadf0a84d951576bea79bce7d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(143, 5, 1373356759, 1374566359, '82b71c531789f43fc30ade3988082414', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(144, 5, 1373361389, 1374570989, 'cfc5dfa2b96f292b6bd33283979a2d3b', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(145, 5, 1373368062, 1374577662, 'fb637d0c3a0eed2a58d3277c450411bf', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(146, 5, 1373370694, 1374580294, 'b6a162542e0085eec219f5a0c861031a', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(147, 1, 1373375337, 1374584937, '2c41124fe00cbe16437f8823566aa951', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36'),
(148, 5, 1373443497, 1374653097, '5164c94777af3ba25702aaf07812dbc6', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(149, 1, 1373446519, 1374656119, '755ef948caa694e2f3efc8c25884fb37', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.71 Safari/537.36'),
(150, 1, 1373449787, 1374659387, '71923e4ef9b246c38619eec0878c25ed', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.71 Safari/537.36'),
(151, 5, 1373459005, 1374668605, 'dd837c777707daa8ff81fbce380b352b', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(152, 5, 1373616376, 1373618624, '48c6d74260500566b9ccc95337ecbaf3', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(153, 15, 1373618610, 1374828210, '7bbcd347273d7ad0dd9a044ed90de133', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.71 Safari/537.36'),
(154, 16, 1373618628, 1374828228, 'b296d4a362fdf473c754d5d05b5385db', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(155, 1, 1374219894, 1374225109, '7a7aa9d24506e11d565b84d9432f9db0', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(156, 5, 1374225113, 1375434713, '3d2df1e7af28940e4780fc28d8811957', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(157, 1, 1374225186, 1375434786, '1f3ba8252ba108519b0d4f9c9e6aa13b', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.72 Safari/537.36'),
(158, 18, 1374526909, 1375736509, 'b6ef60db2563427966ce704ad24432a7', '1', '178.117.118.63', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(159, 5, 1376637277, 1377846877, '24c9c687c4b9f2f39949c48bb4f03b14', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(160, 5, 1376895146, 1378104746, '3555674cd1d1a7b60b0e0aed273163bb', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(161, 5, 1376900838, 1378110438, '33d2bab50cbd974032eb4a74aa0146ec', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(162, 5, 1376910049, 1378119649, '758fff85e02ede33b80191ea074663a1', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0'),
(163, 5, 1376982141, 1378191741, '91a7bd545c1d4fc8dbea344879385c70', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(164, 5, 1376990337, 1378199937, 'ec5f6c606ea095198faccf0bb47b9c1f', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(165, 5, 1376997294, 1378206894, '1ae087f39301af32d13f240f33896ca0', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(166, 5, 1377004108, 1378213708, 'e66f0e3b695255026899cc1595063618', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(167, 5, 1377006184, 1378215784, '5792a99e1c44964e26a120467af9dfc9', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(168, 5, 1377068686, 1378278286, '11ccb10af86d3704377856779670a0b1', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(169, 5, 1377072604, 1378282204, 'd824bfff50ef3c8cdaa44d439f29f737', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(170, 5, 1377075383, 1378284983, 'd938e5034a08b20aa70c010c56caf1b9', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(171, 5, 1377077236, 1378286836, 'd11f8661bb5ad7feb87b598427032ca5', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(172, 1, 1377097335, 1378306935, 'dd31d3f79899c541574874c836a02371', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36'),
(173, 5, 1377153439, 1378363039, '06cdc5ca4ae3cdd3188712f3c5273094', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(174, 5, 1377156889, 1378366489, '90fadd91e923210756893696d0f5691b', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(175, 5, 1377169265, 1378378865, 'cfd32d14f84e5f130026da7fe68c50a0', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(176, 5, 1377174136, 1378383736, '21feef4837bb1105ad7d306aa035f0c2', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(177, 5, 1377181108, 1378390708, '2a910e1eab27d94971616ea6981293a1', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(178, 1, 1377183128, 1378392728, '67fa9836ad134a5463b20542b5b6a5de', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36'),
(179, 5, 1377241107, 1378450707, '63288e10afe1aee487aadb63eb8d257a', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(180, 5, 1377248461, 1378458061, '4686fffc26f8351bbaa7303584786f11', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(181, 5, 1377260292, 1378469892, '9f345603bd18fba38364f5f04f3ca2ef', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(182, 5, 1377501271, 1378710871, 'da65ef4ec1776cf065ca1636b105e7f3', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(183, 5, 1377511620, 1378721220, '11c6e846aa7b3e4c4866baacc2724c00', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(184, 5, 1377518155, 1378727755, '274ae36e0bff2427a81fc81b00431405', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(185, 5, 1377523785, 1378733385, '5de5982d60c7b74766d226444155b5b2', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(186, 1, 1377526919, 1378736519, 'd5b32d852183e12b44dfd3d67f0748db', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(187, 5, 1377585928, 1378795528, 'b2fbaf94c42defc1a5600b3fd0d4c830', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(188, 5, 1377590266, 1377592181, 'a8e5a3c3138e6bafa73686cd18e227a8', '0', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(189, 5, 1377592184, 1378801784, 'b524ba240d163cbb811154932ef115fd', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(190, 1, 1377595813, 1378805413, '695d521d468f245b658ea4e7ddae0685', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(191, 5, 1377604218, 1378813818, '63a8c2a9902b8ee249df90307d67230a', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(192, 5, 1377606486, 1378816086, 'f1e0fa01fd08dd361d7809526c61b949', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(193, 5, 1377611766, 1378821366, '8cda85b0112727a141796c5809554647', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(194, 5, 1377758714, 1378968314, '05efa842e493dbeca567d7734b51b5c6', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(195, 5, 1377765098, 1378974698, '3dac729e3ee41b561b6b3468cced87c6', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(196, 5, 1377767273, 1378976873, '555ba3543f1d3c86a9ead664a6723bd7', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(197, 1, 1377779388, 1378988988, 'c5f36cc9d93bf652828a7e09bc11e966', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.57 Safari/537.36'),
(198, 5, 1377846795, 1379056395, '4c728f24ba0ecc4dc8465587faf91f54', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(199, 5, 1377855313, 1379064913, '749f8eba542f6b17f37da33b1e4ad7d9', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(200, 5, 1378107100, 1379316700, '2216d080ce358d43da6d1d99b432c2c8', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(201, 5, 1378110700, 1379320300, '151f5cddd4b421d1506c46c179c036be', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(202, 5, 1378123060, 1379332660, 'ba59e41f503e132335acbfad87e85a75', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(203, 5, 1378127831, 1379337431, 'ecd44d61bbd8b94a92cb35bfdae8dd43', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(204, 5, 1378130972, 1379340572, '270d17009f03b27f6b8596f68253845c', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(205, 5, 1378192181, 1379401781, 'eb07075f841b736fb84370c6b90a4cea', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(206, 5, 1378195210, 1379404810, '3e3aa8bb7b016bc89eb2ae93a4048cf0', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(207, 18, 1378201383, 1379410983, 'be2917c5cadafc9f7b35b0b104de0ca6', '1', '178.117.118.63', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(208, 5, 1378209405, 1379419005, '5bacef01bd9d7791704f136b88f42e2f', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(209, 18, 1378213497, 1379423097, '2246cb1d2be5aea68d67cb4259d592f9', '1', '178.117.118.63', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(210, 5, 1378213592, 1379423192, '23d6646f31e3abf22aacfdd9eb140150', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(211, 5, 1378215903, 1379425503, '2a4d2a0f8dfa48b483eed3b3e243b407', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(212, 5, 1378279028, 1379488628, '0464acf895ef29f4c4d5b3745826caf2', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(213, 5, 1378287207, 1379496807, '8de8b6f31882a4f2fd711444a9092adc', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(214, 5, 1378298072, 1379507672, '3245557866869426a682676cfa405297', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(215, 5, 1378301722, 1379511322, '3e36a111d2c157ba4095cc8af8a5afee', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(216, 5, 1378364639, 1379574239, '30b1e06ae1f03b8f0a2cce87daeac0e0', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(217, 5, 1378370686, 1379580286, '10fc8b19a35008072ec82872a4f0d04a', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(218, 5, 1378379464, 1379589064, 'edb91482cd1f0f072b1bd012321b6b90', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(219, 5, 1378386260, 1379595860, '17b91dfb112f0065b46d020277e3e182', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(220, 1, 1378387296, 1379596896, 'e029176dcee9bad44e222bccdd5427a3', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36'),
(221, 5, 1378451060, 1379660660, '37af17e7c5d7d54ffcfa9dfbe928df91', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(222, 5, 1378466104, 1379675704, '21d212967b8f7737efa98768d2a03811', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(223, 1, 1378467382, 1379676982, '72faceed2c89a4563a38df7bb76a5aec', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36'),
(224, 16, 1378468143, 1379677743, '1b7775649af07f4b8f73dbadb0f88a63', '1', '193.191.136.194', 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3)'),
(225, 5, 1378805098, 1380014698, '3d03e442863ff079d0716801b8c7a583', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(226, 5, 1378886169, 1380095769, 'ebc0e20431649ed8d0a6901ad1a64753', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(227, 5, 1378996008, 1380205608, 'ec2777c19bc057ad6e42c809c043ef22', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(228, 5, 1379057325, 1380266925, '2dc731981e94ab729573febbae07235d', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0'),
(229, 22, 1379504934, 1380714534, 'c6318d1b2082b3ce1128b4e2ed667e9e', '1', '193.191.136.194', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0');

-- --------------------------------------------------------

--
-- Table structure for table `_tblTransaction`
--

CREATE TABLE IF NOT EXISTS `_tblTransaction` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `datum` bigint(20) unsigned NOT NULL,
  `sender` bigint(20) NOT NULL,
  `receiver` bigint(20) NOT NULL,
  `sendersigned` bigint(20) NOT NULL,
  `receiversigned` bigint(20) NOT NULL,
  `market` bigint(20) NOT NULL,
  `credits` int(11) NOT NULL,
  `mental` int(11) NOT NULL,
  `physical` int(11) NOT NULL,
  `emotional` int(11) NOT NULL,
  `social` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
