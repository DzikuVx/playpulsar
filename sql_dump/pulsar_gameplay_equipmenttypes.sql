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
-- Table structure for table `equipmenttypes`
--

DROP TABLE IF EXISTS `equipmenttypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipmenttypes` (
  `EquipmentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `Unique` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `NamePL` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `NameEN` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Size` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `Price` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Fame` int(10) unsigned NOT NULL DEFAULT '0',
  `Type` enum('upgrade','equipment') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'upgrade',
  `Targetting` tinyint(4) NOT NULL DEFAULT '0',
  `Shield` smallint(6) NOT NULL DEFAULT '0',
  `Armor` smallint(6) NOT NULL DEFAULT '0',
  `ArmorStrength` tinyint(4) NOT NULL DEFAULT '0',
  `ArmorPiercing` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Power` smallint(6) NOT NULL DEFAULT '0',
  `Cargo` smallint(6) NOT NULL DEFAULT '0',
  `Weapons` tinyint(4) NOT NULL DEFAULT '0',
  `Space` tinyint(4) NOT NULL DEFAULT '0',
  `Speed` tinyint(4) NOT NULL DEFAULT '0',
  `Maneuver` tinyint(4) NOT NULL DEFAULT '0',
  `ShieldRegeneration` smallint(6) NOT NULL DEFAULT '0',
  `ArmorRegeneration` smallint(6) NOT NULL DEFAULT '0',
  `PowerRegeneration` smallint(6) NOT NULL DEFAULT '0',
  `ShieldRepair` smallint(6) NOT NULL DEFAULT '0',
  `ArmorRepair` smallint(6) NOT NULL DEFAULT '0',
  `PowerRepair` smallint(6) NOT NULL DEFAULT '0',
  `Scan` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Cloak` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Gather` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Emp` smallint(5) unsigned NOT NULL DEFAULT '0',
  `CanRepairWeapons` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `CanRepairEquipment` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `CanActiveScan` tinyint(4) NOT NULL DEFAULT '0',
  `CanWarpJump` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`EquipmentID`),
  KEY `Active` (`Active`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipmenttypes`
--

LOCK TABLES `equipmenttypes` WRITE;
/*!40000 ALTER TABLE `equipmenttypes` DISABLE KEYS */;
INSERT INTO `equipmenttypes` VALUES (1,'yes','no','Moduł Ładowni','Cargo Module','1',2000,0,'upgrade',0,0,0,0,0,0,10,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(2,'yes','no','Panceż','Armor Plating','1',15000,1,'upgrade',0,0,100,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(3,'yes','no','Generator osłon','Shield Generator','1',40000,1,'upgrade',0,100,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(4,'yes','no','Kondensator','Power capacitor','1',9000,1,'upgrade',0,0,0,0,0,100,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(5,'yes','no','Dodatkowy silnik','Engine upgrade','1',25000,0,'upgrade',0,0,0,0,0,0,0,0,0,2,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(6,'yes','no','Skaner','Scanner','1',40000,5,'equipment',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,50,0,0,0,'no','no',0,0),(7,'yes','no','Moduł maskujący','Cloak Module','1',90000,5,'equipment',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,50,0,0,'no','no',0,0),(8,'yes','no','Czujniki celownicze','Targetting Array','1',28000,1,'upgrade',1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(9,'yes','no','Silniki manewrowe','Thrusters','1',38000,1,'upgrade',0,0,0,0,0,0,0,0,0,0,100,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(10,'yes','no','Armor Piercing System','Armor Piercing System','1',30000,1,'upgrade',0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(11,'yes','no','Osłony regeneracyjne','Regenerative Shielding','1',45000,2,'upgrade',0,75,0,0,0,0,0,0,0,0,0,5,0,0,0,0,0,0,0,0,0,'no','no',0,0),(12,'yes','no','Panceż organiczny','Organic Armor','1',25000,2,'upgrade',0,0,75,0,0,0,0,0,0,0,0,0,3,0,0,0,0,0,0,0,0,'no','no',0,0),(13,'yes','no','Generator energii','Power Generator','1',24000,1,'upgrade',0,0,0,0,0,50,0,0,0,0,0,0,0,10,0,0,0,0,0,0,0,'no','no',0,0),(14,'yes','yes','Moduł regeneracji osłon','Shield Regeneration Module','1',90000,1,'equipment',0,0,0,0,0,0,0,0,0,0,0,20,0,0,0,0,0,0,0,0,0,'no','no',0,0),(15,'yes','yes','Moduł regeneracji energii','Power Regeneration Module','1',90000,1,'equipment',0,0,0,0,0,0,0,0,0,0,0,0,0,20,0,0,0,0,0,0,0,'no','no',0,0),(16,'yes','no','Moduł napędowy','Propulsion Upgrade','1',32000,1,'upgrade',0,0,0,0,0,0,0,0,0,1,50,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(17,'yes','no','Panceż ablacyjny','Ablative Armor','1',30000,1,'upgrade',0,0,105,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(18,'yes','no','Osłony wojskowe','Military Shields','1',60000,1,'upgrade',0,105,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(19,'yes','no','Osłony Ray\'Thu','Ray\'Thu Shield','1',90000,1,'upgrade',0,90,0,0,0,0,0,0,0,0,0,2,0,0,0,0,0,0,0,0,0,'no','no',0,0),(20,'yes','no','Panceż Ray\'Thu','Ray\'Thu Armor','1',45000,1,'upgrade',0,0,90,0,0,0,0,0,0,0,0,0,2,0,0,0,0,0,0,0,0,'no','no',0,0),(21,'yes','no','Generator Energii Ray\'Thu','Ray\'Thu Power Generator','1',18000,1,'upgrade',0,0,0,0,0,90,0,0,0,0,0,0,0,2,0,0,0,0,0,0,0,'no','no',0,0),(22,'yes','no','Skaner Ray\'Thu','Ray\'Thu Scanner','1',80000,1,'upgrade',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,55,0,0,0,'no','no',0,0),(23,'yes','no','Moduł maskujący Ray\'Thu','Ray\'Thu Cloak Module','1',180000,1,'upgrade',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,55,0,0,'no','no',0,0),(24,'yes','no','Wytrzymałość pancerza','Armor Strength','1',22000,1,'upgrade',0,0,0,8,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,0),(25,'yes','yes','Napęd Nadświetlny','FTL Jump Drive','1',250000,10,'equipment',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',0,1),(26,'yes','yes','Skaner Aktywny','Active Scanner','1',195000,10,'equipment',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'no','no',1,0);
/*!40000 ALTER TABLE `equipmenttypes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:36:07
