-- MySQL dump 10.13  Distrib 5.1.36, for suse-linux-gnu (i686)
--
-- Host: localhost    Database: hfos_workspace
-- ------------------------------------------------------
-- Server version	5.1.36

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
-- Table structure for table `page_checks`
--

DROP TABLE IF EXISTS `page_checks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_checks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(10) unsigned NOT NULL,
  `name` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuarios_id` (`usuarios_id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_checks`
--

LOCK TABLES `page_checks` WRITE;
/*!40000 ALTER TABLE `page_checks` DISABLE KEYS */;
INSERT INTO `page_checks` VALUES (7,1,'welcome','2011-04-01 00:34:13'),(8,3,'welcome','2011-04-01 01:18:27'),(9,4,'welcome','2011-04-22 14:39:59'),(10,2,'welcome','2013-08-20 16:14:56'),(11,32,'welcome','2013-08-22 08:11:56');
/*!40000 ALTER TABLE `page_checks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_session`
--

DROP TABLE IF EXISTS `user_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(10) unsigned NOT NULL,
  `app_code` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `token` char(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `ping_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuarios_id` (`usuarios_id`,`app_code`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_session`
--

LOCK TABLES `user_session` WRITE;
/*!40000 ALTER TABLE `user_session` DISABLE KEYS */;
INSERT INTO `user_session` VALUES (1,4,'CO','2845e4fc91f6580e34787ef605eae970','hibernate',1303514420),(2,1,'IN','2990b0e3e1bd46eb65d5c3ec050ee8ae','hibernate',1377037895),(3,4,'IN','2845e4fc91f6580e34787ef605eae970','hibernate',1303514296),(4,0,'CO',NULL,'hibernate',1379373764),(5,1,'CO','cf9aa9af44e3c81664bcb291a5390430','active',1378419698),(6,1,'IM','cf9aa9af44e3c81664bcb291a5390430','sleep',1378419698),(7,0,'IN',NULL,'hibernate',1379187703),(8,2,'IM','c023a7855b176f60fcb7bfbb45b779d0','hibernate',1377128300),(9,2,'CO','c023a7855b176f60fcb7bfbb45b779d0','hibernate',1377128300),(10,32,'CO','c023a7855b176f60fcb7bfbb45b779d0','sleep',1379426791),(11,32,'IM','46fd79618c0b5634fe92985a5e6f9db3','sleep',1378573093),(12,32,'IN','c023a7855b176f60fcb7bfbb45b779d0','sleep',1379083052),(13,0,'IM',NULL,'hibernate',1377813123);
/*!40000 ALTER TABLE `user_session` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-09-17 15:48:34
