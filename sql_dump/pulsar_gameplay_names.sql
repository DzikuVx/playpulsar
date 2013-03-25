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
-- Table structure for table `names`
--

DROP TABLE IF EXISTS `names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `names` (
  `ID__` int(11) NOT NULL AUTO_INCREMENT,
  `Type` enum('first','last') DEFAULT 'last',
  `Name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`ID__`),
  UNIQUE KEY `unx` (`Type`,`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `names`
--

LOCK TABLES `names` WRITE;
/*!40000 ALTER TABLE `names` DISABLE KEYS */;
INSERT INTO `names` VALUES (1,'first','Angus'),(2,'first','Ann'),(3,'first','Bo'),(4,'first','Chris'),(5,'first','Daniel'),(6,'first','Daniela'),(7,'first','David'),(8,'first','Ed'),(9,'first','Georg'),(10,'first','Harry'),(11,'first','Honor'),(12,'first','James'),(13,'first','Jane'),(14,'first','Jean'),(15,'first','Jeanette'),(16,'first','Jil'),(17,'first','John'),(18,'first','Keira'),(19,'first','Kiki'),(20,'first','Luc'),(21,'first','Mark'),(22,'first','Martha'),(23,'first','Olivier'),(24,'first','Pete'),(25,'first','Peter'),(26,'first','Rose'),(27,'first','Samuel'),(28,'first','Sara'),(29,'first','Tania'),(30,'first','Trish'),(31,'first','Xi'),(32,'last','Abbot'),(33,'last','Adkins'),(34,'last','Agnew'),(35,'last','Alcock'),(36,'last','Aleksandrovitsh'),(37,'last','Ali'),(38,'last','Alsop'),(39,'last','Arblaster'),(40,'last','Armour'),(41,'last','Azgadi'),(42,'last','Azis'),(43,'last','Bauer'),(44,'last','Blank'),(45,'last','Bloch'),(46,'last','Bowles'),(47,'last','Bush'),(48,'last','Button'),(49,'last','Cage'),(50,'last','Carter'),(51,'last','Clinton'),(52,'last','Coltrain'),(53,'last','Corbett'),(54,'last','Daggett'),(55,'last','Dalton'),(56,'last','Daniels'),(57,'last','Dauby'),(58,'last','Dawson'),(59,'last','Decker'),(60,'last','Desjani'),(61,'last','Dexter'),(62,'last','Dillon'),(63,'last','Dodge'),(64,'last','Dorr'),(65,'last','Douglas'),(66,'last','Geary'),(67,'last','Hammond'),(68,'last','Harrington'),(69,'last','Hawk'),(70,'last','Jackson'),(71,'last','Kane'),(72,'last','Kiki'),(73,'last','Kowalsky'),(74,'last','Maigny'),(75,'last','Malone'),(76,'last','Manamath'),(77,'last','March'),(78,'last','Massenger'),(79,'last','McBain'),(80,'last','McDougal'),(81,'last','McIntosh'),(82,'last','McKenna'),(83,'last','McNab'),(84,'last','Medcaf'),(85,'last','Milne'),(86,'last','Miriamson'),(87,'last','Monk'),(88,'last','Morse'),(89,'last','Myers'),(90,'last','Neil'),(91,'last','Onigura'),(92,'last','Orlovsky'),(93,'last','Palmer'),(94,'last','Parker'),(95,'last','Parnell'),(96,'last','Peabody'),(97,'last','Peyton'),(98,'last','Picard'),(99,'last','Pilcher'),(100,'last','Ping'),(101,'last','Porcher'),(102,'last','Pye'),(103,'last','Ramzej'),(104,'last','Red'),(105,'last','Riker'),(106,'last','Rudin'),(107,'last','Schroeder'),(108,'last','Sharif'),(109,'last','Skinner'),(110,'last','Smith'),(111,'last','Tabor'),(112,'last','Taylor'),(113,'last','Tennant'),(114,'last','Thoms'),(115,'last','Tibbits'),(116,'last','Tolman'),(117,'last','Tripp'),(118,'last','Tuk'),(119,'last','Tulev'),(120,'last','Tupper'),(121,'last','Tyson'),(122,'last','Wakeman'),(123,'last','Waller'),(124,'last','Walton'),(125,'last','Warwick'),(126,'last','Watkins'),(127,'last','Weber'),(128,'last','Weld'),(129,'last','Westcott'),(130,'last','Whalley'),(131,'last','Whitman'),(132,'last','Wilcox'),(133,'last','Wiler'),(134,'last','Winship'),(135,'last','Woodruff');
/*!40000 ALTER TABLE `names` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-02  8:44:41
