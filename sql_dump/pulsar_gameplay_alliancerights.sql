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
-- Table structure for table `alliancerights`
--

DROP TABLE IF EXISTS `alliancerights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliancerights` (
  `UserID` int(11) NOT NULL,
  `AllianceID` int(11) NOT NULL,
  `Module` enum('edit','accept','kick','cash','relations','post','rank') NOT NULL,
  PRIMARY KEY (`UserID`,`AllianceID`,`Module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliancerights`
--

LOCK TABLES `alliancerights` WRITE;
/*!40000 ALTER TABLE `alliancerights` DISABLE KEYS */;
INSERT INTO `alliancerights` VALUES (1,1,'edit'),(1,1,'accept'),(1,1,'kick'),(1,1,'cash'),(1,1,'relations'),(1,1,'post'),(1,1,'rank'),(4,1,'edit'),(4,1,'accept'),(4,1,'kick'),(4,1,'cash'),(4,1,'relations'),(4,1,'post'),(4,1,'rank'),(16248,1,'accept'),(16248,1,'kick'),(16248,1,'cash'),(16248,1,'relations'),(16248,1,'post'),(16248,1,'rank'),(24707,19,'edit'),(24707,19,'accept'),(24707,19,'kick'),(24707,19,'cash'),(24707,19,'relations'),(24707,19,'post'),(24707,19,'rank');
/*!40000 ALTER TABLE `alliancerights` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-28 19:35:33
