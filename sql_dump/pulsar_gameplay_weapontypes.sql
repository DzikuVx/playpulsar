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
-- Table structure for table `weapontypes`
--

DROP TABLE IF EXISTS `weapontypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weapontypes` (
  `WeaponID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `WeaponClassID` tinyint(3) unsigned DEFAULT NULL,
  `Active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `NamePL` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `NameEN` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Symbol` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Price` bigint(20) unsigned NOT NULL DEFAULT '0',
  `Fame` int(10) unsigned NOT NULL DEFAULT '0',
  `Size` enum('1','2','3','4') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `Accuracy` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ShieldMin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ShieldMax` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ArmorMin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ArmorMax` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `PowerMin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `PowerMax` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `EmpMin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `EmpMax` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Ammo` tinyint(3) unsigned DEFAULT NULL,
  `PowerUsage` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `ReloadTime` enum('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5',
  `CriticalProbability` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `CriticalMultiplier` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `PortWeapon` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `PortPriority` enum('1','2','3','4','5','6') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`WeaponID`),
  UNIQUE KEY `Main` (`Active`,`PortWeapon`,`WeaponID`),
  KEY `Active` (`Active`),
  KEY `PortWeapon` (`PortWeapon`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weapontypes`
--

LOCK TABLES `weapontypes` WRITE;
/*!40000 ALTER TABLE `weapontypes` DISABLE KEYS */;
INSERT INTO `weapontypes` VALUES (1,1,'yes','Mały Laser','Small Laser','SmallLaser',5000,0,'1',96,20,30,20,30,0,0,0,0,NULL,5,'5',10,2,'no',NULL),(2,8,'yes','Cyklotron','Cyclotron','Cyclotron',0,1,'3',25,5,30,10,30,0,0,0,0,NULL,0,'5',10,2,'yes','1'),(3,8,'yes','Miotacz Protonów','Photon Blaster','PhBls',0,1,'3',70,35,60,0,0,0,0,0,0,NULL,0,'4',5,2,'yes','2'),(4,1,'yes','Laser Górniczy','Mining Laser','MinLas',1000,1,'1',10,50,100,50,100,0,0,0,0,NULL,15,'5',10,4,'no',NULL),(5,2,'yes','Działo Plazmowe','Plasma Cannon','PlCan',7000,1,'1',60,35,48,38,43,0,0,0,0,NULL,7,'5',15,2,'no',NULL),(6,1,'yes','Średni Laser','Medium Laser','MLas',12500,0,'2',86,25,40,25,40,0,0,0,0,NULL,10,'5',8,2,'no',NULL),(7,1,'yes','Duży Laser','Large Laser','LLas',25000,1,'3',85,30,47,30,45,0,0,0,0,NULL,20,'5',12,2,'no',NULL),(8,2,'yes','Doładowane Działo Plazmowe','Charged Plasma Cannon','CplCan',32000,2,'2',65,40,53,40,45,0,0,0,0,NULL,10,'5',15,2,'no',NULL),(9,3,'yes','Haubica 85mm','85mm Howitzer','How85',12000,1,'2',45,33,48,40,55,0,0,0,0,19,1,'5',20,3,'no',NULL),(10,3,'yes','Haubica 120mm','120mm Howitzer','How120',30000,1,'3',45,38,65,41,65,0,0,0,0,15,1,'5',20,3,'no',NULL),(11,4,'yes','Seeker Missile','Seeker Missile','SeeMi',8000,1,'1',90,25,38,20,38,0,0,0,0,20,1,'5',10,2,'no',NULL),(12,4,'yes','Impact Missile','Impact Missile','ImpMis',6500,1,'1',65,20,25,50,68,0,0,0,0,18,1,'5',5,5,'no',NULL),(13,5,'yes','Fusion Topedo','Fusion Topedo','FusTorp',14000,1,'2',45,25,75,25,75,3,5,0,2,4,1,'5',40,2,'no',NULL),(14,5,'yes','Cluster Torpedo','Cluster Torpedo','CluTorp',17000,1,'2',95,5,45,5,45,0,0,0,0,8,1,'5',35,2,'no',NULL),(15,6,'yes','Phase Disruptor','Phase Disruptor','PhDis',22000,1,'2',75,43,70,0,0,5,13,3,10,NULL,10,'5',15,4,'no',NULL),(16,6,'yes','Phase Discharge Missile','Phase Discharge Missile','PhMis',7000,2,'1',70,50,75,3,8,8,18,0,0,20,1,'5',20,2,'no',NULL),(17,7,'yes','EMP Cannon','EMP Cannon','EmpCan',34000,1,'2',65,43,60,0,0,1,5,25,90,NULL,7,'5',15,3,'no',NULL),(18,7,'yes','EMP Missile','EMP Missile','EmpMis',9000,1,'1',75,35,60,0,0,0,0,30,86,22,1,'5',15,2,'no',NULL),(19,8,'yes','Generator Osobliwości','Singularity Generator','SinGen',25000,1,'2',100,5,25,0,0,25,45,5,30,NULL,6,'5',10,4,'no',NULL),(20,3,'yes','60mm Rail Cannon','60mm Rail Cannon','Rail60',6500,1,'1',65,28,38,35,45,0,0,0,0,36,6,'5',10,2,'no',NULL),(21,3,'yes','105mm Rail Cannon','105mm Rail Cannon','Rail105',10000,2,'2',70,36,48,38,50,0,0,0,0,28,8,'5',10,2,'no',NULL),(22,8,'yes','Gauss Spike Cannon','Gauss Spike Cannon','Gauss',30000,1,'2',80,3,10,48,68,0,0,0,0,NULL,11,'5',12,4,'no',NULL),(23,3,'yes','125mm Rail Gun','125mm Rail Gun','Rail125',55000,1,'2',70,38,58,42,58,0,0,0,0,32,10,'5',7,2,'no',NULL),(24,2,'yes','Podwójne działo plazmowe','Twin Plasma Gun','TwinPl',55000,1,'2',65,45,55,53,63,0,0,0,0,NULL,12,'5',9,2,'no',NULL),(25,8,'yes','Zarodniki','Spores','Sp',55000,1,'4',30,50,100,50,100,0,0,0,0,NULL,1,'5',10,2,'no',NULL),(26,1,'yes','Laser Wojskowy','Military Laser','MilL',32000,1,'2',90,25,40,25,40,0,0,0,0,NULL,10,'5',8,2,'no',NULL),(27,4,'yes','Rakieta Cień','Shadow Missile','ShMis',16000,1,'1',85,30,40,30,40,0,0,0,0,18,1,'5',8,2,'no',NULL),(28,3,'yes','Kartaczownica','Mitrailleuse','Mitr',16000,1,'2',90,5,45,5,45,0,0,0,0,16,2,'5',25,2,'no',NULL),(29,3,'yes','70mm Rail Cannon','70mm Rail Cannon','Rail70',10000,1,'1',65,30,38,38,45,0,0,0,0,32,7,'5',10,2,'no',NULL),(30,6,'yes','Z-Phase Disruptor','Z-Phase Disruptor','ZPhase',44000,1,'2',75,48,70,0,0,8,13,3,10,NULL,15,'5',15,4,'no',NULL),(31,8,'yes','Jad','Venom','Venom',55000,1,'4',20,0,0,75,125,0,0,0,0,NULL,1,'5',10,4,'no',NULL),(32,7,'yes','Charged EMP Cannon','Charged EMP Cannon','ChEmpC',40000,1,'2',65,45,60,0,0,1,5,28,90,NULL,10,'5',15,3,'no',NULL),(33,8,'yes','Generator Pola Zerowego','Null Field Generator','Null',100000,1,'4',60,75,125,75,125,0,0,0,0,NULL,20,'5',5,2,'no',NULL),(34,8,'yes','Ray\'Thu Blaster','Ray\'Thu Blaster','RTB',40000,1,'2',70,30,43,30,43,1,5,1,10,NULL,10,'5',10,2,'no',NULL),(35,8,'yes','Ray\'Thu Penetrator','Ray\'Thu Penetrator','RTP',40000,1,'2',75,5,8,48,73,1,5,0,0,NULL,10,'5',12,4,'no',NULL),(36,8,'yes','Ray\'Thu Death Ray','Ray\'Thu Death Ray','RTDR',100000,1,'4',60,50,75,50,75,10,20,10,40,NULL,20,'5',10,2,'no',NULL),(37,8,'yes','Ray\'Thu Pacifier','Ray\'Thu Pacifier','RTPa',40000,1,'2',65,45,60,0,0,5,10,33,90,NULL,10,'5',20,2,'no',NULL),(38,8,'yes','Ray\'Thu Morgenstern','Ray\'Thu Morgenstern','RTM',40000,1,'2',75,48,68,0,0,5,13,8,20,NULL,10,'5',10,4,'no',NULL);
/*!40000 ALTER TABLE `weapontypes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:36:11
