-- MySQL dump 10.14  Distrib 5.5.31-MariaDB, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: invoicer
-- ------------------------------------------------------
-- Server version	5.5.31-MariaDB-1~wheezy-log

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
-- Table structure for table `consecutivos`
--

DROP TABLE IF EXISTS `consecutivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consecutivos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `detalle` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `prefijo` char(7) COLLATE utf8_unicode_ci NOT NULL,
  `resolucion` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_resolucion` date NOT NULL,
  `numero_inicial` int(10) unsigned NOT NULL,
  `numero_final` int(10) unsigned NOT NULL,
  `numero_actual` int(10) unsigned NOT NULL,
  `nota_factura` text COLLATE utf8_unicode_ci,
  `nota_ica` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `consecutivos_id` int(10) unsigned NOT NULL,
  `prefijo` char(7) COLLATE utf8_unicode_ci NOT NULL,
  `numero` int(10) unsigned NOT NULL,
  `resolucion` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_resolucion` date NOT NULL,
  `numero_inicial` int(10) unsigned NOT NULL,
  `numero_final` int(10) unsigned NOT NULL,
  `nit` char(18) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `direccion` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nit_entregar` char(18) COLLATE utf8_unicode_ci NOT NULL,
  `nombre_entregar` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `direccion_entregar` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `nota_factura` text COLLATE utf8_unicode_ci,
  `nota_ica` text COLLATE utf8_unicode_ci,
  `venta16` decimal(10,2) NOT NULL,
  `venta10` decimal(10,2) NOT NULL,
  `venta0` decimal(10,2) NOT NULL,
  `iva10` decimal(10,2) NOT NULL,
  `iva16` decimal(10,2) NOT NULL,
  `iva0` decimal(10,2) NOT NULL,
  `pagos` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `comprob_inve` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numero_inve` int(10) DEFAULT NULL,
  `comprob_contab` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numero_contab` int(10) DEFAULT NULL,
  `estado` char(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8287 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facturas_detalle`
--

DROP TABLE IF EXISTS `facturas_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturas_detalle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facturas_id` int(10) unsigned NOT NULL,
  `item` char(12) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `cantidad` int(10) unsigned NOT NULL,
  `descuento` int(4) NOT NULL,
  `valor` decimal(16,2) NOT NULL,
  `iva` decimal(16,2) NOT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29189 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facturas_pagos`
--

DROP TABLE IF EXISTS `facturas_pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturas_pagos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facturas_id` int(10) unsigned NOT NULL,
  `forma_pago` int(10) unsigned NOT NULL,
  `descripcion` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `valor` decimal(16,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `facturas_id` (`facturas_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `financiacion`
--

DROP TABLE IF EXISTS `financiacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financiacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factura_id` int(11) NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `valor` decimal(12,3) NOT NULL DEFAULT '0.000',
  `mora` decimal(12,3) NOT NULL DEFAULT '0.000',
  `total` decimal(12,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3889 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lista_precios`
--

DROP TABLE IF EXISTS `lista_precios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lista_precios` (
  `nit` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `contrato` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `referencia` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `precio_venta` decimal(15,3) NOT NULL DEFAULT '0.000',
  `estado` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  PRIMARY KEY (`nit`,`contrato`,`referencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `terceros`
--

DROP TABLE IF EXISTS `terceros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `terceros` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `documento` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `apellidos` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `nombres` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `telefono` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `direccion` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` char(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-16 16:02:28
