CREATE DATABASE  IF NOT EXISTS `pulsar_portal` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `pulsar_portal`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: pulsar_portal
-- ------------------------------------------------------
-- Server version	5.5.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `portal_news`
--

DROP TABLE IF EXISTS `portal_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portal_news` (
  `NewsID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Type` enum('news','article') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'news',
  `Published` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `Language` enum('pl','en') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pl',
  `MainNews` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `UserID` int(10) unsigned NOT NULL DEFAULT '0',
  `UserName` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '''''',
  `Time` int(10) unsigned NOT NULL DEFAULT '0',
  `Title` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`NewsID`),
  KEY `Time` (`Time`),
  KEY `MainNews` (`MainNews`),
  KEY `Type` (`Type`,`Published`,`Language`,`MainNews`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portal_news`
--

LOCK TABLES `portal_news` WRITE;
/*!40000 ALTER TABLE `portal_news` DISABLE KEYS */;
INSERT INTO `portal_news` VALUES (3,'news','yes','pl','yes',1,'',0,'','<h1 class=\"Header1\"><span style=\"font-size: medium;\">Serdecznie witamy w Pulsar-Online</span></h1>\r\n<p>Pulsar-Online to taktyczna gra symulacyjna o międzynarodowym zasięgu.  Wielu graczy zbiera się by grać z sobą, lub przeciwko sobie. Do gry  potrzebna jest jedynie przeglądarka internetowa jak Internet Explorer  lub Mozilla Firefox<br /> Obecny stan gry: <span style=\"text-decoration: underline;\">tworzenie2</span></p>'),(2,'news','yes','en','yes',1,'',0,'','<div class=\"Header1\">Welcome to Pulsar-Online</div>\r\n<p>Pulsar-Online is an multiplayer tactical simulation game with an  international range. Players get together to fight with or against each  other. To play you only need a regular web browser like Internet  Explorer or Mozilla Firefox. That\'s all. <br /> Current game state: <span style=\"text-decoration: underline;\">development2</span></p>'),(16,'news','yes','pl','no',1,'',1291553528,'Witamy','<p>Witamy w \"naszej\" <strong>bajce 2</strong></p>');
/*!40000 ALTER TABLE `portal_news` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:36:17
