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
-- Table structure for table `alliancefinance`
--

DROP TABLE IF EXISTS `alliancefinance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliancefinance` (
  `OperationID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ForUserID` int(10) unsigned DEFAULT NULL,
  `AllianceID` int(10) unsigned NOT NULL,
  `UserID` int(10) unsigned NOT NULL,
  `Date` int(10) unsigned NOT NULL,
  `Type` enum('in','out') NOT NULL DEFAULT 'in',
  `Value` bigint(20) NOT NULL,
  `Comment` varchar(255) NOT NULL,
  PRIMARY KEY (`OperationID`),
  KEY `AllianceID` (`AllianceID`,`Date`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliancefinance`
--

LOCK TABLES `alliancefinance` WRITE;
/*!40000 ALTER TABLE `alliancefinance` DISABLE KEYS */;
INSERT INTO `alliancefinance` VALUES (13,NULL,1,1,1311597014,'in',1000000,''),(14,1,1,1,1311597023,'out',1,''),(15,1,1,1,1311597110,'out',1,''),(16,1,1,1,1311597154,'out',1,''),(17,1,1,1,1311597301,'out',1,''),(18,1,1,1,1311597337,'out',1,''),(19,1,1,1,1311597370,'out',1,''),(20,1,1,1,1311597375,'out',2,''),(21,1,1,1,1311597384,'out',1,''),(22,1,1,1,1311597389,'out',1,''),(23,28949,1,1,1311597520,'out',2000,''),(24,28949,1,1,1311597614,'out',1,'');
/*!40000 ALTER TABLE `alliancefinance` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:35:26
