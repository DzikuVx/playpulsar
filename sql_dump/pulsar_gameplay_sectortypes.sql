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
-- Table structure for table `sectortypes`
--

DROP TABLE IF EXISTS `sectortypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sectortypes` (
  `SectorTypeID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Color` varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Image` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `MoveCost` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `Visibility` tinyint(4) unsigned NOT NULL DEFAULT '100',
  `Accuracy` tinyint(4) unsigned NOT NULL DEFAULT '100',
  `Resources` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`SectorTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sectortypes`
--

LOCK TABLES `sectortypes` WRITE;
/*!40000 ALTER TABLE `sectortypes` DISABLE KEYS */;
INSERT INTO `sectortypes` VALUES (1,'asteroidbelt','909090','gfx/sectors/asteroidbelt.png',4,50,50,'4,10,11'),(2,'darkmatter','202020','gfx/sectors/darkmatter.jpg',2,75,75,''),(3,'moonorbit','404040','gfx/sectors/moonorbit.jpg',2,100,100,''),(4,'rednebula','a02020','gfx/sectors/rednebula.jpg',4,25,40,'1,2,15'),(5,'darknebula','606060','gfx/sectors/darknebula.jpg',3,30,40,'2,15,4'),(6,'nebula','4040a0','gfx/sectors/nebula.jpg',3,80,60,'1,2,15'),(7,'giantplanet','c06060','gfx/sectors/giantplanet.png',2,75,100,''),(8,'grayplanet','c0c0c0','gfx/sectors/grayplanet.png',2,75,100,''),(9,'nebulaplanet','484f84','gfx/sectors/nebulaplanet.png',4,30,50,''),(10,'redplanet','c56748','gfx/sectors/redplanet.png',2,75,100,''),(11,'redplanet2','c56748','gfx/sectors/redplanet2.png',2,75,100,''),(12,'redplanet3','c56748','gfx/sectors/redplanet2.png',2,75,100,''),(13,'sun','f8f501','gfx/sectors/sun.jpg',4,25,40,''),(14,'bluesun','f8f501','gfx/sectors/bluesun.jpg',6,55,30,''),(15,'blackhole','ff0000','gfx/sectors/blackhole.jpg',100,0,0,''),(16,'pulsar','056e04','gfx/sectors/pulsar.jpg',12,0,5,''),(17,'dualsun','f8f501','gfx/sectors/dualsun.jpg',24,10,25,'');
/*!40000 ALTER TABLE `sectortypes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:36:13
