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
-- Table structure for table `nodes`
--

DROP TABLE IF EXISTS `nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodes` (
  `NodeID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `SrcSystem` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `SrcX` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `SrcY` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `DstSystem` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `DstX` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `DstY` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`NodeID`),
  KEY `SrcGalaxy` (`Active`,`SrcSystem`,`SrcX`,`SrcY`),
  KEY `DstGalaxy` (`Active`,`DstSystem`,`DstX`,`DstY`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nodes`
--

LOCK TABLES `nodes` WRITE;
/*!40000 ALTER TABLE `nodes` DISABLE KEYS */;
INSERT INTO `nodes` VALUES (1,'yes',1,6,9,2,5,6),(2,'yes',1,23,22,3,8,6),(3,'yes',1,7,18,4,19,16),(4,'yes',3,22,20,9,6,14),(5,'yes',2,22,6,8,7,19),(6,'yes',5,14,14,9,14,22),(7,'yes',5,4,5,6,16,19),(8,'yes',4,3,13,6,20,8),(9,'yes',6,15,6,7,6,17),(10,'yes',7,18,11,8,4,10),(11,'yes',8,20,14,9,13,5),(12,'yes',9,20,9,11,10,14),(13,'yes',9,17,15,12,1,6),(14,'yes',12,20,11,13,5,20),(15,'yes',12,9,15,14,10,9),(16,'yes',14,17,16,15,15,10),(17,'yes',8,13,7,16,6,20),(18,'yes',16,11,10,10,21,14),(19,'yes',5,4,14,17,12,10),(20,'yes',17,22,17,18,3,4),(21,'yes',18,14,21,22,20,5),(22,'yes',22,14,18,27,5,14),(23,'yes',17,8,16,19,11,7),(24,'yes',17,4,4,20,18,15),(25,'yes',20,2,4,23,20,16),(26,'yes',23,5,11,24,4,21),(27,'yes',20,8,14,21,7,15),(28,'yes',21,13,21,19,2,23),(29,'yes',19,14,24,26,21,10),(30,'yes',19,16,20,22,1,10),(31,'yes',21,7,19,25,19,15),(32,'yes',6,8,10,28,13,18),(33,'yes',28,12,2,29,19,9),(34,'yes',28,3,11,33,15,19),(35,'yes',33,2,23,37,17,10),(36,'yes',37,3,18,38,22,22),(37,'yes',33,6,5,32,6,20),(38,'yes',32,10,8,39,12,18),(39,'yes',29,7,18,32,22,8),(40,'yes',29,3,3,31,20,5),(41,'yes',31,5,9,40,4,16),(42,'yes',29,13,2,30,8,17),(43,'yes',30,19,9,34,17,12),(44,'yes',34,19,11,35,9,13),(45,'yes',34,18,13,36,6,9);
/*!40000 ALTER TABLE `nodes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:35:43
