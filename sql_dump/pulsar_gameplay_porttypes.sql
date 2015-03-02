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
-- Table structure for table `porttypes`
--

DROP TABLE IF EXISTS `porttypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `porttypes` (
  `PortTypeID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `Weapons` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Equipment` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Ships` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Items` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `NamePL` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `NameEN` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Type` enum('port','station') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'port',
  `Image` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT 'gfx/ports/station1.jpg',
  `SpecialBuy` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `SpecialSell` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `NoSell` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `NoBuy` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`PortTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `porttypes`
--

LOCK TABLES `porttypes` WRITE;
/*!40000 ALTER TABLE `porttypes` DISABLE KEYS */;
INSERT INTO `porttypes` VALUES (1,'1','25,26','1','1,2,3,4,5,6','Stacja Testowa','Test Station','station','gfx/ports/station1.jpg','','','',''),(2,'','','','','Kolonia','Colony','port','gfx/ports/station1.jpg','','','5,8,9','5,8,9'),(3,'','','','','Zbuntowana Kolonia','Rouge Colony','port','gfx/ports/station1.jpg','5,8','5,8','',''),(4,'','','','','Port Planetarny','Planetary Port','port','gfx/ports/station1.jpg','','','',''),(5,'','','','','Port Księżycowy','Moon Port','port','gfx/ports/station1.jpg','','','',''),(6,'','','','','Stacja Górnicza','Mining Station','port','gfx/ports/station1.jpg','','','',''),(7,'1','1','1','1','Stacja badawcza','Research Station','station','gfx/ports/station1.jpg','','','',''),(10,'','','','','Port handlowy','Trading Outpost','port','gfx/ports/station1.jpg','','','',''),(11,'1,5','1,8','1,4','1,2,3,4,5,6','Sarin A','Sarin A','station','gfx/ports/station1.jpg','','','',''),(12,'1,6','16,13','1,4','1,2,3,4,5,6','Sarin B','Sarin B','station','gfx/ports/station1.jpg','','','',''),(13,'1,11','16,14','1','1,2,3,4,5,6','Regulus A','Regulus A','station','gfx/ports/station1.jpg','','','',''),(14,'1,6','4,10','1,9','1,2,3,4,5,6','Regulus B','Regulus B','station','gfx/ports/station1.jpg','','','',''),(15,'1,4','9,2','1','1,2,3,4,5,6','Alfa Centauri A','Alfa Centauri A','station','gfx/ports/station1.jpg','','','',''),(16,'1,20','14,4','1,5','1,2,3,4,5,6','Alfa Centauri B','Alfa Centauri B','station','gfx/ports/station1.jpg','','','',''),(17,'1,6','4,7,24','1,12','1,2,3,4,5,6','Treversi Gamma A','Treversi Gamma A','station','gfx/ports/station1.jpg','','','',''),(18,'1,22','3,8','1','1,2,3,4,5,6','Treversi Gamma B','Treversi Gamma B','station','gfx/ports/station1.jpg','','','',''),(19,'1,6','15,5','1,4','1,2,3,4,5,6','Sol A','Sol A','station','gfx/ports/station1.jpg','','','',''),(20,'1,10','13,11','1,2','1,2,3,4,5,6','Sol B','Sol B','station','gfx/ports/station1.jpg','','','',''),(21,'1,7','3,15','1,5','1,2,3,4,5,6','Tau Ceti A','Tau Ceti A','station','gfx/ports/station1.jpg','','','',''),(22,'1,6','1,11','1','1,2,3,4,5,6','Tau Ceti B','Tau Ceti B','station','gfx/ports/station1.jpg','','','',''),(23,'1,5','15,13','1','1,2,3,4,5,6','Barnard A','Barnard A','station','gfx/ports/station1.jpg','','','',''),(24,'1,4','9,14','1,6','1,2,3,4,5,6','Barnard B','Barnard B','station','gfx/ports/station1.jpg','','','',''),(25,'1,8','10,1','1,2','1,2,3,4,5,6','Facece A','Facece A','station','gfx/ports/station1.jpg','','','',''),(26,'1,11','10,16','1,12','1,2,3,4,5,6','Facece B','Facece B','station','gfx/ports/station1.jpg','','','',''),(27,'1,18','5,14','1','1,2,3,4,5,6','Esshocan A','Esshocan A','station','gfx/ports/station1.jpg','','','',''),(28,'1,6','7,6','1,4','1,2,3,4,5,6','Esshocan B','Esshocan B','station','gfx/ports/station1.jpg','','','',''),(29,'1,6,16,14','9,2,24','1,7','1,2,3,4,5,6','Exhoed A','Exhoed A','station','gfx/ports/station1.jpg','','','',''),(31,'1,4,21','12,9','1,8,13','1,2,3,4,5,6','Alioth A','Alioth A','station','gfx/ports/station1.jpg','','','',''),(32,'1,9,14','7,4','1,10','1,2,3,4,5,6','Alioth B','Alioth B','station','gfx/ports/station1.jpg','','','',''),(33,'1,20','1,4','1,9','1,2,3,4,5,6','Altair A','Altair A','station','gfx/ports/station1.jpg','','','',''),(34,'1,8','12,13','1','1,2,3,4,5,6','Altair B','Altair B','station','gfx/ports/station1.jpg','','','',''),(35,'1,6','2,5','1','1,2,3,4,5,6','Arcturus A','Arcturus A','station','gfx/ports/station1.jpg','','','',''),(36,'1,16','2,1,24','1','1,2,3,4,5,6','Arcturus B','Arcturus B','station','gfx/ports/station1.jpg','','','',''),(37,'1,5,17','8,13','1','1,2,3,4,5,6','Betelgeuse A','Betelgeuse A','station','gfx/ports/station1.jpg','','','',''),(38,'1,7','13,14','1','1,2,3,4,5,6','Betelgeuse B','Betelgeuse B','station','gfx/ports/station1.jpg','','','',''),(39,'1,22,18','1,6','1','1,2,3,4,5,6','Delta Pavonis A','Delta Pavonis A','station','gfx/ports/station1.jpg','','','',''),(40,'1,6,17','9,7','1','1,2,3,4,5,6','Delta Pavonis B','Delta Pavonis B','station','gfx/ports/station1.jpg','','','',''),(41,'1,12','1,9','1,5','1,2,3,4,5,6','Ross 128 A','Ross 128 A','station','gfx/ports/station1.jpg','','','',''),(42,'1,7','16,7','1','1,2,3,4,5,6','Ross 128 B','Ross 128 B','station','gfx/ports/station1.jpg','','','',''),(43,'1,4','7,9','1','1,2,3,4,5,6','Epsilon Eridani A','Epsilon Eridani A','station','gfx/ports/station1.jpg','','','',''),(44,'1,5','8,9','1,7','1,2,3,4,5,6','Epsilon Eridani B','Epsilon Eridani B','station','gfx/ports/station1.jpg','','','',''),(45,'1,6','11,12','1,9','1,2,3,4,5,6','Ross 154 A','Ross 154 A','station','gfx/ports/station1.jpg','','','',''),(47,'1,20','15,14','1','1,2,3,4,5,6','Fomalhaut A','Fomalhaut A','station','gfx/ports/station1.jpg','','','',''),(48,'1,7','5,14','1,4','1,2,3,4,5,6','Fomalhaut B','Fomalhaut B','station','gfx/ports/station1.jpg','','','',''),(49,'1,13','10,12','1,12','1,2,3,4,5,6','Vega A','Vega A','station','gfx/ports/station1.jpg','','','',''),(50,'1,19','7,2,24','1','1,2,3,4,5,6','Vega B','Vega B','station','gfx/ports/station1.jpg','','','',''),(51,'1,11','8,3','1,5','1,2,3,4,5,6','Vequess A','Vequess A','station','gfx/ports/station1.jpg','','','',''),(52,'1,10','11,4','1','1,2,3,4,5,6','Vequess B','Vequess B','station','gfx/ports/station1.jpg','','','',''),(53,'1,6','12,10','1','1,2,3,4,5,6','Inedol A','Inedol A','station','gfx/ports/station1.jpg','','','',''),(54,'1,22','2,7','1','1,2,3,4,5,6','Inedol B','Inedol B','station','gfx/ports/station1.jpg','','','',''),(55,'1,15,13','6,2','1','1,2,3,4,5,6','Ioarqu A','Ioarqu A','station','gfx/ports/station1.jpg','','','',''),(56,'1,4,12','5,7','1','1,2,3,4,5,6','Ioarqu B','Ioarqu B','station','gfx/ports/station1.jpg','','','',''),(57,'1,6','10,1','1','1,2,3,4,5,6','Laedgre A','Laedgre A','station','gfx/ports/station1.jpg','','','',''),(58,'1,11','7,5','1','1,2,3,4,5,6','Laedgre B','Laedgre B','station','gfx/ports/station1.jpg','','','',''),(59,'1,4,9','6,16','1','1,2,3,4,5,6','Liabeze A','Liabeze A','station','gfx/ports/station1.jpg','','','',''),(61,'1','2,14','1','1,2,3,4,5,6','Luyten 97-12 A','Luyten 97-12 A','station','gfx/ports/station1.jpg','','','',''),(62,'1,6','11,2','1','1,2,3,4,5,6','Luyten 97-12 B','Luyten 97-12 B','station','gfx/ports/station1.jpg','','','',''),(63,'1,4','13,2','1','1,2,3,4,5,6','Micanex A','Micanex A','station','gfx/ports/station1.jpg','','','',''),(65,'1,5,21','13,7','1,13','1,2,3,4,5,6','Ross 986 A','Ross 986 A','station','gfx/ports/station1.jpg','','','',''),(66,'1,7','3,6','1,9','1,2,3,4,5,6','Ross 986 B','Ross 986 B','station','gfx/ports/station1.jpg','','','',''),(67,'1,6,19','5,16','1,8','1,2,3,4,5,6','Sohoa A','Sohoa A','station','gfx/ports/station1.jpg','','','',''),(68,'1,8,20','7,8','1,4','1,2,3,4,5,6','Sohoa B','Sohoa B','station','gfx/ports/station1.jpg','','','',''),(69,'1,18','2,6,24','1,12','1,2,3,4,5,6','Veexio A','Veexio A','station','gfx/ports/station1.jpg','','','',''),(70,'1,6','4,14','1','1,2,3,4,5,6','Veexio B','Veexio B','station','gfx/ports/station1.jpg','','','',''),(71,'1,17','11,12','1','1,2,3,4,5,6','Zeceeth A','Zeceeth A','station','gfx/ports/station1.jpg','','','',''),(72,'1','15,4','1','1,2,3,4,5,6','Zeceeth B','Zeceeth B','station','gfx/ports/station1.jpg','','','',''),(73,'1,15','16,10','1,5','1,2,3,4,5,6','Enengre A','Enengre A','station','gfx/ports/station1.jpg','','','',''),(74,'1,22','2,1','1','1,2,3,4,5,6','Enengre B','Enengre B','station','gfx/ports/station1.jpg','','','',''),(75,'1,6','9,3','1,4,2','1,2,3,4,5,6','Laceti A','Laceti A','station','gfx/ports/station1.jpg','','','',''),(76,'1,12','2,1','1,10,6','1,2,3,4,5,6','Laceti B','Laceti B','station','gfx/ports/station1.jpg','','','',''),(77,'1,9','4,15','1','1,2,3,4,5,6','Bemiio A','Bemiio A','station','gfx/ports/station1.jpg','','','',''),(78,'1','9,11','1','1,2,3,4,5,6','Bemiio B','Bemiio B','station','gfx/ports/station1.jpg','','','',''),(79,'1','10,16','1','1,2,3,4,5,6','Canedand A','Canedand A','station','gfx/ports/station1.jpg','','','',''),(80,'1,6','12,11','1','1,2,3,4,5,6','Canedand B','Canedand B','station','gfx/ports/station1.jpg','','','',''),(81,'1,11','15,5','1','1,2,3,4,5,6','Hovea A','Hovea A','station','gfx/ports/station1.jpg','','','',''),(82,'1','8,12','1','1,2,3,4,5,6','Hovea B','Hovea B','station','gfx/ports/station1.jpg','','','',''),(83,'1','15,9','1','1,2,3,4,5,6','Waav A','Waav A','station','gfx/ports/station1.jpg','','','',''),(85,'1,6','15,8','1','1,2,3,4,5,6','Miurar A','Miurar A','station','gfx/ports/station1.jpg','','','',''),(86,'1,16','2,5','1','1,2,3,4,5,6','Miurar B','Miurar B','station','gfx/ports/station1.jpg','','','',''),(87,'1','10,13','1','1,2,3,4,5,6','Hoquso A','Hoquso A','station','gfx/ports/station1.jpg','','','',''),(88,'1,10','5,8','1','1,2,3,4,5,6','Hoquso B','Hoquso B','station','gfx/ports/station1.jpg','','','',''),(89,'1','12,6','1','1,2,3,4,5,6','Omricon Beta A','Omricon Beta A','station','gfx/ports/station1.jpg','','','',''),(90,'1','12,14','1','1,2,3,4,5,6','Omricon Beta B','Omricon Beta B','station','gfx/ports/station1.jpg','','','',''),(91,'','','','','Posterunek wojskowy','Military outpost','port','gfx/ports/station1.jpg','','','',''),(92,'','','','','Porzucony port','Abandoned port','port','gfx/ports/station1.jpg','','','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15');
/*!40000 ALTER TABLE `porttypes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:35:53
