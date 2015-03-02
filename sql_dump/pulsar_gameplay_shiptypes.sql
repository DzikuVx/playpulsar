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
-- Table structure for table `shiptypes`
--

DROP TABLE IF EXISTS `shiptypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shiptypes` (
  `ShipID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `UserBuyable` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `NamePL` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `NameEN` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Price` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Fame` int(10) unsigned NOT NULL DEFAULT '0',
  `Size` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `Targetting` tinyint(4) NOT NULL DEFAULT '0',
  `Weapons` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `WeaponSize` enum('1','2','3','4') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `Cargo` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `Space` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Speed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Maneuver` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Shield` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Armor` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ArmorStrength` tinyint(4) NOT NULL DEFAULT '10',
  `ArmorPiercing` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Power` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ShieldRegeneration` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ArmorRegeneration` smallint(5) unsigned NOT NULL DEFAULT '0',
  `PowerRegeneration` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ShieldRepair` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ArmorRepair` smallint(5) unsigned NOT NULL DEFAULT '0',
  `PowerRepair` smallint(5) unsigned NOT NULL DEFAULT '0',
  `Scan` tinyint(4) NOT NULL DEFAULT '0',
  `Cloak` tinyint(4) NOT NULL DEFAULT '0',
  `Gather` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `Emp` smallint(5) unsigned NOT NULL DEFAULT '1000',
  `CanWarpJump` tinyint(4) NOT NULL DEFAULT '0',
  `CanActiveScan` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ShipID`),
  KEY `Name` (`NamePL`),
  KEY `NameEN` (`NameEN`),
  KEY `UserBuyable` (`UserBuyable`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shiptypes`
--

LOCK TABLES `shiptypes` WRITE;
/*!40000 ALTER TABLE `shiptypes` DISABLE KEYS */;
INSERT INTO `shiptypes` VALUES (1,'yes','Clipper','Clipper',2000,1,'1',0,2,'1',10,5,44,100,150,100,10,0,100,2,1,2,0,0,0,0,0,5,1000,0,0),(2,'yes','Korweta','Corvette',250000,10,'1',0,8,'2',5,10,68,350,300,120,10,0,200,5,1,5,0,0,0,10,0,0,1000,0,0),(3,'no','Leviatan','Leviatan',0,0,'3',0,4,'4',0,0,20,50,0,10000,25,0,10000,5,1,5,0,0,0,0,25,0,1000,0,0),(4,'yes','Bark','Barque',80000,10,'3',0,2,'2',110,10,36,20,200,150,10,0,100,2,1,1,0,0,0,0,15,5,1000,0,0),(5,'yes','Resourcer','Resourcer',80000,10,'2',0,2,'1',60,10,36,50,200,150,10,0,100,2,1,1,0,0,0,0,0,20,1000,0,0),(6,'yes','Krążownik','Cruiser',1500000,40,'2',1,10,'2',10,14,44,160,500,250,40,0,260,6,2,8,0,0,0,50,0,0,1000,0,0),(7,'yes','Niszczyciel','Destroyer',1500000,40,'2',0,10,'2',10,14,52,300,400,190,30,0,210,6,2,6,0,0,0,25,10,0,1000,0,0),(8,'yes','Pancernik','Battleship',5000000,100,'3',0,12,'3',5,12,28,20,450,700,60,0,500,5,8,10,0,0,0,0,0,0,1000,0,0),(9,'yes','Explorer','Explorer',80000,10,'1',0,2,'1',10,4,88,560,100,80,5,0,100,2,1,1,0,0,0,120,60,0,1000,0,0),(10,'yes','Battlecruiser','Battlecruiser',5000000,100,'3',1,12,'2',5,14,48,160,800,400,40,0,440,5,2,6,0,0,0,10,0,0,1000,0,0),(11,'no','Battlestar','Battlestar',100000000,0,'3',6,36,'4',25,20,60,100,1800,1000,80,0,800,20,20,20,0,0,0,100,0,10,1000,0,0),(12,'yes','Interceptor','Interceptor',250000,10,'1',0,8,'1',5,10,92,450,200,60,8,0,150,4,1,6,0,0,0,25,25,0,1000,0,0),(13,'yes','Fregata','Frigate',1500000,40,'2',3,12,'1',10,12,48,260,450,220,25,2,120,6,2,3,0,0,0,60,0,10,1000,0,0),(14,'no','Lekki krążownik','Light Cruiser',1000000,0,'2',1,8,'2',10,14,44,175,450,200,30,0,260,6,2,6,0,0,0,0,10,0,1000,0,0),(15,'no','Ciężki krążownik','Heavy Cruiser',2500000,0,'2',1,10,'2',10,14,44,150,600,275,40,0,260,6,2,8,0,0,0,50,0,0,1000,0,0),(16,'no','Zwiadowca','Scout',180000,0,'1',0,4,'1',10,4,88,560,100,80,5,0,100,4,1,1,0,0,0,120,60,0,1000,0,0),(17,'no','Krążownik Ray\'Thu','Ray\'Thu Cruiser',1500000,0,'2',3,10,'2',10,14,44,160,450,300,10,1,280,7,2,8,0,0,0,50,0,0,1000,0,0),(18,'no','Niszczyciel Ray\'Thu','Ray\'The Destroyer',1500000,0,'2',2,9,'2',10,14,52,300,350,240,30,0,240,7,2,6,0,0,0,25,10,0,1000,0,0),(19,'no','Ray\'Thu Battlecruiser','Ray\'Thu Battlecruiser',5000000,0,'3',3,12,'2',0,14,48,160,700,500,40,0,490,6,2,6,0,0,0,10,0,0,1000,0,0),(20,'no','Pancernik Ray\'Thu','Ray\'Thu Battleship',5000000,0,'3',1,12,'4',0,12,28,20,350,800,10,2,600,6,8,10,0,0,0,0,0,0,1000,0,0),(21,'no','Korweta Ray\'Thu','Ray\'Thu Corvette',250000,0,'1',2,8,'2',5,10,68,350,250,170,10,0,220,6,1,5,0,0,0,10,0,0,1000,0,0),(22,'no','Ray\'Thu Overlord','Ray\'Thu Overlord',100000000,0,'3',6,36,'4',0,20,36,100,1800,1000,80,2,800,20,20,20,0,0,0,100,0,0,1000,0,0),(23,'no','Młody Leviatan','Young Leviatan',0,0,'3',0,2,'4',0,0,20,50,0,5000,25,0,5000,5,1,5,0,0,0,0,35,0,1000,0,0),(25,'no','Transporter Opancerzony','Armored Transport',250000,10,'3',3,8,'2',90,8,36,20,400,250,20,0,200,1,1,2,0,0,0,0,0,0,1000,0,0);
/*!40000 ALTER TABLE `shiptypes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:35:31
