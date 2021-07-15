update empresa set version = '6.1.6';

alter table criterios add prefijo char(3) after id;

alter table comprob drop key `l_comprob`;
alter table comprob add primary key(codigo);

CREATE TABLE `consolidados` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `server` varchar(32) NOT NULL,
  `instance` varchar(64) NOT NULL,
  `estado` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `notmov` (
  `comprob` char(3) NOT NULL,
  `numero` int(10) unsigned NOT NULL,
  `nota` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `regimen_cuentas` (
  `regimen` char(1) NOT NULL,
  `cta_iva10d` char(12) DEFAULT NULL,
  `cta_iva16d` char(12) DEFAULT NULL,
  `cta_iva10r` char(12) DEFAULT NULL,
  `cta_iva16r` char(12) DEFAULT NULL,
  `cta_iva10v` char(12) DEFAULT NULL,
  `cta_iva16v` char(12) DEFAULT NULL,
  PRIMARY KEY (`regimen`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

alter table movilin modify cantidad decimal(16,2);
alter table movilin modify cantidad_desp decimal(16,2);
alter table movilin modify cantidad_rec decimal(16,2);

CREATE TABLE `reccaj` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nit` int(11) NOT NULL,
  `nombre` char(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `direccion` char(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ciudad` int(11) DEFAULT NULL,
  `telefono` char(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `comprob` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numero` int(11) NOT NULL DEFAULT '1',
  `codusu` int(11) DEFAULT NULL,
  `observaciones` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `rc` int(11) NOT NULL,
  `valor` decimal(13,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `codcaj` (`comprob`),
  KEY `codusu` (`numero`),
  KEY `codven` (`codusu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `detalle_reccaj` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `reccaj_id` int(11) NOT NULL,
  `forma_pago_id` int(10) NOT NULL,
  `numero` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `valor` decimal(14,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


