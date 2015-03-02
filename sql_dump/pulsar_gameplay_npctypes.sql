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
-- Table structure for table `npctypes`
--

DROP TABLE IF EXISTS `npctypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `npctypes` (
  `NPCTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `AutoPopulate` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `Moveable` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `MoveTimeMin` smallint(5) unsigned NOT NULL DEFAULT '0',
  `MoveTimeMax` smallint(5) unsigned NOT NULL DEFAULT '0',
  `MoveCountMin` smallint(5) unsigned NOT NULL DEFAULT '0',
  `MoveCountMax` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Dock` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `PortTrade` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `MapTrade` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `Trade` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ShipID` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `Systems` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `UniverseNumber` smallint(5) unsigned NOT NULL DEFAULT '1',
  `Equipment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Weapons` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Behavior` enum('protect','aggresive','neutral','defensive','random','protect_own') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'neutral',
  `BehaviorRadius` tinyint(3) unsigned NOT NULL DEFAULT '4',
  `AllianceID` int(20) unsigned DEFAULT NULL,
  `RandomName` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `Cash` bigint(20) NOT NULL DEFAULT '0',
  `Experience` bigint(20) NOT NULL DEFAULT '0',
  `Level` tinyint(4) NOT NULL DEFAULT '1',
  `HaveItems` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `HaveCargo` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  PRIMARY KEY (`NPCTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `npctypes`
--

LOCK TABLES `npctypes` WRITE;
/*!40000 ALTER TABLE `npctypes` DISABLE KEYS */;
INSERT INTO `npctypes` VALUES (3,'Leviatan','yes','yes',200,400,1,2,'no','no','no','',3,'all',20,'','25,25,31,31','neutral',4,NULL,'no',125000,1200000,50,'yes','no'),(4,'GPF Battlecruiser','yes','yes',15,30,1,2,'no','no','yes','',10,'1,2,3,4',8,'8,8,8,17,17,18,18,18,18,18,18,18,10,10','15,15,15,21,21,21,21,21,21,21,22,22','neutral',4,4,'yes',115000,5500000,46,'no','no'),(5,'GPF Cruiser','yes','yes',10,20,1,2,'no','no','yes','',6,'1,2,3,4',18,'2,2,18,18,18,18,18,18,7,8,8,8,8,10','11,11,16,16,8,8,8,8,8,8','protect',4,4,'yes',85000,3500000,34,'yes','no'),(6,'GPF Corvette','yes','yes',5,15,2,4,'yes','no','yes','',2,'1,2,3,4',30,'9,9,9,8,8,8,11,10,10,3','11,11,11,11,11,11,11,11','protect',4,4,'yes',42500,3500000,17,'no','no'),(7,'GPF Interceptor','yes','yes',3,9,1,1,'yes','no','no','',12,'1,2,3,4',35,'9,9,9,8,8,8,15,11,7,10','18,18,18,18,18,12,12,12','protect',4,4,'yes',35000,3500000,14,'no','no'),(8,'OT Trader','yes','yes',15,30,3,6,'yes','yes','yes','',4,'1,2,3,4,5,6,7,8,9,16,10,11,12,13,14,15,17',30,'3,3,8,8,3,3,3,3,11,11','17,17','defensive',4,7,'yes',47500,2000000,19,'yes','yes'),(9,'CSN Battlestar','yes','yes',50,90,1,1,'yes','no','no','',11,'1,2,3,4,5,6,7,8,9',9,'18,18,18,18,18,18,18,8,8,8,18,18,10,10,10,17,17,17,18,18','30,30,30,30,30,30,32,32,32,32,24,24,24,24,24,24,24,24,19,19,22,22,23,23,23,23,23,23,10,10,10,23,23,23,23,23','neutral',4,8,'yes',137500,60000000,55,'yes','no'),(10,'CSN Battlecruiser','yes','yes',35,60,1,2,'no','no','yes','',10,'1,2,3,4,5,6,7,8,9',9,'8,8,8,17,17,18,18,18,18,18,18,18,10,10','30,30,30,30,24,24,23,23,23,23,33,19','neutral',4,8,'yes',117500,45500000,47,'yes','no'),(11,'CSN Cruiser','yes','yes',25,55,1,2,'no','no','no','',6,'1,2,3,4,5,6,7,8,9',48,'12,12,18,18,18,18,18,18,18,7,8,8,8,8','11,11,11,11,24,24,24,24,19,19','protect_own',4,8,'yes',87500,35000000,35,'yes','no'),(12,'OT Clipper','yes','yes',12,25,3,12,'yes','yes','yes','',1,'1,2,3,4,5,6,7,8,9,16,10,11,12,13,14,15,17',40,'3,3,3,3,3','18,18','defensive',4,7,'yes',20000,1200000,8,'yes','yes'),(13,'CSN Destroyer','yes','yes',25,55,1,2,'no','no','no','',7,'1,2,3,4,5,6,7,8,9',60,'10,10,18,18,18,18,18,18,18,10,8,8,8,8','11,11,11,11,8,8,8,8,21,21','protect_own',4,8,'yes',47500,22000000,19,'yes','no'),(14,'CSN Frigate','yes','yes',25,55,1,2,'no','no','no','',13,'1,2,3,4,5,6,7,8,9',55,'10,10,10,18,18,18,18,10,8,8,8,8','27,27,16,16,16,16,16,12,12,12,12,12','neutral',4,8,'yes',65000,27000000,26,'yes','no'),(15,'CSN Heavy Cruiser','yes','yes',25,55,1,2,'no','no','no','',15,'1,2,3,4,5,6,7,8,9',36,'12,12,18,18,18,18,18,18,18,10,8,8,8,8','11,11,11,11,24,24,24,24,19,19','neutral',4,8,'yes',97500,38000000,39,'yes','no'),(16,'CSN Explorer','yes','yes',3,9,1,1,'no','no','no','',9,'1,2,3,4,5,6,7,8,9,16,10,11,12,13,14,15,17,24,25,26,27,31,35,39,40',20,'11,7,11,18','12,12','defensive',4,4,'yes',70000,3500000,28,'no','no'),(17,'Young Leviatan','yes','yes',200,400,1,2,'no','no','no','',23,'all',30,'','25,31','defensive',4,NULL,'no',62500,800000,25,'yes','no'),(18,'Ray\'Thu Overlord','yes','yes',50,90,1,1,'no','no','no','',22,'24,25,26,27,31,35,39,40',8,'19,19,19,19,19,20,20,20,10,10,10,21,21,22,23,20,20,20,19,19','38,38,38,38,38,38,38,38,38,37,37,37,37,37,37,37,37,37,34,34,34,34,34,34,34,35,35,35,35,35,35,35,35,36,36,36','neutral',4,9,'no',137500,67000000,55,'yes','yes'),(19,'Ray\'Thu Corvette','yes','yes',5,15,2,4,'no','no','yes','',21,'24,25,26,27,31,35,39,40',20,'9,9,9,8,8,19,19,10,10,19','34,34,34,34,34,34,34,34','protect_own',4,9,'no',40000,3750000,16,'no','no'),(20,'Ray\'Thu Battleship','yes','yes',35,60,1,2,'no','no','no','',20,'24,25,26,27,31,35,39,40',12,'8,8,8,20,20,20,19,19,19,20,10,10','38,38,38,38,35,35,35,35,35,35,37,36','neutral',4,9,'no',107500,42500000,43,'yes','no'),(21,'Ray\'Thu Battlecruiser','yes','yes',20,40,1,2,'no','no','yes','',19,'24,25,26,27,31,35,39,40',18,'8,8,8,20,20,19,19,19,19,19,19,19,10,10','38,38,38,34,34,34,34,34,34,34,35,35','neutral',4,9,'no',117500,5200000,47,'yes','no'),(22,'Ray\'Thu Destroyer','yes','yes',25,55,1,4,'no','no','no','',18,'24,25,26,27,31,35,39,40',23,'10,10,8,19,19,19,19,19,19,10,8,8,8,8','35,35,34,34,34,34,34,37,37','protect_own',4,9,'no',50000,25000000,20,'yes','no'),(23,'Ray\'Thu Cruiser','yes','yes',25,55,1,2,'no','no','no','',17,'24,25,26,27,31,35,39,40',20,'20,20,19,19,19,19,19,19,19,23,8,8,8,8','38,38,34,34,34,34,35,35,35,35','neutral',4,9,'no',82500,38000000,33,'yes','no'),(24,'Laceti Battlecruiser','yes','yes',35,60,1,2,'no','no','yes','',10,'33',1,'8,8,8,17,17,3,3,3,3,11,11,3,10,10','15,15,15,15,28,28,28,28,28,21,21,21','neutral',4,17,'yes',112500,55500000,45,'yes','no'),(25,'Laceti Cruiser','yes','yes',15,30,1,2,'no','no','yes','',6,'33',4,'3,3,3,3,3,3,3,3,2,8,8,8,8,10','16,16,16,16,28,28,28,26,26,26','neutral',4,17,'yes',87500,4500000,35,'yes','no'),(26,'Laceti Destroyer','yes','yes',25,55,1,2,'no','no','no','',7,'33',10,'10,10,3,3,3,3,3,3,3,10,8,8,8,3','11,11,28,28,28,29,29,29,29,29','protect_own',4,17,'yes',50000,28600000,20,'yes','no'),(27,'Laceti Frigate','yes','yes',25,55,1,2,'no','no','no','',13,'33',10,'10,10,10,3,3,3,3,8,8,8,8,8','16,16,16,16,16,27,27,27,12,12,12,12','protect_own',4,17,'yes',67500,32000000,27,'yes','no'),(28,'Laceti Destroyer HightTarg','yes','yes',25,55,1,2,'no','no','no','',7,'33',10,'10,10,3,3,3,3,3,3,3,8,8,8,8,8','11,11,28,28,28,29,29,29,29,29','protect_own',4,17,'yes',55000,28600000,22,'yes','no'),(29,'Laceti Cruiser HightTarg','yes','yes',15,30,1,2,'no','no','yes','',6,'33',4,'8,8,8,3,3,3,3,3,2,8,8,8,8,10','16,16,16,16,28,28,28,26,26,26','neutral',4,17,'yes',87500,4500000,35,'yes','no'),(30,'Ray\'Thu Destroyer Protective','yes','yes',25,55,1,4,'no','no','no','',18,'24,25,26,27,31,35,39,40',23,'10,10,8,19,19,19,19,19,19,10,8,8,8,8','35,35,34,34,34,34,34,37,37','protect_own',4,9,'no',50000,25000000,20,'yes','no'),(31,'Ray\'Thu Corvette Protective','yes','yes',5,15,2,4,'no','no','yes','',21,'24,25,26,27,31,35,39,40',20,'9,9,9,8,8,19,19,10,10,19','34,34,34,34,34,34,34,34','protect_own',4,9,'no',40000,3750000,16,'no','no'),(32,'Ray\'Thu Cruiser HighTarg','yes','yes',25,55,1,2,'no','no','no','',17,'24,25,26,27,31,35,39,40',20,'20,20,8,8,19,19,19,19,19,23,8,8,8,8','38,38,34,34,34,34,35,35,35,35','neutral',4,9,'no',82500,38000000,33,'yes','no'),(33,'Laceti Battlecruiser HightTarg','yes','yes',35,60,1,2,'no','no','yes','',10,'33',1,'8,8,8,17,17,3,3,3,3,11,8,8,10,10','15,15,15,15,28,28,28,28,28,21,21,21','neutral',4,17,'yes',112500,55500000,45,'yes','no'),(34,'CSN Battlecruiser HighTarg','yes','yes',35,60,1,2,'no','no','yes','',10,'1,2,3,4,5,6,7,8,9',9,'8,8,8,17,17,18,18,18,18,8,8,8,10,10','30,30,30,30,24,24,23,23,23,23,33,19','neutral',4,8,'yes',117500,45500000,47,'yes','no'),(35,'OT Clipper FightBack','yes','yes',12,25,3,12,'yes','yes','yes','',1,'1,2,3,4,5,6,7,8,9,16,10,11,12,13,14,15,17',50,'3,3,3,8,8','18,18','neutral',4,7,'yes',20000,1200000,10,'yes','yes'),(36,'OT Trader FightBack','yes','yes',15,30,3,6,'yes','yes','yes','',4,'1,2,3,4,5,6,7,8,9,16,10,11,12,13,14,15,17',50,'3,3,8,8,3,3,3,3,11,11','17,17','neutral',4,7,'yes',47500,2000000,21,'yes','yes'),(37,'OT ArmoredTransport','yes','yes',10,20,2,4,'yes','yes','yes','',25,'1,2,3,4,5,6,7,8,9,16,10,11,12,13,14,15,17',45,'8,8,3,3,3,3,11,11','16,16,16,17,17,17,22,22','neutral',4,7,'yes',77500,2000000,25,'yes','yes'),(39,'Pirate Interceptor v1','yes','yes',3,9,1,1,'yes','no','no','',12,'14,15,18,23,30,31,32',38,'9,9,9,8,8,8,3,3,3,10','18,18,18,18,18,29,29,29','aggresive',4,10,'yes',38000,3500000,16,'no','no'),(40,'Pirate Interceptor v2','yes','yes',3,9,1,1,'yes','no','no','',12,'14,15,18,23,30,31,32',37,'3,3,3,8,8,8,3,3,3,10','18,18,18,18,11,11,11,11','aggresive',4,10,'yes',38000,3500000,16,'no','no'),(41,'Pirate Corvette','yes','yes',5,15,2,4,'yes','no','no','',2,'14,15,18,23,30,31,32',45,'3,3,3,8,8,8,11,10,10,3','17,17,17,28,28,28,28,28','aggresive',4,10,'yes',47500,3500000,20,'no','no'),(42,'Pirate Destroyer','yes','yes',15,30,1,2,'yes','no','no','',7,'14,15,18,23,30,31,32',45,'10,10,3,3,3,3,3,3,3,3,8,8,8,8','18,18,18,18,18,28,28,28,21,21','aggresive',4,10,'yes',52500,22000000,22,'yes','no'),(43,'Laceti ArmoredTransport','yes','yes',10,20,2,4,'yes','yes','yes','',25,'33',6,'8,8,3,3,3,3,11,11','16,16,16,16,16,22,22,22','neutral',4,17,'yes',67500,1400000,22,'yes','yes'),(44,'Laceti Trader','yes','yes',15,30,3,6,'yes','yes','yes','',4,'33',6,'3,3,8,8,3,3,3,3,11,11','17,17','neutral',4,17,'yes',52500,2000000,18,'yes','yes'),(45,'Laceti Clipper','yes','yes',12,25,3,12,'yes','yes','yes','',1,'33',8,'3,3,3,3,3','18,18','defensive',4,17,'yes',22500,1200000,10,'yes','yes'),(46,'OT Escort Interceptor','yes','yes',3,9,1,1,'yes','no','no','',12,'1,2,3,4,5,6,7,8,9,16,10,11,12,13,14,15,17',50,'3,3,3,8,8,8,15,3,3,10','18,18,18,18,18,12,12,12','protect_own',4,7,'yes',35000,3500000,18,'no','no'),(47,'OT Escort Corvette','yes','yes',5,15,2,4,'yes','no','yes','',2,'1,2,3,4,5,6,7,8,9,16,10,11,12,13,14,15,17',38,'3,3,3,8,8,8,11,10,10,3','16,16,16,16,16,12,12,12','protect_own',4,7,'yes',42500,3500000,21,'no','no'),(48,'TG Trader','yes','yes',15,30,3,6,'yes','yes','yes','',4,'18,19,20,21,22,23,28,29,30,32,33,34,36,37,38',100,'3,3,3,3,3,3,3,3,11,11','17,17','defensive',4,29,'yes',49000,2000000,23,'yes','yes'),(49,'TG Clipper','yes','yes',12,25,3,12,'yes','yes','yes','',1,'18,19,20,21,22,23,28,29,30,32,33,34,36,37,38',125,'3,3,3,3,3','18,18','defensive',4,29,'yes',12500,1200000,14,'yes','yes'),(50,'TG Escort Corvette','yes','yes',5,20,2,4,'no','no','yes','',2,'18,19,20,21,22,23,28,29,30,32,33,34,36,37,38',100,'3,3,3,8,8,8,11,10,3,3','16,16,16,16,16,12,12,12','protect_own',4,29,'yes',42500,3500000,23,'no','no'),(51,'TG Escort Interceptor','yes','yes',3,9,1,1,'no','no','no','',12,'18,19,20,21,22,23,28,29,30,32,33,34,36,37,38',100,'3,3,3,8,8,8,15,3,3,10','18,18,18,18,18,12,12,12','protect_own',4,29,'yes',36000,3500000,21,'no','no'),(52,'TG Escort Frigate','yes','yes',10,55,1,2,'no','no','no','',13,'18,19,20,21,22,23,28,29,30,32,33,34,36,37,38',50,'10,10,10,3,3,3,3,8,8,8,8,8','16,16,16,16,16,16,16,16,12,12,12,12','protect_own',4,29,'yes',67500,32000000,28,'yes','no'),(53,'TG Escort Cruiser','yes','yes',10,55,1,2,'no','no','no','',6,'18,19,20,21,22,23,28,29,30,32,33,34,36,37,38',48,'12,12,3,3,3,3,3,3,3,3,8,8,8,8','15,15,28,28,28,28,28,28,28,28','protect_own',4,29,'yes',87500,35000000,37,'yes','no'),(54,'ORR Battlecruiser','yes','yes',15,30,1,2,'no','no','yes','',10,'13,14,15,17,18,19,20,21,22',10,'8,8,8,3,3,3,3,3,3,3,3,3,10,10','30,30,30,30,30,30,28,28,28,23,23,23','protect',4,5,'yes',105000,5500000,43,'yes','no'),(55,'ORR Destroyer','yes','yes',25,55,1,2,'no','no','no','',7,'13,14,15,17,18,19,20,21,22',20,'3,3,3,3,3,3,3,3,3,10,8,8,8,3','16,16,16,28,28,28,28,28,22,22','protect',4,5,'yes',50000,28600000,24,'yes','no'),(56,'ORR Interceptor','yes','yes',3,9,1,1,'no','no','no','',12,'13,14,15,17,18,19,20,21,22',30,'9,9,9,8,8,8,3,3,3,9','16,16,16,16,16,12,12,12','protect',4,5,'yes',35000,3500000,26,'no','no'),(57,'ORR Light Cruiser','yes','yes',25,55,1,2,'no','no','no','',14,'13,14,15,17,18,19,20,21,22',15,'12,12,3,3,3,3,3,3,3,10,8,8,3,3','16,16,28,28,28,28,28,28','protect',4,5,'yes',97500,38000000,32,'yes','no');
/*!40000 ALTER TABLE `npctypes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:36:02
