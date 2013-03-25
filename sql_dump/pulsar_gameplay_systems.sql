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
-- Table structure for table `systems`
--

DROP TABLE IF EXISTS `systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systems` (
  `SystemID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Galaxy` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `Name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Number` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `Width` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Height` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Enabled` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `MapAvaible` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`SystemID`),
  KEY `Enabled` (`Enabled`),
  KEY `Galaxy` (`Galaxy`),
  KEY `MapAvaible` (`MapAvaible`)
) ENGINE=MyISAM AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systems`
--

LOCK TABLES `systems` WRITE;
/*!40000 ALTER TABLE `systems` DISABLE KEYS */;
INSERT INTO `systems` VALUES (1,1,'Sarin',1,32,32,'yes','yes'),(2,1,'Regulus',2,32,32,'yes','yes'),(3,1,'Alfa Centauri',3,24,24,'yes','yes'),(4,1,'Treversi Gamma',4,24,24,'yes','yes'),(5,1,'Sol',5,16,16,'yes','yes'),(6,1,'Tau Ceti',6,24,24,'yes','yes'),(7,1,'Barnard',7,24,24,'yes','yes'),(8,1,'Facece',8,24,24,'yes','yes'),(9,1,'Esshocan',9,24,24,'yes','yes'),(10,1,'Exhoed',10,24,24,'yes','no'),(11,1,'Alioth',11,24,24,'yes','no'),(12,1,'Altair',12,24,24,'yes','no'),(13,1,'Arcturus',13,24,24,'yes','no'),(14,1,'Betelgeuse',14,24,24,'yes','no'),(15,1,'Delta Pavonis',15,24,24,'yes','no'),(16,1,'Ross 128',16,24,24,'yes','no'),(17,1,'Epsilon Eridani',17,24,24,'yes','no'),(18,1,'Ross 154',18,24,24,'yes','no'),(19,1,'Fomalhaut',19,24,24,'yes','no'),(20,1,'Vega',20,24,24,'yes','no'),(21,1,'Vequess',21,24,24,'yes','no'),(22,1,'Inedol',22,24,24,'yes','no'),(23,1,'Ioarqu',23,24,24,'yes','no'),(24,1,'Laedgre',24,24,24,'yes','no'),(25,1,'Liabeze',25,24,24,'yes','no'),(26,1,'Luyten 97-12',26,24,24,'yes','no'),(27,1,'Micanex',27,24,24,'yes','no'),(28,1,'Ross 986',28,24,24,'yes','no'),(29,1,'Sohoa',29,24,24,'yes','no'),(30,1,'Veexio',30,24,24,'yes','no'),(31,1,'Zeceeth',31,24,24,'yes','no'),(32,1,'Enengre',32,24,24,'yes','no'),(33,1,'Laceti',33,24,24,'yes','no'),(34,1,'Bemiio',34,24,24,'yes','no'),(35,1,'Canedand',35,24,24,'yes','no'),(36,1,'Hovea',36,24,24,'yes','no'),(37,1,'Waav',37,24,24,'yes','no'),(38,1,'Miurar',38,24,24,'yes','no'),(39,1,'Hoquso',39,24,24,'yes','no'),(40,1,'Omricon Beta',40,24,24,'yes','no'),(41,2,'P2X9156',1,16,16,'no','no'),(42,2,'P2X3222',2,20,20,'no','no'),(43,2,'P2X9364',3,16,16,'no','no'),(44,2,'P2X8485',4,20,20,'no','no'),(45,2,'P2X7579',5,16,16,'no','no'),(46,2,'P2X9682',6,24,24,'no','no'),(47,2,'P2X9750',7,16,16,'no','no'),(48,2,'P2X4848',8,24,24,'no','no'),(49,2,'P2X2979',9,24,24,'no','no'),(50,2,'P2X81074',10,20,20,'no','no'),(51,2,'P2X31130',11,16,16,'no','no'),(52,2,'P2X41287',12,20,20,'no','no'),(53,2,'P2X91339',13,24,24,'no','no'),(54,2,'P2X71430',14,24,24,'no','no'),(55,2,'P2X41546',15,16,16,'no','no'),(56,2,'P2X51650',16,16,16,'no','no'),(57,2,'P2X91762',17,16,16,'no','no'),(58,2,'P2X41824',18,20,20,'no','no'),(59,2,'P2X51922',19,16,16,'no','no'),(60,2,'P2X22072',20,16,16,'no','no'),(61,2,'P2X72151',21,16,16,'no','no'),(62,2,'P2X62289',22,24,24,'no','no'),(63,2,'P2X42380',23,20,20,'no','no'),(64,2,'P2X92486',24,20,20,'no','no'),(65,2,'P2X92528',25,16,16,'no','no'),(66,2,'P2X22654',26,20,20,'no','no'),(67,2,'P2X92762',27,20,20,'no','no'),(68,2,'P2X52863',28,24,24,'no','no'),(69,2,'P2X42955',29,20,20,'no','no'),(70,2,'P2X83035',30,20,20,'no','no'),(71,3,'P3X9156',1,20,20,'no','no'),(72,3,'P3X6289',2,16,16,'no','no'),(73,3,'P3X4370',3,16,16,'no','no'),(74,3,'P3X8424',4,16,16,'no','no'),(75,3,'P3X6542',5,24,24,'no','no'),(76,3,'P3X5619',6,16,16,'no','no'),(77,3,'P3X4765',7,24,24,'no','no'),(78,3,'P3X7886',8,20,20,'no','no'),(79,3,'P3X2956',9,20,20,'no','no'),(80,3,'P3X31053',10,16,16,'no','no'),(81,3,'P3X51123',11,24,24,'no','no'),(82,3,'P3X61270',12,16,16,'no','no'),(83,3,'P3X41382',13,24,24,'no','no'),(84,3,'P3X61462',14,20,20,'no','no'),(85,3,'P3X71570',15,24,24,'no','no'),(86,3,'P3X41671',16,24,24,'no','no'),(87,3,'P3X71759',17,20,20,'no','no'),(88,3,'P3X41837',18,24,24,'no','no'),(89,3,'P3X21945',19,24,24,'no','no'),(90,3,'P3X32060',20,24,24,'no','no'),(91,3,'P3X22152',21,20,20,'no','no'),(92,3,'P3X42213',22,16,16,'no','no'),(93,3,'P3X32372',23,24,24,'no','no'),(94,3,'P3X82450',24,20,20,'no','no'),(95,3,'P3X82584',25,24,24,'no','no'),(96,3,'P3X32661',26,16,16,'no','no'),(97,3,'P3X32774',27,20,20,'no','no'),(98,3,'P3X72814',28,16,16,'no','no'),(99,3,'P3X22955',29,16,16,'no','no'),(100,3,'P3X83054',30,24,24,'no','no'),(101,4,'P4X8119',1,20,20,'no','no'),(102,4,'P4X3246',2,16,16,'no','no'),(103,4,'P4X8385',3,16,16,'no','no'),(104,4,'P4X8428',4,20,20,'no','no'),(105,4,'P4X6559',5,20,20,'no','no'),(106,4,'P4X8663',6,16,16,'no','no'),(107,4,'P4X9730',7,24,24,'no','no'),(108,4,'P4X4846',8,20,20,'no','no'),(109,4,'P4X9923',9,20,20,'no','no'),(110,4,'P4X71078',10,16,16,'no','no'),(111,4,'P4X81121',11,16,16,'no','no'),(112,4,'P4X41287',12,16,16,'no','no'),(113,4,'P4X61365',13,24,24,'no','no'),(114,4,'P4X31410',14,24,24,'no','no'),(115,4,'P4X71531',15,16,16,'no','no'),(116,4,'P4X81653',16,16,16,'no','no'),(117,4,'P4X41756',17,20,20,'no','no'),(118,4,'P4X81866',18,16,16,'no','no'),(119,4,'P4X41972',19,16,16,'no','no'),(120,4,'P4X22048',20,20,20,'no','no'),(121,4,'P4X72170',21,16,16,'no','no'),(122,4,'P4X82286',22,16,16,'no','no'),(123,4,'P4X92336',23,20,20,'no','no'),(124,4,'P4X22452',24,24,24,'no','no'),(125,4,'P4X92558',25,16,16,'no','no'),(126,4,'P4X82631',26,20,20,'no','no'),(127,4,'P4X52717',27,24,24,'no','no'),(128,4,'P4X32810',28,16,16,'no','no'),(129,4,'P4X42913',29,16,16,'no','no'),(130,4,'P4X83067',30,20,20,'no','no');
/*!40000 ALTER TABLE `systems` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:35:41
