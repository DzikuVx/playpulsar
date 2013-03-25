CREATE DATABASE  IF NOT EXISTS `pulsar_gameplay` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `pulsar_gameplay`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: pulsar_gameplay
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
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `MessageID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Author` int(10) unsigned DEFAULT NULL,
  `Receiver` int(10) unsigned NOT NULL,
  `Received` enum('yes','no') NOT NULL DEFAULT 'no',
  `Text` text NOT NULL,
  `CreateTime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`MessageID`),
  KEY `Receiver` (`Receiver`,`Received`),
  KEY `Author` (`Author`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (32,NULL,16247,'no','dfhggdfh',1300832149),(31,1,16249,'no','To jest super test ąółŁŻŹĆŻŁRE',1300820811),(41,NULL,16249,'no','Super Dziku requests to by your buddy.',1302720001),(47,NULL,16248,'no','Super Dziku wysłał do Ciebie prośbę o przyjęcie do grona znajomych.',1302804184),(49,1,4,'no','ąęółźżćśóśąśąśóśłśą',1304199404),(57,1,24707,'yes','tyurtu',1307686477),(58,1,24707,'yes','dsdgfgsd dfgsdfg',1307687538);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:36:06
