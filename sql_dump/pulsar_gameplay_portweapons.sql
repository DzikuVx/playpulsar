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
-- Table structure for table `portweapons`
--

DROP TABLE IF EXISTS `portweapons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portweapons` (
  `PortID` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `WeaponID` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `Active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  KEY `PortID` (`PortID`,`Active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portweapons`
--

LOCK TABLES `portweapons` WRITE;
/*!40000 ALTER TABLE `portweapons` DISABLE KEYS */;
INSERT INTO `portweapons` VALUES (4,2,'yes'),(4,2,'yes'),(4,2,'yes'),(4,2,'yes'),(4,2,'yes'),(4,2,'yes'),(4,2,'yes'),(4,2,'yes'),(4,3,'yes'),(4,3,'yes'),(4,3,'yes'),(4,3,'yes'),(4,3,'yes'),(4,3,'yes'),(4,3,'yes'),(4,3,'yes'),(1,3,'yes'),(1,3,'yes'),(1,3,'yes'),(1,3,'yes'),(1,3,'yes'),(1,3,'yes'),(1,3,'yes'),(1,2,'yes'),(1,2,'yes'),(1,2,'yes'),(1,2,'yes'),(1,2,'yes'),(1,2,'yes'),(1,2,'yes'),(1,2,'yes'),(1,2,'yes'),(1,2,'yes'),(1,2,'yes'),(2,3,'yes'),(2,3,'yes'),(2,3,'yes'),(2,3,'yes'),(2,3,'yes'),(2,3,'yes'),(2,3,'yes'),(2,2,'yes'),(2,2,'yes'),(2,2,'yes'),(2,2,'yes'),(2,2,'yes'),(2,2,'yes'),(2,2,'yes'),(2,2,'yes'),(2,2,'yes'),(102,2,'yes'),(102,2,'yes'),(102,2,'yes'),(102,2,'yes'),(102,2,'yes'),(102,2,'yes'),(102,2,'yes'),(102,2,'yes'),(102,3,'yes'),(102,3,'yes'),(102,3,'yes'),(102,3,'yes'),(102,3,'yes'),(102,3,'yes'),(102,3,'yes'),(102,3,'yes'),(3,2,'yes'),(3,2,'yes'),(3,2,'yes'),(3,2,'yes'),(3,2,'yes'),(3,2,'yes'),(3,2,'yes'),(3,2,'yes'),(3,3,'yes'),(3,3,'yes'),(3,3,'yes'),(3,3,'yes'),(3,3,'yes'),(3,3,'yes'),(3,3,'yes'),(3,3,'yes'),(35,3,'yes'),(35,3,'yes'),(35,3,'yes'),(35,3,'yes'),(35,3,'yes'),(35,3,'yes'),(35,3,'yes'),(35,3,'yes'),(35,2,'yes'),(35,2,'yes'),(35,2,'yes'),(35,2,'yes'),(35,2,'yes'),(35,2,'yes'),(35,2,'yes'),(35,2,'yes'),(37,2,'yes'),(37,2,'yes'),(37,2,'yes'),(37,2,'yes'),(37,2,'yes'),(37,2,'yes'),(37,2,'yes'),(37,2,'yes'),(37,3,'yes'),(37,3,'yes'),(37,3,'yes'),(37,3,'yes'),(37,3,'yes'),(37,3,'yes'),(37,3,'yes'),(37,3,'yes'),(36,2,'yes'),(36,2,'yes'),(36,2,'yes'),(36,2,'yes'),(36,2,'yes'),(36,2,'yes'),(36,2,'yes'),(36,2,'yes'),(36,3,'yes'),(36,3,'yes'),(36,3,'yes'),(36,3,'yes'),(36,3,'yes'),(36,3,'yes'),(36,3,'yes'),(36,3,'yes'),(40,2,'yes'),(40,2,'yes'),(40,2,'yes'),(40,2,'yes'),(40,2,'yes'),(40,2,'yes'),(40,2,'yes'),(40,2,'yes'),(40,2,'yes'),(40,3,'yes'),(40,3,'yes'),(40,3,'yes'),(40,3,'yes'),(40,3,'yes'),(40,3,'yes'),(40,3,'yes'),(40,3,'yes'),(40,3,'yes'),(6,3,'yes'),(6,3,'yes'),(6,3,'yes'),(6,3,'yes'),(6,3,'yes'),(6,3,'yes'),(6,3,'yes'),(6,2,'yes'),(6,2,'yes'),(6,2,'yes'),(6,2,'yes'),(6,2,'yes'),(6,2,'yes'),(6,2,'yes'),(6,2,'yes'),(6,2,'yes'),(6,3,'yes'),(6,3,'yes'),(148,2,'yes'),(148,2,'yes'),(148,2,'yes'),(148,2,'yes'),(148,2,'yes'),(148,2,'yes'),(148,2,'yes'),(148,2,'yes'),(148,3,'yes'),(148,3,'yes'),(148,3,'yes'),(148,3,'yes'),(148,3,'yes'),(148,3,'yes'),(148,3,'yes'),(148,3,'yes'),(1,3,'yes'),(1,3,'yes'),(1,3,'yes'),(1,3,'yes'),(39,2,'yes'),(39,2,'yes'),(39,2,'yes'),(39,2,'yes'),(39,2,'yes'),(39,2,'yes'),(39,2,'yes'),(39,2,'yes'),(39,3,'yes'),(39,3,'yes'),(39,3,'yes'),(39,3,'yes'),(39,3,'yes'),(39,3,'yes'),(39,3,'yes'),(39,3,'yes'),(32,2,'yes'),(32,2,'yes'),(32,2,'yes'),(32,2,'yes'),(32,2,'yes'),(32,2,'yes'),(32,2,'yes'),(32,2,'yes'),(32,3,'yes'),(32,3,'yes'),(32,3,'yes'),(32,3,'yes'),(32,3,'yes'),(32,3,'yes'),(32,3,'yes'),(32,3,'yes'),(73,2,'yes'),(73,2,'yes'),(73,2,'yes'),(73,2,'yes'),(73,2,'yes'),(73,2,'yes'),(73,2,'yes'),(73,2,'yes'),(73,3,'yes'),(73,3,'yes'),(73,3,'yes'),(73,3,'yes'),(73,3,'yes'),(73,3,'yes'),(73,3,'yes'),(73,3,'yes'),(74,2,'yes'),(74,2,'yes'),(74,2,'yes'),(74,2,'yes'),(74,2,'yes'),(74,2,'yes'),(74,2,'yes'),(74,2,'yes'),(74,3,'yes'),(74,3,'yes'),(74,3,'yes'),(74,3,'yes'),(74,3,'yes'),(74,3,'yes'),(74,3,'yes'),(74,3,'yes'),(5,2,'yes'),(5,2,'yes'),(5,2,'yes'),(5,2,'yes'),(5,2,'yes'),(5,2,'yes'),(5,2,'yes'),(5,2,'yes'),(5,3,'yes'),(5,3,'yes'),(5,3,'yes'),(5,3,'yes'),(5,3,'yes'),(5,3,'yes'),(5,3,'yes'),(5,3,'yes'),(2,3,'yes'),(2,3,'yes'),(104,2,'yes'),(104,2,'yes'),(104,2,'yes'),(104,2,'yes'),(104,2,'yes'),(104,2,'yes'),(104,2,'yes'),(104,2,'yes'),(104,3,'yes'),(104,3,'yes'),(104,3,'yes'),(104,3,'yes'),(104,3,'yes'),(104,3,'yes'),(104,3,'yes'),(104,3,'yes'),(24,2,'yes'),(24,2,'yes'),(24,2,'yes'),(24,2,'yes'),(24,2,'yes'),(24,2,'yes'),(24,2,'yes'),(24,2,'yes'),(24,3,'yes'),(24,3,'yes'),(24,3,'yes'),(24,3,'yes'),(24,3,'yes'),(24,3,'yes'),(24,3,'yes'),(24,3,'yes'),(242,3,'yes'),(242,3,'yes'),(242,3,'yes'),(242,3,'yes'),(242,3,'yes'),(242,3,'yes'),(242,3,'yes'),(242,2,'yes'),(242,2,'yes'),(242,2,'yes'),(242,2,'yes'),(242,2,'yes'),(242,2,'yes'),(242,2,'yes'),(242,2,'yes'),(242,2,'yes'),(103,2,'yes'),(103,2,'yes'),(103,2,'yes'),(103,2,'yes'),(103,2,'yes'),(103,2,'yes'),(103,2,'yes'),(103,2,'yes'),(103,3,'yes'),(103,3,'yes'),(103,3,'yes'),(103,3,'yes'),(103,3,'yes'),(103,3,'yes'),(103,3,'yes'),(103,3,'yes'),(242,3,'yes'),(242,3,'yes'),(12,2,'yes'),(12,2,'yes'),(12,2,'yes'),(12,2,'yes'),(12,2,'yes'),(12,2,'yes'),(12,2,'yes'),(12,2,'yes'),(12,3,'yes'),(12,3,'yes'),(12,3,'yes'),(12,3,'yes'),(12,3,'yes'),(12,3,'yes'),(12,3,'yes'),(12,3,'yes'),(38,2,'yes'),(38,2,'yes'),(38,2,'yes'),(38,2,'yes'),(38,2,'yes'),(38,2,'yes'),(38,2,'yes'),(38,2,'yes'),(38,3,'yes'),(38,3,'yes'),(38,3,'yes'),(38,3,'yes'),(38,3,'yes'),(38,3,'yes'),(38,3,'yes'),(38,3,'yes'),(850,2,'yes'),(850,2,'yes'),(850,2,'yes'),(850,2,'yes'),(850,2,'yes'),(850,2,'yes'),(850,2,'yes'),(850,2,'yes'),(850,3,'yes'),(850,3,'yes'),(850,3,'yes'),(850,3,'yes'),(850,3,'yes'),(850,3,'yes'),(850,3,'yes'),(850,3,'yes'),(848,2,'yes'),(848,2,'yes'),(848,2,'yes'),(848,2,'yes'),(848,2,'yes'),(848,2,'yes'),(848,2,'yes'),(848,2,'yes'),(848,3,'yes'),(848,3,'yes'),(848,3,'yes'),(848,3,'yes'),(848,3,'yes'),(848,3,'yes'),(848,3,'yes'),(848,3,'yes'),(435,2,'yes'),(435,2,'yes'),(435,2,'yes'),(435,2,'yes'),(435,2,'yes'),(435,2,'yes'),(435,2,'yes'),(435,2,'yes'),(435,3,'yes'),(435,3,'yes'),(435,3,'yes'),(435,3,'yes'),(435,3,'yes'),(435,3,'yes'),(435,3,'yes'),(435,3,'yes'),(441,2,'yes'),(441,2,'yes'),(441,2,'yes'),(441,2,'yes'),(441,2,'yes'),(441,2,'yes'),(441,2,'yes'),(441,2,'yes'),(441,3,'yes'),(441,3,'yes'),(441,3,'yes'),(441,3,'yes'),(441,3,'yes'),(441,3,'yes'),(441,3,'yes'),(441,3,'yes');
/*!40000 ALTER TABLE `portweapons` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:36:00
