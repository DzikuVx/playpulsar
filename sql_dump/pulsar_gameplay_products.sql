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
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `ProductID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `NamePL` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `NameEN` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Symbol` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `PriceMin` smallint(10) unsigned NOT NULL DEFAULT '0',
  `PriceMax` smallint(10) unsigned NOT NULL DEFAULT '0',
  `Experience` tinyint(4) NOT NULL DEFAULT '0',
  `ExpMin` int(10) unsigned NOT NULL DEFAULT '0',
  `ExpMax` int(10) unsigned NOT NULL DEFAULT '0',
  `Size` enum('1','2','3') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `RegularSell` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `RegularBuy` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `CreationDivider` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `Active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`ProductID`),
  KEY `RegularSell` (`RegularSell`),
  KEY `RegularBuy` (`RegularBuy`),
  KEY `Active` (`Active`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Woda','Water','Water',10,15,35,4,18,'1','yes','yes',1,'yes'),(2,'Paliwo','Fuel','Fuel',25,38,16,2,14,'1','yes','yes',1,'yes'),(3,'Jedzenie','Food','Food',15,23,26,2,16,'1','yes','yes',1,'yes'),(4,'Ruda','Ore','Ore',12,18,30,4,22,'1','yes','yes',1,'yes'),(5,'Broń','Firearms','Firearms',210,225,8,2,14,'1','no','no',2,'yes'),(6,'Leki','Medicines','Medicines',180,195,10,2,12,'1','yes','yes',1,'yes'),(7,'Urządzenia','Machines','Machines',140,155,14,2,10,'1','yes','yes',1,'yes'),(8,'Narkotyki','Narcotics','Narcotics',520,580,4,1,1,'1','no','yes',8,'yes'),(9,'Roboty','Robots','Robots',250,270,6,1,2,'1','yes','yes',1,'yes'),(10,'Metale','Metals','Metals',24,36,20,4,20,'1','yes','yes',2,'yes'),(11,'Kryształy','Crystals','Crystals',90,100,10,1,10,'1','no','yes',10,'yes'),(12,'Kosztowności','Luxuries','Luxuries',90,205,2,1,4,'1','no','yes',1,'yes'),(13,'Odpady','Waste','Waste',0,0,50,10,10,'1','yes','no',1,'yes'),(14,'Elektronika','Electronics','Electronics',90,105,12,2,8,'1','yes','yes',1,'yes'),(15,'Chemikalia','Chemicals','Chemicals',20,30,22,4,18,'1','yes','yes',2,'yes');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:35:42
